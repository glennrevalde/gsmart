<?php
/**
 * Starter catalog content for the GSMaRT website.
 *
 * This file is only read by tools/build-sql.php, which turns it into the two
 * SQL files in /database. The live site reads its catalog from MySQL, so after
 * the first import you normally edit systems in the admin area instead.
 *
 * Every entry is STARTER CONTENT. Confirm each system and each listed function
 * with GSMaRT before publishing, and unpublish anything that is not offered.
 *
 * Fields per system:
 *   slug      URL part, lowercase letters, numbers and hyphens
 *   name      product title
 *   category  label shown on the card (industry)
 *   sector    one of the sector slugs below (used by the catalog filter)
 *   tagline   one benefit statement for the detail page
 *   summary   one or two sentences for the card
 *   audience  who it is for
 *   manages   what it manages
 *   terms     extra search words (business use cases)
 *   modules   [name, description]  the first four appear on the card
 *   steps     [label, detail]      the operational workflow, in order
 *   roles     [role, what they do]
 *   notes     implementation notes
 */

return [
    'sectors' => [
        ['slug' => 'finance-administration', 'name' => 'Finance and Administration',
         'summary' => 'Books, payroll and consolidated records for the office that keeps the rest of the business accountable.'],
        ['slug' => 'education-utilities', 'name' => 'Education and Utilities',
         'summary' => 'Recurring accounts that are billed on a schedule: students by term, water consumers by reading cycle.'],
        ['slug' => 'retail-supply', 'name' => 'Retail and Supply Chain',
         'summary' => 'Selling floors and stock rooms, from the checkout counter back to the receiving dock.'],
        ['slug' => 'hospitality-food', 'name' => 'Hospitality and Food Service',
         'summary' => 'Rooms, tables and counters where the order has to reach the right person within minutes.'],
        ['slug' => 'services-recreation', 'name' => 'Local Services and Recreation',
         'summary' => 'Walk-in service businesses and bookable facilities that run on job tickets and time slots.'],
        ['slug' => 'logistics-construction', 'name' => 'Logistics and Construction',
         'summary' => 'Work that moves or gets built over days and months, tracked by status, cost and progress.'],
    ],

    'systems' => [

        // ------------------------------------------------------------------
        [
            'slug' => 'accounting-finance',
            'name' => 'Accounting & Finance',
            'category' => 'Finance',
            'sector' => 'finance-administration',
            'tagline' => 'One set of books that the whole office posts to and reports from.',
            'summary' => 'Keeps the general ledger, receivables and payables in one place, so financial reports come from posted entries rather than separate spreadsheets.',
            'audience' => 'Accounting staff, bookkeepers and business owners who need their ledgers, customer balances and supplier balances to agree with each other.',
            'manages' => 'The chart of accounts, journal entries, sales invoices and collections, supplier bills and payments, and the financial statements that are drawn from them.',
            'terms' => 'bookkeeping ledger invoices bills trial balance income statement balance sheet collections disbursements audit',
            'modules' => [
                ['General ledger', 'Chart of accounts, journal entries and posting periods, with each entry traceable to its source document.'],
                ['Receivables', 'Customer invoices, collections and an aging schedule showing what is due and overdue.'],
                ['Payables', 'Supplier bills, payment scheduling and a record of what has been settled.'],
                ['Financial reports', 'Trial balance, income statement and balance sheet for a chosen period.'],
                ['Cash and bank', 'Receipts and disbursements by account, with entries marked as they are reconciled.'],
                ['Audit trail', 'Who recorded, edited or voided an entry, and when.'],
            ],
            'steps' => [
                ['Record the document', 'An invoice, bill, receipt or voucher is entered with its accounts and amounts.'],
                ['Review and post', 'An approver checks the entry before it is posted to the ledger.'],
                ['Collect or pay', 'Collections are applied to invoices and payments to bills, updating each balance.'],
                ['Reconcile', 'Cash and bank entries are matched against statements for the period.'],
                ['Close the period', 'The period is locked so posted figures no longer change.'],
                ['Report', 'Statements and schedules are produced from the posted entries.'],
            ],
            'roles' => [
                ['Bookkeeper', 'Encodes invoices, bills, receipts and journal entries.'],
                ['Accountant', 'Reviews and posts entries, reconciles accounts and closes periods.'],
                ['Owner or manager', 'Reads statements, aging schedules and cash positions.'],
            ],
            'notes' => [
                'Setup starts from your existing chart of accounts and opening balances.',
                'Approval steps and posting periods are agreed during configuration.',
                'Report layouts that your office already uses can be reviewed during the demo.',
            ],
        ],

        // ------------------------------------------------------------------
        [
            'slug' => 'human-resource-payroll',
            'name' => 'Human Resource & Payroll',
            'category' => 'Workforce',
            'sector' => 'finance-administration',
            'tagline' => 'Attendance, leave and pay computed from the same employee record.',
            'summary' => 'Holds employee records, attendance and leave, and computes each payroll run from them. Payslips and payroll registers come out of the same data.',
            'audience' => 'HR officers and payroll staff in organizations where attendance, leave and pay are currently tracked in separate files.',
            'manages' => 'Employee profiles and employment details, daily attendance, leave requests and balances, payroll periods, earnings and deductions, and payslips.',
            'terms' => 'employees staff timekeeping dtr attendance leave payslip salary deductions overtime hris',
            'modules' => [
                ['Employee records', 'Personal and employment details, position, department and pay setup for each employee.'],
                ['Attendance', 'Daily time records with late, undertime, absence and overtime computed per day.'],
                ['Leave management', 'Leave requests, approvals and running balances by leave type.'],
                ['Payroll processing', 'Period-based pay runs with earnings, deductions and net pay per employee.'],
                ['Payslips and registers', 'Individual payslips and a payroll register for each run.'],
            ],
            'steps' => [
                ['Maintain employee records', 'New hires and changes in position or pay are recorded as they happen.'],
                ['Capture attendance', 'Time records are collected for the pay period and exceptions are flagged.'],
                ['Approve leave and overtime', 'Supervisors act on pending requests before the cut-off.'],
                ['Run payroll', 'Earnings and deductions are computed for every employee in the period.'],
                ['Review and finalize', 'Payroll staff check the register, correct exceptions and lock the run.'],
                ['Release payslips', 'Payslips are issued and the run is kept for reference.'],
            ],
            'roles' => [
                ['HR officer', 'Maintains employee records and leave balances.'],
                ['Payroll staff', 'Prepares, reviews and finalizes each payroll run.'],
                ['Supervisor', 'Approves leave and overtime for their team.'],
                ['Employee', 'Files leave and views their own attendance and payslips, where self-service is enabled.'],
            ],
            'notes' => [
                'Pay rules, deduction tables and cut-off dates are configured to your policies.',
                'Attendance can be encoded or imported from your time-recording source.',
                'Statutory computations should be reviewed with your accountant before the first live run.',
            ],
        ],

        // ------------------------------------------------------------------
        [
            'slug' => 'school-management',
            'name' => 'School Management',
            'category' => 'Education',
            'sector' => 'education-utilities',
            'tagline' => 'Enrollment, classes and school fees kept on one student record.',
            'summary' => 'Follows each student from enrollment through class assignment and fee payment. The registrar and the cashier work from the same record.',
            'audience' => 'Registrars, cashiers and administrators of schools that enroll students by term and collect fees on a schedule.',
            'manages' => 'Student information, enrollment by school year and term, sections and class schedules, fee assessments, payments and outstanding balances.',
            'terms' => 'students enrollment registrar tuition fees sections subjects grades cashier assessment academic',
            'modules' => [
                ['Enrollment', 'Admission and enrollment per school year, with year level and section assignment.'],
                ['Student information', 'Profiles, guardians, requirements submitted and enrollment history.'],
                ['Classes and sections', 'Sections, subjects, schedules and class lists.'],
                ['Fees and assessment', 'Fee schedules by level, with an assessment generated for each student.'],
                ['Cashiering', 'Payments against assessments, official receipts and remaining balances.'],
                ['School administration', 'School years, terms, levels and user access.'],
            ],
            'steps' => [
                ['Register the student', 'A new or returning student is recorded with their details and requirements.'],
                ['Enroll for the term', 'The student is placed in a year level and section for the school year.'],
                ['Assess fees', 'The fee schedule for that level produces the amount due.'],
                ['Collect payment', 'The cashier records payments and issues receipts.'],
                ['Produce class lists', 'Sections and schedules are finalized and class lists are released.'],
                ['Monitor balances', 'Unpaid balances are tracked through the term.'],
            ],
            'roles' => [
                ['Registrar', 'Handles admission, enrollment and student records.'],
                ['Cashier', 'Collects fees and issues receipts.'],
                ['Administrator', 'Sets up school years, levels, fees and user access.'],
                ['Teacher', 'Views class lists and schedules for assigned sections.'],
            ],
            'notes' => [
                'Year levels, sections and fee schedules are set up before enrollment opens.',
                'Existing student lists can be brought in at the start so returning students are not re-encoded.',
                'Receipt numbering and layout are configured to the school\'s practice.',
            ],
        ],

        // ------------------------------------------------------------------
        [
            'slug' => 'water-billing-collection',
            'name' => 'Water Billing & Collection',
            'category' => 'Utilities',
            'sector' => 'education-utilities',
            'tagline' => 'From meter reading to collected payment, one account at a time.',
            'summary' => 'Turns meter readings into bills for each billing cycle and records what consumers pay. Unpaid balances carry forward to the next bill.',
            'audience' => 'Water service providers, cooperatives and associations that read meters, issue periodic bills and collect payments over the counter or in the field.',
            'manages' => 'Consumer accounts and meters, reading routes, consumption per cycle, rate schedules, bills, payments, arrears and disconnection notices.',
            'terms' => 'water utility consumers meters readings cubic consumption bills arrears collection cooperative waterworks',
            'modules' => [
                ['Consumer accounts', 'Account holders, service addresses, meters and account status.'],
                ['Meter readings', 'Previous and present readings per route, with consumption computed for each account.'],
                ['Billing cycles', 'Bills generated for a cycle from readings and the rate schedule.'],
                ['Collections', 'Payments posted against bills, with receipts and daily collection totals.'],
                ['Arrears and notices', 'Overdue balances, penalties and lists of accounts due for notice.'],
            ],
            'steps' => [
                ['Open the cycle', 'A billing period is opened with its reading and due dates.'],
                ['Enter readings', 'Present readings are recorded by route; unusual consumption is flagged for checking.'],
                ['Generate bills', 'Consumption is priced with the rate schedule and arrears are added.'],
                ['Distribute bills', 'Bills are printed or prepared for delivery by route.'],
                ['Collect payments', 'Tellers post payments and issue receipts.'],
                ['Follow up arrears', 'Accounts still unpaid after the due date are listed for notice.'],
            ],
            'roles' => [
                ['Meter reader', 'Records present readings for assigned routes.'],
                ['Billing clerk', 'Reviews readings, generates and corrects bills.'],
                ['Teller', 'Posts payments and issues receipts.'],
                ['Manager', 'Monitors billed against collected amounts and arrears.'],
            ],
            'notes' => [
                'Rate schedules, minimum charges and penalties follow your approved tariff.',
                'Existing consumer accounts and their last readings are loaded before the first cycle.',
                'Bill and receipt layouts are matched to the forms you already issue.',
            ],
        ],

        // ------------------------------------------------------------------
        [
            'slug' => 'retail-pos-inventory',
            'name' => 'Retail POS & Inventory',
            'category' => 'Retail',
            'sector' => 'retail-supply',
            'tagline' => 'Every sale at the counter updates the stock behind it.',
            'summary' => 'Runs the checkout counter and deducts stock with each sale. Purchases, price changes and sales reports draw on the same item list.',
            'audience' => 'Store owners and staff of shops, groceries and supply stores that sell from a counter and restock from suppliers.',
            'manages' => 'Items and prices, checkout transactions, stock on hand, purchases from suppliers, stock adjustments and sales reports by day, item and cashier.',
            'terms' => 'store shop cashier checkout barcode receipt sales stock items purchases supplier grocery sari-sari',
            'modules' => [
                ['Checkout', 'Item lookup or scan, discounts, payment and receipt for each sale.'],
                ['Stock tracking', 'Quantity on hand per item, updated by sales, purchases and adjustments.'],
                ['Pricing', 'Selling prices and cost per item, with price changes kept on record.'],
                ['Purchases', 'Supplier deliveries received into stock with their cost.'],
                ['Sales reports', 'Sales by day, item and cashier, with low-stock listings.'],
            ],
            'steps' => [
                ['Set up items', 'Items are listed with their price, cost and opening quantity.'],
                ['Receive purchases', 'Deliveries from suppliers are added to stock.'],
                ['Sell at the counter', 'The cashier rings up items and takes payment.'],
                ['Close the shift', 'Cash is counted against the recorded sales for the shift.'],
                ['Review stock', 'Low or fast-moving items are checked for reordering.'],
                ['Read the reports', 'The owner reviews sales and margins for the period.'],
            ],
            'roles' => [
                ['Cashier', 'Rings up sales and closes their shift.'],
                ['Stock clerk', 'Receives deliveries and records adjustments.'],
                ['Owner or manager', 'Sets prices and reviews sales and stock reports.'],
            ],
            'notes' => [
                'The item list can be loaded from an existing price list.',
                'Receipt format and any printer or scanner used at the counter are confirmed during setup.',
                'An opening stock count is taken before the first selling day.',
            ],
        ],

        // ------------------------------------------------------------------
        [
            'slug' => 'ready-to-wear-business',
            'name' => 'Ready-to-Wear Business',
            'category' => 'Apparel Retail',
            'sector' => 'retail-supply',
            'tagline' => 'Stock counted by size and color, not just by style.',
            'summary' => 'Tracks clothing by style, size and color so staff can see which variants are in stock. Selling and customer orders deduct from the exact variant.',
            'audience' => 'Clothing sellers, boutiques and online apparel shops whose stock is the same design in many sizes and colors.',
            'manages' => 'Styles and their size and color variants, stock per variant, incoming stock, sales, customer orders and their fulfillment status.',
            'terms' => 'clothing apparel boutique garments sizes colors variants rtw fashion orders online seller',
            'modules' => [
                ['Styles and variants', 'Each style with its sizes and colors, and a price per style or variant.'],
                ['Variant stock', 'Quantity on hand for every size and color combination.'],
                ['Stock movement', 'Stock received, transferred, returned or adjusted, with the reason recorded.'],
                ['Selling', 'Counter sales that deduct from the chosen variant.'],
                ['Order handling', 'Customer orders from reservation to packing and release.'],
            ],
            'steps' => [
                ['Create the style', 'A design is listed with its available sizes and colors.'],
                ['Receive stock', 'Incoming pieces are counted in by variant.'],
                ['Sell or take an order', 'Staff pick the exact size and color being sold or reserved.'],
                ['Pack the order', 'Reserved pieces are pulled and packed.'],
                ['Release', 'The order is handed over or dispatched and marked complete.'],
                ['Review variants', 'Sold-out and slow-moving sizes and colors are reviewed for restocking.'],
            ],
            'roles' => [
                ['Sales staff', 'Sells at the counter and encodes customer orders.'],
                ['Stock keeper', 'Receives stock and records movements by variant.'],
                ['Owner', 'Reviews sales by style and decides what to restock.'],
            ],
            'notes' => [
                'Size and color sets are defined once and reused across styles.',
                'An opening count by variant is needed before going live.',
                'Order statuses are named to match how your shop already works.',
            ],
        ],

        // ------------------------------------------------------------------
        [
            'slug' => 'hotel-property-management',
            'name' => 'Hotel & Property Management',
            'category' => 'Hospitality',
            'sector' => 'hospitality-food',
            'tagline' => 'Front desk, housekeeping and guest charges on one room record.',
            'summary' => 'Manages reservations, check-in and check-out, and the charges on each guest stay. Housekeeping sees which rooms need attention.',
            'audience' => 'Front desk staff, housekeeping and managers of hotels, inns, resorts and transient houses.',
            'manages' => 'Room types and rates, reservations, guest stays, room assignments, housekeeping status, charges posted to a stay and settlement at check-out.',
            'terms' => 'hotel inn resort rooms reservations booking front desk check-in check-out guests housekeeping folio',
            'modules' => [
                ['Reservations', 'Bookings by date and room type, with availability checked before confirming.'],
                ['Guest stays', 'Check-in, check-out, extensions and room transfers.'],
                ['Room assignments', 'A room rack showing which rooms are vacant, occupied, reserved or out of service.'],
                ['Housekeeping', 'Room cleaning status and tasks for the day.'],
                ['Charges and settlement', 'Room and incidental charges on the guest account, settled at check-out.'],
            ],
            'steps' => [
                ['Take the reservation', 'Dates, room type and guest details are recorded.'],
                ['Assign a room', 'A specific room is allocated before or at arrival.'],
                ['Check in', 'The guest is registered and the stay begins.'],
                ['Post charges', 'Room nights and incidentals are added to the stay.'],
                ['Check out', 'The account is reviewed and settled.'],
                ['Turn the room', 'Housekeeping cleans the room and returns it to vacant.'],
            ],
            'roles' => [
                ['Front desk', 'Handles reservations, check-in, check-out and charges.'],
                ['Housekeeping', 'Updates room status as rooms are cleaned.'],
                ['Manager', 'Reviews occupancy, arrivals, departures and revenue.'],
            ],
            'notes' => [
                'Room types, room numbers and rates are set up first.',
                'Chargeable items such as extra beds or services are listed during configuration.',
                'Existing future reservations are encoded before the switch-over date.',
            ],
        ],

        // ------------------------------------------------------------------
        [
            'slug' => 'restaurant-management',
            'name' => 'Restaurant Management',
            'category' => 'Food Service',
            'sector' => 'hospitality-food',
            'tagline' => 'Orders move from the table to the kitchen to the bill without re-writing.',
            'summary' => 'Takes dine-in orders by table and sends them to the kitchen as tickets. The bill is built from what was served.',
            'audience' => 'Dine-in restaurants where servers take orders at the table and the kitchen prepares them in sequence.',
            'manages' => 'The menu, tables and their status, orders per table, kitchen tickets, bills, payments and sales reports by item and shift.',
            'terms' => 'restaurant dine-in tables waiter server kitchen orders menu bill split eatery diner',
            'modules' => [
                ['Dining orders', 'Orders taken per table, with additions and notes for the kitchen.'],
                ['Table management', 'A floor view of free, occupied and billed tables.'],
                ['Kitchen workflow', 'Tickets queued for the kitchen and marked as they are prepared and served.'],
                ['Billing', 'Bills from served items, with discounts, split payments and receipts.'],
                ['Sales reporting', 'Sales by item, category and shift.'],
            ],
            'steps' => [
                ['Seat the guests', 'A table is opened for the party.'],
                ['Take the order', 'Items are entered against the table.'],
                ['Send to the kitchen', 'A ticket is queued for preparation.'],
                ['Serve', 'Items are marked served as they leave the kitchen.'],
                ['Bill the table', 'The bill is produced and payment is taken.'],
                ['Clear the table', 'The table returns to free for the next party.'],
            ],
            'roles' => [
                ['Server', 'Opens tables and takes orders.'],
                ['Kitchen staff', 'Prepares tickets and marks them ready.'],
                ['Cashier', 'Bills tables and takes payment.'],
                ['Manager', 'Maintains the menu and reviews sales.'],
            ],
            'notes' => [
                'The menu, categories and table layout are set up before opening.',
                'How kitchen tickets are shown, on a screen or printed, is decided with you during setup.',
                'Discount rules and service charges follow your current practice.',
            ],
        ],

        // ------------------------------------------------------------------
        [
            'slug' => 'coffee-shop-management',
            'name' => 'Coffee Shop Management',
            'category' => 'Food Service',
            'sector' => 'hospitality-food',
            'tagline' => 'Fast counter orders, with ingredients counted as drinks are sold.',
            'summary' => 'Built for counter service: a menu with sizes and add-ons, quick order entry and delivery orders in the same queue. Ingredient stock is reduced as drinks are sold.',
            'audience' => 'Cafes, milk tea shops and kiosks that take orders at the counter and prepare them while the customer waits.',
            'manages' => 'Menu items with sizes and add-ons, counter and delivery orders, the preparation queue, ingredient inventory and daily transactions.',
            'terms' => 'cafe coffee milk tea kiosk barista counter drinks add-ons delivery orders ingredients',
            'modules' => [
                ['Menu', 'Drinks and food with sizes, add-ons and prices.'],
                ['Counter sales', 'Quick order entry, payment and a number or name for pickup.'],
                ['Delivery orders', 'Orders for delivery recorded alongside counter orders.'],
                ['Inventory', 'Ingredients and supplies, reduced by recipe as items are sold.'],
                ['Transactions', 'Daily sales, payment types and shift totals.'],
            ],
            'steps' => [
                ['Take the order', 'The cashier selects items, sizes and add-ons.'],
                ['Take payment', 'Payment is recorded and the order joins the queue.'],
                ['Prepare', 'The barista works through the queue.'],
                ['Hand over or dispatch', 'The order is called for pickup or passed to the rider.'],
                ['Count ingredients', 'Stock is reduced by what was used and low items are flagged.'],
                ['Close the day', 'Sales and cash are reviewed at closing.'],
            ],
            'roles' => [
                ['Cashier', 'Takes orders and payment at the counter.'],
                ['Barista', 'Prepares orders from the queue.'],
                ['Owner or manager', 'Maintains the menu, checks ingredients and reviews daily sales.'],
            ],
            'notes' => [
                'Recipes are entered per menu item so ingredient use can be computed.',
                'Delivery orders are recorded by staff; any link to a delivery platform would be a separate integration to confirm.',
                'Sizes and add-ons are defined once and applied across the menu.',
            ],
        ],

        // ------------------------------------------------------------------
        [
            'slug' => 'laundry-management',
            'name' => 'Laundry Management',
            'category' => 'Local Services',
            'sector' => 'services-recreation',
            'tagline' => 'Every load tracked from the counter to the claim shelf.',
            'summary' => 'Records each laundry order at intake and follows it through washing, drying and folding. Staff can tell a customer the status of their order and what is due.',
            'audience' => 'Laundry shops and wash-dry-fold services that receive items from walk-in customers and release them later.',
            'manages' => 'Customer orders, services and pricing by weight or item, order status at each stage, claim stubs, payments and unclaimed orders.',
            'terms' => 'laundry laundromat wash dry fold dry cleaning kilos claim stub pickup',
            'modules' => [
                ['Service intake', 'Orders received with weight or item count, chosen services and a claim number.'],
                ['Item tracking', 'Each order followed through washing, drying, folding and ready for claim.'],
                ['Pricing', 'Rates by kilo, load or item, with add-on services.'],
                ['Order status', 'A board of orders by stage, with due dates.'],
                ['Payment', 'Payment at intake or on release, with balances shown for unpaid orders.'],
            ],
            'steps' => [
                ['Receive items', 'Items are weighed or counted and the order is given a claim number.'],
                ['Assign services', 'Wash, dry, fold, press or other services are selected and priced.'],
                ['Track cleaning', 'The order moves through each stage as staff update it.'],
                ['Notify the customer', 'The order is marked ready so the customer can be informed.'],
                ['Release the order', 'Items are handed over against the claim number.'],
                ['Record payment', 'Any remaining balance is collected and the order is closed.'],
            ],
            'roles' => [
                ['Counter staff', 'Receives items, prices orders and releases them.'],
                ['Laundry staff', 'Updates the stage of each order.'],
                ['Owner', 'Reviews daily orders, collections and unclaimed items.'],
            ],
            'notes' => [
                'Service rates and add-ons are configured to your price list.',
                'How customers are notified depends on the channels available to your shop and is confirmed during setup.',
                'Claim stub layout can follow the stub you currently issue.',
            ],
        ],

        // ------------------------------------------------------------------
        [
            'slug' => 'car-wash-management',
            'name' => 'Car Wash Management',
            'category' => 'Local Services',
            'sector' => 'services-recreation',
            'tagline' => 'Know which vehicle is in which bay, and how long it has been there.',
            'summary' => 'Logs each vehicle with its service package and assigns it to a bay. Job times, supplies used and collections are recorded per job.',
            'audience' => 'Car wash and auto detailing shops that handle a queue of vehicles across several bays.',
            'manages' => 'Service packages and prices, the vehicle queue, bay assignments, job start and finish times, supplies consumption and daily collections.',
            'terms' => 'car wash carwash auto detailing vehicles bays queue washers wax vacuum',
            'modules' => [
                ['Service packages', 'Packages and add-ons priced by vehicle size.'],
                ['Bay assignments', 'Which vehicle is in which bay, and which bays are free.'],
                ['Job timing', 'Start and finish time for each job, with elapsed time shown.'],
                ['Supplies', 'Soap, wax and other supplies issued and remaining.'],
                ['Collection', 'Payment per job and totals for the day.'],
            ],
            'steps' => [
                ['Log the vehicle', 'Plate number, vehicle size and chosen package are recorded.'],
                ['Queue or assign a bay', 'The job waits in the queue or goes straight to a free bay.'],
                ['Start the job', 'The start time is recorded and the crew is noted.'],
                ['Finish and inspect', 'The job is marked done and the bay is freed.'],
                ['Collect payment', 'The customer pays for the package and add-ons.'],
                ['Review the day', 'Jobs, collections and supplies used are summarized.'],
            ],
            'roles' => [
                ['Receptionist or cashier', 'Logs vehicles and collects payment.'],
                ['Bay supervisor', 'Assigns bays and marks jobs started and finished.'],
                ['Owner', 'Reviews job counts, timing and collections.'],
            ],
            'notes' => [
                'Packages, vehicle sizes and prices are set up from your current menu of services.',
                'The number of bays and their names are configured to your site.',
                'If crew incentives are computed per job, the basis is agreed during configuration.',
            ],
        ],

        // ------------------------------------------------------------------
        [
            'slug' => 'pickleball-court-booking',
            'name' => 'Pickleball Court Booking',
            'category' => 'Recreation',
            'sector' => 'services-recreation',
            'tagline' => 'A clear schedule for every court, with fees tracked per booking.',
            'summary' => 'Shows court availability by hour and records reservations against it. Fees and payments are tracked for each booking, so double bookings and unpaid slots are easy to spot.',
            'audience' => 'Pickleball facilities and sports clubs that rent courts by the hour.',
            'manages' => 'Courts and operating hours, hourly rates, reservations, occupancy through the day, booking fees and payment status.',
            'terms' => 'pickleball courts sports facility reservation schedule slots rental club booking',
            'modules' => [
                ['Court availability', 'An hourly schedule for each court showing free, reserved and blocked slots.'],
                ['Reservations', 'Bookings by court, date and time, with the player or group who reserved.'],
                ['Occupancy', 'How full each court is across the day and week.'],
                ['Fees', 'Rates by time of day, with the amount due computed per booking.'],
                ['Payment tracking', 'Paid, partially paid and unpaid bookings.'],
            ],
            'steps' => [
                ['Check availability', 'Staff look up free slots for the requested date.'],
                ['Reserve the court', 'The slot is held under the player or group.'],
                ['Compute the fee', 'The rate for that time produces the amount due.'],
                ['Record payment', 'A deposit or full payment is posted to the booking.'],
                ['Check in the players', 'The booking is marked as played when the group arrives.'],
                ['Review occupancy', 'The manager reviews used and unused hours.'],
            ],
            'roles' => [
                ['Front desk', 'Takes reservations, collects fees and checks players in.'],
                ['Facility manager', 'Sets rates and hours, blocks courts and reviews occupancy.'],
                ['Player', 'Requests a slot; online self-booking is an option to confirm with GSMaRT.'],
            ],
            'notes' => [
                'Courts, operating hours and peak or off-peak rates are configured first.',
                'Rules for deposits, cancellations and no-shows follow your facility policy.',
                'Recurring bookings for regular groups can be discussed during the demo.',
            ],
        ],

        // ------------------------------------------------------------------
        [
            'slug' => 'shipping-logistics',
            'name' => 'Shipping & Logistics',
            'category' => 'Logistics',
            'sector' => 'logistics-construction',
            'tagline' => 'One shipment record that everyone updates and anyone can check.',
            'summary' => 'Keeps a record for each shipment with its sender, consignee and contents. Status is updated at each handover, so staff can answer where a shipment is.',
            'audience' => 'Shipping lines, cargo forwarders and courier operators that accept shipments and move them between locations.',
            'manages' => 'Shipment bookings, customers and consignees, cargo details, status updates along the route, charges and operational reports.',
            'terms' => 'cargo freight forwarding courier waybill tracking shipments consignee delivery vessel trucking',
            'modules' => [
                ['Shipment records', 'Each shipment with its reference number, origin, destination and cargo details.'],
                ['Status updates', 'A dated history of where the shipment has been and its current status.'],
                ['Customers', 'Shippers and consignees with their shipment history.'],
                ['Charges', 'Freight and other charges per shipment, with payment status.'],
                ['Operational reporting', 'Shipments by status, route and period.'],
            ],
            'steps' => [
                ['Accept the shipment', 'Cargo details, shipper and consignee are recorded and a reference number is issued.'],
                ['Compute charges', 'Freight and other fees are assessed.'],
                ['Dispatch', 'The shipment is loaded and marked in transit.'],
                ['Update along the route', 'Each handover or arrival is recorded with its date.'],
                ['Deliver or release', 'The consignee receives the cargo and the release is recorded.'],
                ['Report', 'Volumes and pending shipments are reviewed.'],
            ],
            'roles' => [
                ['Booking clerk', 'Accepts shipments and issues reference numbers.'],
                ['Operations staff', 'Updates status at dispatch, transit and arrival.'],
                ['Cashier', 'Collects and records charges.'],
                ['Manager', 'Monitors volumes, delays and collections.'],
            ],
            'notes' => [
                'Routes, locations and status names are configured to your operation.',
                'Rate tables are loaded from your current tariff.',
                'Whether customers can look up status themselves is an option to confirm with GSMaRT.',
            ],
        ],

        // ------------------------------------------------------------------
        [
            'slug' => 'construction-management',
            'name' => 'Construction Management',
            'category' => 'Construction',
            'sector' => 'logistics-construction',
            'tagline' => 'Progress, materials and spending tracked against the project plan.',
            'summary' => 'Organizes each project into tasks with a schedule and budget. Materials issued, expenses and accomplishment are recorded against it as work proceeds.',
            'audience' => 'Contractors and project teams that run several projects and need to see progress and cost side by side.',
            'manages' => 'Projects and their phases, tasks and schedules, materials requested and issued, expenses, labor and equipment assigned, and progress against plan.',
            'terms' => 'contractor projects site engineer materials budget expenses progress accomplishment gantt building',
            'modules' => [
                ['Projects', 'Project details, phases, contract amount and timeline.'],
                ['Tasks', 'Scheduled work items with planned dates and percentage complete.'],
                ['Materials', 'Requests, deliveries and issuances of materials per project.'],
                ['Expenses', 'Project costs recorded by category against the budget.'],
                ['Resources', 'Labor and equipment assigned to each project.'],
                ['Progress tracking', 'Planned against actual accomplishment over time.'],
            ],
            'steps' => [
                ['Set up the project', 'Scope, phases, budget and schedule are recorded.'],
                ['Plan the tasks', 'Work items are scheduled and assigned.'],
                ['Request and issue materials', 'Site requests are approved and materials are issued to the project.'],
                ['Record expenses', 'Costs are entered against budget categories.'],
                ['Report accomplishment', 'Site staff update the percentage complete for each task.'],
                ['Review progress and cost', 'Managers compare actual progress and spending with the plan.'],
            ],
            'roles' => [
                ['Project manager', 'Plans tasks and reviews progress and cost.'],
                ['Site engineer', 'Requests materials and reports accomplishment.'],
                ['Purchasing or warehouse', 'Processes material requests and issuances.'],
                ['Accounting', 'Records project expenses.'],
            ],
            'notes' => [
                'Budget categories and task templates can follow your usual bill of quantities.',
                'Ongoing projects are entered with their progress to date.',
                'Approval steps for material requests are agreed during configuration.',
            ],
        ],

        // ------------------------------------------------------------------
        [
            'slug' => 'multi-business-operations',
            'name' => 'Multi-Business Operations',
            'category' => 'Multi-Branch',
            'sector' => 'finance-administration',
            'tagline' => 'Several businesses in one system, each with its own records.',
            'summary' => 'Lets one owner or group run several businesses or branches in a single system. Each keeps its own data, and the owner can view them separately or together.',
            'audience' => 'Owners and head offices that operate more than one company, branch or line of business.',
            'manages' => 'Company and branch profiles, users and their access per business, separated transactions and records, and reports per business or consolidated.',
            'terms' => 'branches companies group head office consolidated multi-company franchise outlets',
            'modules' => [
                ['Company separation', 'Each business keeps its own customers, transactions and settings.'],
                ['Business-level operations', 'Daily work is done inside one selected business at a time.'],
                ['User access', 'Users are granted access to specific businesses and functions.'],
                ['Consolidated reporting', 'Figures viewed per business or combined across the group.'],
            ],
            'steps' => [
                ['Register each business', 'Companies or branches are created with their own profile.'],
                ['Assign users', 'Staff are given access to the businesses they work in.'],
                ['Work within a business', 'Transactions are recorded under the selected business.'],
                ['Switch business', 'Authorized users move between businesses without signing out.'],
                ['Compare and consolidate', 'The owner reviews each business and the group total.'],
            ],
            'roles' => [
                ['Group owner or administrator', 'Creates businesses, assigns access and reads consolidated reports.'],
                ['Business manager', 'Manages operations and reports for one business.'],
                ['Staff', 'Records transactions in the business they are assigned to.'],
            ],
            'notes' => [
                'Which operational modules run inside each business is decided per engagement.',
                'Businesses can be added later without disturbing existing records.',
                'Access rules are reviewed with you so sensitive figures stay with the right people.',
            ],
        ],

        // ------------------------------------------------------------------
        [
            'slug' => 'inventory-warehouse',
            'name' => 'Inventory & Warehouse',
            'category' => 'Supply Chain',
            'sector' => 'retail-supply',
            'tagline' => 'What is in the warehouse, where it came from and when to reorder.',
            'summary' => 'Records every receipt and issue of stock so quantities on hand can be trusted. Reorder levels flag items before they run out.',
            'audience' => 'Warehouses, stock rooms and distributors that receive goods from suppliers and issue them to customers or branches.',
            'manages' => 'Products and units, suppliers, receiving, issuances and transfers, stock on hand by location, stock counts and reorder levels.',
            'terms' => 'warehouse stockroom stock card receiving issuance transfers suppliers reorder count distributor bodega',
            'modules' => [
                ['Products', 'Items with their units, categories and reorder levels.'],
                ['Suppliers', 'Supplier details and what each has delivered.'],
                ['Stock movement', 'Issuances, transfers and adjustments, each with a reference.'],
                ['Receiving', 'Deliveries checked in against what was ordered.'],
                ['Reorder information', 'Items at or below their reorder level.'],
                ['Stock counts', 'Physical counts compared with recorded quantities.'],
            ],
            'steps' => [
                ['Maintain the product list', 'Items, units and reorder levels are kept current.'],
                ['Receive deliveries', 'Incoming goods are checked and added to stock.'],
                ['Store by location', 'Stock is recorded against the warehouse or bin it is kept in.'],
                ['Issue or transfer', 'Outgoing stock is recorded with its destination.'],
                ['Count', 'Physical counts are compared with the records and differences adjusted.'],
                ['Reorder', 'Items below their reorder level are listed for purchasing.'],
            ],
            'roles' => [
                ['Warehouse staff', 'Receives, issues and transfers stock.'],
                ['Inventory controller', 'Maintains products, runs counts and approves adjustments.'],
                ['Purchasing', 'Acts on the reorder list.'],
                ['Manager', 'Reviews stock levels and movement.'],
            ],
            'notes' => [
                'An opening count establishes starting quantities.',
                'Locations can be a single stock room or several warehouses.',
                'Units of measure and conversions, such as box to piece, are set per product.',
            ],
        ],
    ],
];
