<?php
/**
 * Inquiry and demo request handling.
 *
 * Delivery: every accepted inquiry is saved to the `inquiries` table and can
 * be read in the admin area. If inquiry.notify_email is set in the config, a
 * notification e-mail is sent as well. The visitor is only told the inquiry
 * was received after the database insert has succeeded.
 *
 * Spam controls: CSRF token, a hidden field real visitors never fill, a signed
 * timing token (forms submitted within a few seconds are rejected), length
 * limits and a per-address hourly limit.
 */

function inquiry_types()
{
    return company('inquiry_types', ['demo' => 'Request a system demo', 'other' => 'Something else']);
}

/** Signed timestamp recording when the form was shown. */
function inquiry_time_token()
{
    $ts = (string) time();
    return $ts . '.' . hash_hmac('sha256', $ts, (string) config('app_key', ''));
}

/** Seconds since the form was shown, or null if the token is not ours. */
function inquiry_token_age($token)
{
    if (!is_string($token) || strpos($token, '.') === false) {
        return null;
    }
    list($ts, $sig) = explode('.', $token, 2);
    if (!ctype_digit($ts) || !hash_equals(hash_hmac('sha256', $ts, (string) config('app_key', '')), $sig)) {
        return null;
    }
    return time() - (int) $ts;
}

function inquiry_clean($value, $max)
{
    $value = is_string($value) ? $value : '';
    $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value);
    return mb_substr(trim((string) $value), 0, $max);
}

/**
 * Validate a submission.
 * @return array [array $data, array $errors, int $httpStatus]
 */
function inquiry_validate(array $in)
{
    $errors = [];
    $status = 422;

    $data = [
        'full_name'    => preg_replace('/\s+/u', ' ', inquiry_clean(isset($in['full_name']) ? $in['full_name'] : '', 200)),
        'organization' => preg_replace('/\s+/u', ' ', inquiry_clean(isset($in['organization']) ? $in['organization'] : '', 200)),
        'email'        => inquiry_clean(isset($in['email']) ? $in['email'] : '', 255),
        'phone'        => inquiry_clean(isset($in['phone']) ? $in['phone'] : '', 60),
        'system'       => inquiry_clean(isset($in['system']) ? $in['system'] : '', 120),
        'inquiry_type' => inquiry_clean(isset($in['inquiry_type']) ? $in['inquiry_type'] : '', 40),
        'message'      => inquiry_clean(isset($in['message']) ? $in['message'] : '', 6000),
        'consent'      => !empty($in['consent']),
        'system_id'    => null,
        'system_name'  => null,
    ];

    // Checks that are not about a particular field.
    if (!csrf_valid(isset($in['csrf']) ? $in['csrf'] : null)) {
        $errors['form'] = 'This form expired before it was sent. Check your details and send it again.';
        return [$data, $errors, 419];
    }
    if (!empty($in['website'])) {   // hidden field: only automated senders fill it
        $errors['form'] = 'The inquiry could not be accepted. If you are using a form filler, turn it off and try again.';
        return [$data, $errors, 422];
    }
    $age = inquiry_token_age(isset($in['shown_at']) ? $in['shown_at'] : null);
    if ($age === null || $age > 6 * 3600) {
        $errors['form'] = 'This form expired before it was sent. Check your details and send it again.';
        return [$data, $errors, 419];
    }
    if ($age < (int) config('inquiry.min_seconds', 4)) {
        $errors['form'] = 'That was sent too quickly to be read as a real inquiry. Wait a few seconds and send it again.';
        return [$data, $errors, 429];
    }

    // Field checks.
    $nameLen = mb_strlen($data['full_name']);
    if ($nameLen === 0) {
        $errors['full_name'] = 'Enter your full name.';
    } elseif ($nameLen < 2 || $nameLen > 120) {
        $errors['full_name'] = 'Your name should be between 2 and 120 characters.';
    }
    if (mb_strlen($data['organization']) > 150) {
        $errors['organization'] = 'Keep the company or organization name under 150 characters.';
    }
    if ($data['email'] === '') {
        $errors['email'] = 'Enter your e-mail address so we can reply.';
    } elseif (mb_strlen($data['email']) > 191 || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter an e-mail address in the form name@example.com.';
    }
    if ($data['phone'] !== '') {
        $digits = preg_replace('/\D/', '', $data['phone']);
        if (!preg_match('/^[0-9+()\-.\s]{7,40}$/', $data['phone']) || strlen($digits) < 7) {
            $errors['phone'] = 'Enter a phone number using digits, spaces and + ( ) - only, or leave it blank.';
        }
    }
    if ($data['system'] !== '') {
        $system = catalog_system($data['system']);
        if ($system) {
            $data['system_id']   = (int) $system['id'];
            $data['system_name'] = $system['name'];
        } else {
            $errors['system'] = 'Choose a system from the list, or choose "Not sure yet".';
        }
    }
    if (!array_key_exists($data['inquiry_type'], inquiry_types())) {
        $errors['inquiry_type'] = 'Choose what the inquiry is about.';
    }
    $msgLen = mb_strlen($data['message']);
    if ($msgLen === 0) {
        $errors['message'] = 'Tell us what your business needs.';
    } elseif ($msgLen < 10) {
        $errors['message'] = 'Add a little more detail so we can prepare a useful reply.';
    } elseif ($msgLen > 4000) {
        $errors['message'] = 'Shorten the message to 4,000 characters or fewer.';
    }
    if (!$data['consent']) {
        $errors['consent'] = 'Confirm that we may use these details to reply to you.';
    }

    // Hourly limit per visitor address.
    if (!$errors) {
        $ip = client_ip();
        if ($ip !== '') {
            $row = db_one(
                'SELECT COUNT(*) AS n FROM inquiries WHERE ip_address = ? AND created_at >= ?',
                [$ip, date('Y-m-d H:i:s', time() - 3600)]
            );
            if ($row && (int) $row['n'] >= (int) config('inquiry.max_per_hour', 5)) {
                $errors['form'] = 'Several inquiries were already sent from this connection in the last hour. Try again later.';
                $status = 429;
            }
        }
    }

    return [$data, $errors, $status];
}

