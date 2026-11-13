/*!
 * GSMaRT website behaviour. No dependencies.
 *
 * Every feature below is progressive enhancement: with JavaScript off the
 * pages, catalog filters (as links and a form) and inquiry form still work.
 *
 *   header()    shadow on the sticky header after scrolling
 *   drawer()    mobile navigation drawer with focus handling
 *   stages()    pointer and scroll driven 3D device scenes
 *   tilt()      layered 3D tilt and cursor light on catalog cards
 *   reveal()    one-time scroll reveal for a few key blocks
 *   wall()      sorts system cards into staggered columns that drift on scroll
 *   catalog()   live search, industry filter, counts, empty state
 *   showcase()  screen tabs on a system detail page
 *   inquiry()   inline validation and submission of the inquiry form
 */
(function () {
  'use strict';

  var doc = document;
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  var finePointer = window.matchMedia('(hover: hover) and (pointer: fine)');

  function $(sel, root) { return (root || doc).querySelector(sel); }
  function $$(sel, root) { return Array.prototype.slice.call((root || doc).querySelectorAll(sel)); }
  function clamp(v, min, max) { return Math.min(max, Math.max(min, v)); }

  /* ---------------------------------------------------------------- header */
  function header() {
    var el = $('[data-header]');
    if (!el) { return; }
    var stuck = false;
    function update() {
      var now = window.scrollY > 8;
      if (now !== stuck) {
        stuck = now;
        el.classList.toggle('is-stuck', stuck);
      }
    }
    window.addEventListener('scroll', update, { passive: true });
    update();
  }

  /* ---------------------------------------------------------------- drawer */
  function drawer() {
    var panel = $('[data-drawer]');
    var opener = $('[data-drawer-open]');
    if (!panel || !opener) { return; }
    var lastFocus = null;

    function focusables() {
      return $$('a[href], button:not([disabled])', panel).filter(function (n) { return n.offsetParent !== null; });
    }
    function open() {
      lastFocus = doc.activeElement;
      panel.hidden = false;
      // Force a layout so the slide-in transition runs.
      panel.getBoundingClientRect();
      panel.classList.add('is-open');
      opener.setAttribute('aria-expanded', 'true');
      doc.body.style.overflow = 'hidden';
      var first = focusables()[0];
      if (first) { first.focus(); }
      doc.addEventListener('keydown', onKey);
    }
    function close() {
      panel.classList.remove('is-open');
      opener.setAttribute('aria-expanded', 'false');
      doc.body.style.overflow = '';
      doc.removeEventListener('keydown', onKey);
      window.setTimeout(function () { panel.hidden = true; }, reduceMotion.matches ? 0 : 260);
      if (lastFocus && lastFocus.focus) { lastFocus.focus(); }
    }
    function onKey(e) {
      if (e.key === 'Escape') { close(); return; }
      if (e.key !== 'Tab') { return; }
      var items = focusables();
      if (!items.length) { return; }
      var first = items[0], last = items[items.length - 1];
      if (e.shiftKey && doc.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && doc.activeElement === last) { e.preventDefault(); first.focus(); }
    }
    opener.addEventListener('click', open);
    $$('[data-drawer-close]', panel).forEach(function (n) { n.addEventListener('click', close); });
    var wide = window.matchMedia('(min-width: 1000px)');
    function onWide(e) { if (e.matches && panel.classList.contains('is-open')) { close(); } }
    if (wide.addEventListener) { wide.addEventListener('change', onWide); } else if (wide.addListener) { wide.addListener(onWide); }
  }

  /* ----------------------------------------------------------- 3D scenes */
  // Each [data-stage] exposes three CSS variables that the stylesheet turns
  // into transforms: --px / --py (pointer, -1..1) and --sp (scroll, 0..1).
  // Rotation is clamped in CSS to a few degrees. Rendering stops when the
  // scene is off screen or the tab is in the background.
  function stages() {
    var els = $$('[data-stage]');
    if (!els.length || reduceMotion.matches) { return; }

    els.forEach(function (stage) {
      var area = stage.closest('section') || stage;
      var target = { x: 0, y: 0, s: 0 };
      var current = { x: 0, y: 0, s: 0 };
      var visible = false;
      var frame = 0;

      function measureScroll() {
        var r = area.getBoundingClientRect();
        target.s = clamp(-r.top / Math.max(r.height, 1), 0, 1);
      }
      function render() {
        frame = 0;
        current.x += (target.x - current.x) * 0.09;
        current.y += (target.y - current.y) * 0.09;
        current.s += (target.s - current.s) * 0.14;
        stage.style.setProperty('--px', current.x.toFixed(4));
        stage.style.setProperty('--py', current.y.toFixed(4));
        stage.style.setProperty('--sp', current.s.toFixed(4));
        var settling = Math.abs(target.x - current.x) + Math.abs(target.y - current.y) + Math.abs(target.s - current.s) > 0.002;
        if (settling) { request(); }
      }
      function request() {
        if (!frame && visible && !doc.hidden) { frame = window.requestAnimationFrame(render); }
      }

      if (finePointer.matches) {
        area.addEventListener('pointermove', function (e) {
          if (e.pointerType && e.pointerType !== 'mouse') { return; }
          var r = area.getBoundingClientRect();
          target.x = clamp(((e.clientX - r.left) / r.width) * 2 - 1, -1, 1);
          target.y = clamp(((e.clientY - r.top) / r.height) * 2 - 1, -1, 1);
          request();
        });
        area.addEventListener('pointerleave', function () {
          target.x = 0; target.y = 0;
          request();
        });
      }
      window.addEventListener('scroll', function () {
        if (!visible) { return; }
        measureScroll();
        request();
      }, { passive: true });

      if ('IntersectionObserver' in window) {
        new IntersectionObserver(function (entries) {
          visible = entries[0].isIntersecting;
          if (visible) { measureScroll(); request(); }
        }, { rootMargin: '80px' }).observe(stage);
      } else {
        visible = true;
      }
      doc.addEventListener('visibilitychange', request);
    });
  }

  /* ------------------------------------------------------------ card tilt */
  function tilt() {
    if (reduceMotion.matches || !finePointer.matches) { return; }
    var MAX = 7;   // degrees
    $$('[data-tilt]').forEach(function (el) {
      var frame = 0, px = 0.5, py = 0.5;
      function apply() {
        frame = 0;
        el.style.setProperty('--ry', ((px - 0.5) * 2 * MAX).toFixed(2) + 'deg');
        el.style.setProperty('--rx', ((0.5 - py) * 2 * MAX * 0.7).toFixed(2) + 'deg');
        el.style.setProperty('--lx', (px * 100).toFixed(1) + '%');
        el.style.setProperty('--ly', (py * 100).toFixed(1) + '%');
      }
      el.addEventListener('pointermove', function (e) {
        if (e.pointerType && e.pointerType !== 'mouse') { return; }
        var r = el.getBoundingClientRect();
        px = clamp((e.clientX - r.left) / r.width, 0, 1);
        py = clamp((e.clientY - r.top) / r.height, 0, 1);
        el.classList.add('is-tilting');
        if (!frame) { frame = window.requestAnimationFrame(apply); }
      });
      el.addEventListener('pointerleave', function () {
        if (frame) { window.cancelAnimationFrame(frame); frame = 0; }
        el.classList.remove('is-tilting');
        el.style.setProperty('--rx', '0deg');
        el.style.setProperty('--ry', '0deg');
      });
    });
  }

  /* --------------------------------------------------------------- reveal */
  function reveal() {
    var els = $$('[data-reveal]');
    if (!els.length) { return; }
    if (reduceMotion.matches || !('IntersectionObserver' in window)) {
      els.forEach(function (n) { n.classList.add('is-in'); });
      return;
    }
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-in');
          io.unobserve(entry.target);
        }
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.05 });
    els.forEach(function (n) { io.observe(n); });
  }

  /* ----------------------------------------------------------------- wall */
  // Staggered wall of system cards (home page and Our Systems).
  //
  // The page arrives with the cards in a plain grid. This sorts them into one,
  // two or three columns depending on the screen width, always adding the next
  // card to the shortest column, and then moves the columns at different speeds
  // as the page scrolls. site.css holds the speeds and the starting offsets.
  function wall() {
    var roots = $$('[data-wall]');
    if (!roots.length) { return; }
    var two = window.matchMedia('(min-width: 680px)');
    var three = window.matchMedia('(min-width: 1080px)');
    // Rough height of each card shape, as a share of the column width.
    var WEIGHT = { wide: 1.3, std: 1.45, square: 1.7, tall: 1.95 };
    var walls = [];

    roots.forEach(function (root) {
      var head = $('[data-wall-head]', root);
      var cells = $$('[data-cell]', root);
      var stash = doc.createElement('div');   // cards hidden by the search or filter wait here
      stash.hidden = true;
      var cols = [];

      function layout() {
        var n = three.matches ? 3 : (two.matches ? 2 : 1);
        var load = n === 3 ? [0, 0.22, 0.08] : (n === 2 ? [0, 0.18] : [0]);   // columns that start lower count as fuller
        while (root.firstChild) { root.removeChild(root.firstChild); }
        cols = [];
        for (var i = 0; i < n; i++) {
          var col = doc.createElement('div');
          col.className = 'wall__col';
          root.appendChild(col);
          cols.push(col);
        }
        root.appendChild(stash);
        if (head) { cols[0].appendChild(head); load[0] += 0.85; }
        cells.forEach(function (cell) {
          if (cell.hidden) { stash.appendChild(cell); return; }
          var k = 0;
          for (var c = 1; c < n; c++) { if (load[c] < load[k] - 0.001) { k = c; } }
          cols[k].appendChild(cell);
          var card = $('[data-card]', cell);
          var shape = card ? (card.className.match(/sys-card--(\w+)/) || [])[1] : 'std';
          load[k] += WEIGHT[shape] || WEIGHT.std;
        });
        root.classList.add('wall--cols');
        root.setAttribute('data-cols', n);
        drift();
      }
      function drift() {
        if (reduceMotion.matches || cols.length < 2) { root.style.removeProperty('--y'); return; }
        var r = root.getBoundingClientRect();
        var vh = window.innerHeight;
        if (r.bottom < -200 || r.top > vh + 200) { return; }           // off screen: leave it alone
        root.style.setProperty('--y', clamp(vh * 0.5 - r.top, -vh * 0.5, r.height).toFixed(1));
      }
      root.wallLayout = layout;      // catalog() calls this after filtering
      walls.push({ layout: layout, drift: drift });
      layout();
    });

    var frame = 0;
    window.addEventListener('scroll', function () {
      if (frame) { return; }
      frame = window.requestAnimationFrame(function () {
        frame = 0;
        walls.forEach(function (w) { w.drift(); });
      });
    }, { passive: true });
    function relayout() { walls.forEach(function (w) { w.layout(); }); }
    [two, three].forEach(function (mq) {
      if (mq.addEventListener) { mq.addEventListener('change', relayout); } else if (mq.addListener) { mq.addListener(relayout); }
    });
  }

  /* -------------------------------------------------------------- catalog */
  // Search, industry filter, result count and empty state on Our Systems.
  function catalog() {
    var root = $('[data-catalog]');
    if (!root) { return; }
    var form = $('[data-catalog-form]', root);
    var input = $('[data-catalog-q]', root);
    var sectorSel = $('[data-catalog-sector]', root);
    var grid = $('[data-catalog-grid]', root);
    var cells = $$('[data-cell]', root).map(function (cell) {
      var card = $('[data-card]', cell);
      return { cell: cell, text: card.getAttribute('data-search') || '', sector: card.getAttribute('data-sector') };
    });
    var count = $('[data-count-n]', root);
    var empty = $('[data-catalog-empty]', root);
    var term = $('[data-catalog-term]', root);
    var resets = $$('[data-catalog-reset]', root);
    var total = cells.length;
    var timer = 0;

    function apply(pushUrl) {
      var q = input.value.trim().toLowerCase();
      var words = q ? q.split(/\s+/) : [];
      var s = sectorSel.value;
      var shown = 0;
      cells.forEach(function (c) {
        var ok = (!s || c.sector === s) && words.every(function (w) { return c.text.indexOf(w) !== -1; });
        c.cell.hidden = !ok;
        if (ok) { shown++; }
      });
      count.textContent = shown + ' of ' + total;
      empty.hidden = shown !== 0;
      grid.hidden = shown === 0;
      if (term) { term.textContent = q ? '“' + input.value.trim() + '”' : 'that filter'; }
      var filtered = q !== '' || s !== '';
      resets.forEach(function (r) { if (!r.closest('[data-catalog-empty]')) { r.hidden = !filtered; } });
      if (grid.wallLayout) { grid.wallLayout(); }       // re-sort the visible cards into columns
      if (pushUrl && window.history && history.replaceState) {
        var params = new URLSearchParams();
        if (q) { params.set('q', input.value.trim()); }
        if (s) { params.set('sector', s); }
        var qs = params.toString();
        history.replaceState(null, '', window.location.pathname + (qs ? '?' + qs : ''));
      }
    }

    input.addEventListener('input', function () {
      window.clearTimeout(timer);
      timer = window.setTimeout(function () { apply(true); }, 90);
    });
    sectorSel.addEventListener('change', function () { apply(true); });
    form.addEventListener('submit', function (e) { e.preventDefault(); apply(true); });
    resets.forEach(function (r) {
      r.addEventListener('click', function (e) {
        e.preventDefault();
        input.value = '';
        sectorSel.value = '';
        apply(true);
        input.focus();
      });
    });
    apply(false);
  }

  /* ------------------------------------------------------------- showcase */
  function showcase() {
    var root = $('[data-showcase]');
    if (!root) { return; }
    var tabs = $$('[role="tab"]', root);
    var panels = $$('[role="tabpanel"]', root);
    function select(i, focus) {
      tabs.forEach(function (t, n) {
        var on = n === i;
        t.setAttribute('aria-selected', on ? 'true' : 'false');
        t.tabIndex = on ? 0 : -1;
        panels[n].hidden = !on;
      });
      if (focus) { tabs[i].focus(); }
    }
    tabs.forEach(function (tab, i) {
      tab.addEventListener('click', function () { select(i, false); });
      tab.addEventListener('keydown', function (e) {
        var next = null;
        if (e.key === 'ArrowRight') { next = (i + 1) % tabs.length; }
        else if (e.key === 'ArrowLeft') { next = (i - 1 + tabs.length) % tabs.length; }
        else if (e.key === 'Home') { next = 0; }
        else if (e.key === 'End') { next = tabs.length - 1; }
        if (next !== null) { e.preventDefault(); select(next, true); }
      });
    });
  }

  /* -------------------------------------------------------------- inquiry */
  function inquiry() {
    $$('[data-inquiry]').forEach(function (form) {
      var status = $('[data-status]', form);
      var button = $('[data-submit]', form);
      var label = $('[data-submit-label]', form);
      var type = form.elements.inquiry_type;
      var sending = false;

      function idleLabel() { return type && type.value !== 'demo' ? 'Send Inquiry' : 'Request a System Demo'; }
      if (type) { type.addEventListener('change', function () { if (!sending) { label.textContent = idleLabel(); } }); }

      function setError(name, message) {
        var field = form.elements[name];
        var box = $('#err-' + name, form);
        if (!field || !box) { return; }
        if (message) {
          box.textContent = message;
          box.hidden = false;
          field.setAttribute('aria-invalid', 'true');
        } else {
          box.textContent = '';
          box.hidden = true;
          field.removeAttribute('aria-invalid');
        }
      }
      function showStatus(message, ok) {
        status.textContent = message;
        status.hidden = !message;
        status.classList.toggle('form-status--ok', !!ok);
        status.classList.toggle('form-status--error', !ok);
      }

      // Same rules as inquiry_validate() in includes/inquiry.php. The server
      // repeats every check, so this only saves the visitor a round trip.
      function validate() {
        var v = function (n) { return (form.elements[n].value || '').trim(); };
        var errors = {};
        if (!v('full_name')) { errors.full_name = 'Enter your full name.'; }
        else if (v('full_name').length < 2) { errors.full_name = 'Your name should be between 2 and 120 characters.'; }
        if (!v('email')) { errors.email = 'Enter your e-mail address so we can reply.'; }
        else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v('email'))) { errors.email = 'Enter an e-mail address in the form name@example.com.'; }
        if (v('phone') && (!/^[0-9+()\-.\s]{7,40}$/.test(v('phone')) || v('phone').replace(/\D/g, '').length < 7)) {
          errors.phone = 'Enter a phone number using digits, spaces and + ( ) - only, or leave it blank.';
        }
        if (!v('message')) { errors.message = 'Tell us what your business needs.'; }
        else if (v('message').length < 10) { errors.message = 'Add a little more detail so we can prepare a useful reply.'; }
        if (!form.elements.consent.checked) { errors.consent = 'Confirm that we may use these details to reply to you.'; }
        return errors;
      }
      function paint(errors) {
        ['full_name', 'organization', 'email', 'phone', 'system', 'inquiry_type', 'message', 'consent'].forEach(function (n) {
          setError(n, errors[n] || '');
        });
        var first = Object.keys(errors).filter(function (k) { return k !== 'form' && form.elements[k]; })[0];
        if (first) { form.elements[first].focus(); }
      }
      function busy(on) {
        sending = on;
        form.classList.toggle('is-sending', on);
        button.setAttribute('aria-disabled', on ? 'true' : 'false');
        label.textContent = on ? 'Sending' : idleLabel();
      }

      form.addEventListener('submit', function (e) {
        if (!window.fetch || !window.FormData) { return; }   // fall back to a normal post
        e.preventDefault();
        if (sending) { return; }

        var errors = validate();
        paint(errors);
        if (Object.keys(errors).length) {
          showStatus('Some details need attention before this can be sent.', false);
          return;
        }
        showStatus('', false);
        busy(true);

        fetch(form.getAttribute('action').split('#')[0], {
          method: 'POST',
          body: new FormData(form),
          headers: { 'Accept': 'application/json' },
          credentials: 'same-origin'
        }).then(function (res) {
          return res.json().then(function (data) { return data; }, function () {
            throw new Error('The server replied in a form this page could not read.');
          });
        }).then(function (data) {
          busy(false);
          if (data.csrf) { form.elements.csrf.value = data.csrf; }
          if (data.shown_at) { form.elements.shown_at.value = data.shown_at; }
          if (data.ok) {
            var done = doc.createElement('div');
            done.className = 'form-success';
            done.setAttribute('role', 'status');
            done.tabIndex = -1;
            var h = doc.createElement('h2');
            h.textContent = 'Inquiry received';
            var p = doc.createElement('p');
            p.textContent = data.message;
            done.appendChild(h);
            done.appendChild(p);
            form.parentNode.replaceChild(done, form);
            done.focus();
            return;
          }
          paint(data.errors || {});
          showStatus(data.message || 'The inquiry could not be sent.', false);
          if (!Object.keys(data.errors || {}).some(function (k) { return k !== 'form'; })) { status.scrollIntoView({ block: 'center' }); }
        }).catch(function () {
          busy(false);
          showStatus('The inquiry did not reach our server, so nothing was sent. Check your connection and send it again.', false);
        });
      });
    });

    var auto = $('[data-autofocus]');
    if (auto) { auto.focus(); }
  }

  function start() {
    header(); drawer(); stages(); tilt(); reveal(); wall(); catalog(); showcase(); inquiry();
  }
  if (doc.readyState === 'loading') { doc.addEventListener('DOMContentLoaded', start); } else { start(); }
})();