/** Save the inquiry. @return int new inquiry id */
function inquiry_store(array $data)
{
    $now = now();
    db_run(
        'INSERT INTO inquiries
            (full_name, organization, email, phone, system_id, system_name, inquiry_type, message,
             consent, status, ip_address, user_agent, created_at, updated_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?, ?, ?, ?)',
        [
            $data['full_name'],
            $data['organization'] !== '' ? $data['organization'] : null,
            $data['email'],
            $data['phone'] !== '' ? $data['phone'] : null,
            $data['system_id'],
            $data['system_name'],
            $data['inquiry_type'],
            $data['message'],
            'new',
            client_ip() !== '' ? client_ip() : null,
            mb_substr(isset($_SERVER['HTTP_USER_AGENT']) ? (string) $_SERVER['HTTP_USER_AGENT'] : '', 0, 255),
            $now,
            $now,
        ]
    );
    return (int) db()->lastInsertId();
}

/** Optional e-mail notification. Returns true when the mail was handed off. */
function inquiry_notify($id, array $data)
{
    $to = trim((string) config('inquiry.notify_email', ''));
    if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    $strip = function ($v) {
        return trim(preg_replace('/[\r\n]+/', ' ', (string) $v));
    };
    $from = trim((string) config('inquiry.from_email', ''));
    if (!filter_var($from, FILTER_VALIDATE_EMAIL)) {
        $host = parse_url(site_origin(), PHP_URL_HOST);
        $from = 'no-reply@' . ($host ?: 'localhost');
    }
    $types   = inquiry_types();
    $subject = sprintf('[%s website] Inquiry #%d: %s', company('brand', 'GSMaRT'), $id, $strip($types[$data['inquiry_type']]));
    $body = "A new inquiry was submitted on the website.\n\n"
        . 'Reference: #' . $id . "\n"
        . 'Name: ' . $strip($data['full_name']) . "\n"
        . 'Organization: ' . ($data['organization'] !== '' ? $strip($data['organization']) : '(not given)') . "\n"
        . 'E-mail: ' . $strip($data['email']) . "\n"
        . 'Phone: ' . ($data['phone'] !== '' ? $strip($data['phone']) : '(not given)') . "\n"
        . 'System: ' . ($data['system_name'] ? $strip($data['system_name']) : '(not sure yet)') . "\n"
        . 'About: ' . $strip($types[$data['inquiry_type']]) . "\n\n"
        . "Message:\n" . $data['message'] . "\n\n"
        . 'Open in admin: ' . absolute_url(base_path() . '/admin/inquiry.php?id=' . $id) . "\n";
    $headers = [
        'From: ' . $from,
        'Reply-To: ' . $strip($data['email']),
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
    ];
    $sent = @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, implode("\r\n", $headers));
    if ($sent) {
        db_run('UPDATE inquiries SET email_sent = 1 WHERE id = ?', [$id]);
    } else {
        error_log('GSMaRT website: inquiry #' . $id . ' saved, but the notification e-mail could not be sent.');
    }
    return (bool) $sent;
}

/**
 * Process a POST to the contact page.
 * @return array ['ok' => bool, 'status' => int, 'message' => string, 'errors' => array, 'data' => array]
 */
function inquiry_handle(array $post)
{
    list($data, $errors, $status) = inquiry_validate($post);
    if ($errors) {
        return [
            'ok' => false, 'status' => $status, 'errors' => $errors, 'data' => $data,
            'message' => isset($errors['form']) ? $errors['form'] : 'Some details need attention before this can be sent.',
        ];
    }
    try {
        $id = inquiry_store($data);
    } catch (Exception $ex) {
        error_log('GSMaRT website: inquiry could not be saved: ' . $ex->getMessage());
        return [
            'ok' => false, 'status' => 500, 'data' => $data,
            'errors' => ['form' => 'Your inquiry could not be saved because of a problem on our side. Nothing was sent. Try again in a few minutes.'],
            'message' => 'Your inquiry could not be saved because of a problem on our side. Nothing was sent. Try again in a few minutes.',
        ];
    }
    inquiry_notify($id, $data);
    return [
        'ok' => true, 'status' => 200, 'errors' => [], 'data' => $data, 'id' => $id,
        'message' => sprintf(
            'Inquiry received. Your reference number is %06d, and our reply will go to %s.',
            $id,
            $data['email']
        ),
    ];
}

/**
 * The inquiry form.
 * $o keys: system (slug), type, data (old values), errors, heading_id, compact
 */
function inquiry_form(array $o = [])
{
    $data    = isset($o['data']) ? $o['data'] : [];
    $errors  = isset($o['errors']) ? $o['errors'] : [];
    $system  = isset($data['system']) ? $data['system'] : (isset($o['system']) ? $o['system'] : '');
    $type    = isset($data['inquiry_type']) ? $data['inquiry_type'] : (isset($o['type']) ? $o['type'] : '');
    $old     = function ($key) use ($data) {
        return isset($data[$key]) ? $data[$key] : '';
    };
    $err = function ($key) use ($errors) {
        return isset($errors[$key])
            ? '<p class="field__error" id="err-' . e($key) . '">' . e($errors[$key]) . '</p>'
            : '<p class="field__error" id="err-' . e($key) . '" hidden></p>';
    };
    $aria = function ($key, $hint = '') use ($errors) {
        $ids = trim(($hint !== '' ? $hint . ' ' : '') . 'err-' . $key);
        return ' aria-describedby="' . e($ids) . '"' . (isset($errors[$key]) ? ' aria-invalid="true"' : '');
    };
    $groups = catalog_by_sector();
    ?>
<form class="inquiry" method="post" action="<?= e(url('contact')) ?>#inquiry" novalidate data-inquiry>
  <?= csrf_field() ?>
  <input type="hidden" name="shown_at" value="<?= e(inquiry_time_token()) ?>">
  <div class="hp-field" aria-hidden="true">
    <label for="f-website">Leave this field empty</label>
    <input type="text" id="f-website" name="website" tabindex="-1" autocomplete="off">
  </div>

  <div class="form-status<?= isset($errors['form']) ? ' form-status--error' : '' ?>" role="alert" data-status<?= isset($errors['form']) || $errors ? '' : ' hidden' ?>><?php
    if (isset($errors['form'])) {
        echo e($errors['form']);
    } elseif ($errors) {
        echo 'Some details need attention before this can be sent.';
    }
  ?></div>

  <div class="form-grid">
    <div class="field">
      <label for="f-name">Full name</label>
      <input type="text" id="f-name" name="full_name" value="<?= e($old('full_name')) ?>" autocomplete="name" maxlength="120" required<?= $aria('full_name') ?>>
      <?= $err('full_name') ?>
    </div>
    <div class="field">
      <label for="f-org">Company or organization <span class="field__opt">optional</span></label>
      <input type="text" id="f-org" name="organization" value="<?= e($old('organization')) ?>" autocomplete="organization" maxlength="150"<?= $aria('organization') ?>>
      <?= $err('organization') ?>
    </div>
    <div class="field">
      <label for="f-email">E-mail</label>
      <input type="email" id="f-email" name="email" value="<?= e($old('email')) ?>" autocomplete="email" maxlength="191" inputmode="email" required<?= $aria('email') ?>>
      <?= $err('email') ?>
    </div>
    <div class="field">
      <label for="f-phone">Phone <span class="field__opt">optional</span></label>
      <input type="tel" id="f-phone" name="phone" value="<?= e($old('phone')) ?>" autocomplete="tel" maxlength="40" inputmode="tel"<?= $aria('phone') ?>>
      <?= $err('phone') ?>
    </div>
    <div class="field">
      <label for="f-system">System</label>
      <select id="f-system" name="system" data-system-select<?= $aria('system') ?>>
        <option value="">Not sure yet</option>
<?php foreach ($groups as $group): ?>
        <optgroup label="<?= e($group['name']) ?>">
<?php foreach ($group['systems'] as $s): ?>
          <option value="<?= e($s['slug']) ?>"<?= $system === $s['slug'] ? ' selected' : '' ?>><?= e($s['name']) ?></option>
<?php endforeach; ?>
        </optgroup>
<?php endforeach; ?>
      </select>
      <?= $err('system') ?>
    </div>
    <div class="field">
      <label for="f-type">What is this about?</label>
      <select id="f-type" name="inquiry_type" required<?= $aria('inquiry_type') ?>>
<?php foreach (inquiry_types() as $key => $label): ?>
        <option value="<?= e($key) ?>"<?= $type === $key ? ' selected' : '' ?>><?= e($label) ?></option>
<?php endforeach; ?>
      </select>
      <?= $err('inquiry_type') ?>
    </div>
    <div class="field field--wide">
      <label for="f-message">Message</label>
      <textarea id="f-message" name="message" rows="6" maxlength="4000" required<?= $aria('message', 'hint-message') ?>><?= e($old('message')) ?></textarea>
      <p class="field__hint" id="hint-message">What the business does, how the work is recorded today, and what you would like to see.</p>
      <?= $err('message') ?>
    </div>
    <div class="field field--wide field--check">
      <input type="checkbox" id="f-consent" name="consent" value="1"<?= !empty($data['consent']) ? ' checked' : '' ?> required<?= $aria('consent') ?>>
      <label for="f-consent">I agree that GSMaRT may use these details to reply to this inquiry, as described in the <a href="<?= e(url('privacy')) ?>">privacy notice</a>.</label>
      <?= $err('consent') ?>
    </div>
  </div>

  <div class="inquiry__foot">
    <button class="btn btn--primary" type="submit" data-submit>
      <span data-submit-label><?= ($type === '' || $type === 'demo') ? 'Request a System Demo' : 'Send Inquiry' ?></span>
    </button>
    <p class="inquiry__note">Your inquiry is saved to our records and read by a person. We do not add you to a mailing list.</p>
  </div>
</form>
<?php
}
