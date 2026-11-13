<?php
/**
 * Illustrative interface previews, one entry per system slug.
 *
 * These describe the sample screens drawn on cards and detail pages. All
 * figures, reference numbers and codes are made-up sample data, and no real
 * customer, employee or company names are used. Replace them with real
 * screenshots or approved sample data when GSMaRT supplies them.
 *
 * A system with no entry here still gets a preview built from its modules
 * and workflow, so adding a system in the admin area never breaks a page.
 *
 * Screen fields
 *   label   name of the screen tab on the detail page
 *   app     short product name shown in the side menu
 *   nav     side menu items, 'active' is the highlighted one (counting from 0)
 *   title, meta, action   the screen's top bar
 *   blocks  list of [type, data]. Types: stats, table, bars, grid, board,
 *           items, ticket, tiles, timeline, gantt, matrix, kv, list, cols, h
 *
 * Status text can start with ok:, warn:, bad:, info: or mute: to colour it.
 * In a table, 'num' lists the right-aligned column numbers and 'w' sets the
 * column widths.
 */

$nav = [
    'acc'  => ['Dashboard', 'General ledger', 'Receivables', 'Payables', 'Cash and bank', 'Reports'],
    'hr'   => ['Employees', 'Attendance', 'Leave', 'Payroll', 'Payslips', 'Reports'],
    'sch'  => ['Enrollment', 'Students', 'Sections', 'Fees', 'Cashier', 'Setup'],
    'wat'  => ['Accounts', 'Readings', 'Billing', 'Collections', 'Arrears', 'Reports'],
    'pos'  => ['Checkout', 'Items', 'Stock', 'Purchases', 'Reports'],
    'rtw'  => ['Styles', 'Variant stock', 'Movements', 'Selling', 'Orders'],
    'htl'  => ['Reservations', 'Stays', 'Room rack', 'Housekeeping', 'Charges', 'Reports'],
    'rst'  => ['Floor', 'Orders', 'Kitchen', 'Billing', 'Menu', 'Reports'],
    'cof'  => ['Counter', 'Queue', 'Delivery', 'Menu', 'Inventory', 'Sales'],
    'lau'  => ['Intake', 'Orders', 'Pricing', 'Payments', 'Reports'],
    'car'  => ['Queue', 'Bays', 'Packages', 'Supplies', 'Collection'],
    'pkl'  => ['Schedule', 'Reservations', 'Rates', 'Payments', 'Reports'],
    'shp'  => ['Shipments', 'Tracking', 'Customers', 'Charges', 'Reports'],
    'con'  => ['Projects', 'Tasks', 'Materials', 'Expenses', 'Resources', 'Progress'],
    'mul'  => ['Group overview', 'Businesses', 'Users', 'Reports'],
    'inv'  => ['Products', 'Suppliers', 'Receiving', 'Movements', 'Reorder', 'Counts'],
];

return [

    // -------------------------------------------------- home page showcase
    // Which screens appear on the devices in the home page hero.
    '_hero' => [
        'laptop' => ['accounting-finance', 0],
        'tablet' => ['hotel-property-management', 1],
        'window' => ['construction-management', 0],
        'phone'  => [
            'app' => 'Water Billing', 'nav' => ['Routes', 'Readings', 'Flagged', 'Sync'], 'active' => 1,
            'title' => 'Route 03', 'meta' => '82 of 96 read',
            'blocks' => [
                ['list', [
                    ['01-0417', 'Previous 1,284', 'ok:1,302'],
                    ['01-0418', 'Previous 956', 'ok:981'],
                    ['01-0422', 'Previous 2,013', 'warn:2,076'],
                    ['01-0425', 'Previous 1,540', 'ok:1,551'],
                    ['01-0431', 'Previous 733', 'mute:Not read'],
                    ['01-0433', 'Previous 1,108', 'mute:Not read'],
                    ['01-0436', 'Previous 2,490', 'mute:Not read'],
                    ['01-0440', 'Previous 618', 'mute:Not read'],
                ]],
            ],
        ],
    ],

    // ------------------------------------------------------------ accounting
    'accounting-finance' => ['screens' => [
        [
            'label' => 'Overview', 'app' => 'Accounting', 'nav' => $nav['acc'], 'active' => 0,
            'title' => 'Financial overview', 'meta' => 'Period: January',
            'blocks' => [
                ['stats', [['Cash and bank', '1,284,600.00'], ['Receivables', '412,350.00'], ['Payables', '268,900.00']]],
                ['cols', ['w' => '3fr 2fr', 'items' => [
                    [['bars', ['label' => 'Collections by week', 'labels' => ['W1', 'W2', 'W3', 'W4', 'W5'], 'values' => [82, 126, 97, 148, 64], 'mark' => 3]]],
                    [['table', ['cols' => ['Receivables aging', 'Amount'], 'w' => '3fr 2fr', 'num' => [1], 'rows' => [
                        ['Current', '248,100.00'],
                        ['1 to 30 days', '96,400.00'],
                        ['31 to 60 days', '45,250.00'],
                        ['Over 60 days', '22,600.00'],
                        ['Total', '412,350.00', 'total' => 1],
                    ]]]],
                ]]],
            ],
        ],
        [
            'label' => 'Journal entries', 'app' => 'Accounting', 'nav' => $nav['acc'], 'active' => 1,
            'title' => 'Journal entries', 'meta' => 'January', 'action' => 'New entry',
            'blocks' => [
                ['table', ['cols' => ['Date', 'Reference', 'Description', 'Amount', 'Status'], 'w' => '1fr 1.2fr 3fr 1.4fr 1.4fr', 'num' => [3], 'rows' => [
                    ['Jan 08', 'JV-0112', 'Office rent, January', '35,000.00', 'ok:Posted'],
                    ['Jan 08', 'CR-0431', 'Collection, INV-0387', '18,400.00', 'ok:Posted'],
                    ['Jan 09', 'CD-0276', 'Supplier payment, BILL-0164', '52,750.00', 'ok:Posted'],
                    ['Jan 09', 'JV-0113', 'Depreciation, equipment', '8,250.00', 'warn:For review'],
                    ['Jan 10', 'SI-0392', 'Sales invoice, services', '27,900.00', 'info:Draft'],
                    ['Jan 10', 'CR-0432', 'Collection, INV-0390', '12,600.00', 'info:Draft'],
                ]]],
            ],
        ],
        [
            'label' => 'Trial balance', 'app' => 'Accounting', 'nav' => $nav['acc'], 'active' => 5,
            'title' => 'Trial balance', 'meta' => 'As of January 31', 'action' => 'Print',
            'blocks' => [
                ['table', ['cols' => ['Code', 'Account', 'Debit', 'Credit'], 'w' => '.8fr 3fr 1.6fr 1.6fr', 'num' => [2, 3], 'rows' => [
                    ['1010', 'Cash on hand', '84,600.00', ''],
                    ['1020', 'Cash in bank', '1,200,000.00', ''],
                    ['1200', 'Accounts receivable', '412,350.00', ''],
                    ['2010', 'Accounts payable', '', '268,900.00'],
                    ['3010', 'Owner\'s capital', '', '1,150,000.00'],
                    ['4010', 'Service revenue', '', '642,300.00'],
                    ['5010', 'Salaries expense', '286,000.00', ''],
                    ['5020', 'Rent and utilities', '78,250.00', ''],
                    ['', 'Total', '2,061,200.00', '2,061,200.00', 'total' => 1],
                ]]],
            ],
        ],
    ]],

    // -------------------------------------------------------------- payroll
    'human-resource-payroll' => ['screens' => [
        [
            'label' => 'Payroll run', 'app' => 'HR and Payroll', 'nav' => $nav['hr'], 'active' => 3,
            'title' => 'Payroll run', 'meta' => 'January 1 to 15', 'action' => 'Finalize run',
            'blocks' => [
                ['stats', [['Employees', '48'], ['Gross pay', '612,480.00'], ['Deductions', '71,935.00'], ['Net pay', '540,545.00']]],
                ['table', ['cols' => ['Employee', 'Position', 'Basic', 'Overtime', 'Deductions', 'Net pay'], 'w' => '1fr 2fr 1.2fr 1.2fr 1.2fr 1.3fr', 'num' => [2, 3, 4, 5], 'rows' => [
                    ['E-0104', 'Accounting clerk', '11,500.00', '620.00', '1,410.00', '10,710.00'],
                    ['E-0117', 'Warehouse staff', '9,800.00', '1,225.00', '1,180.00', '9,845.00'],
                    ['E-0123', 'Sales associate', '10,200.00', '0.00', '1,236.00', '8,964.00'],
                    ['E-0131', 'Supervisor', '16,750.00', '0.00', '2,315.00', '14,435.00'],
                    ['E-0142', 'Driver', '9,800.00', '1,837.50', '1,204.00', '10,433.50'],
                ]]],
            ],
        ],
        [
            'label' => 'Attendance', 'app' => 'HR and Payroll', 'nav' => $nav['hr'], 'active' => 1,
            'title' => 'Daily time record', 'meta' => 'Wednesday, January 14',
            'blocks' => [
                ['stats', [['Present', '44'], ['On leave', '3'], ['Absent', '1'], ['Late arrivals', '5']]],
                ['table', ['cols' => ['Employee', 'Time in', 'Time out', 'Late', 'Overtime', 'Status'], 'w' => '1.2fr 1fr 1fr 1fr 1fr 1.5fr', 'rows' => [
                    ['E-0104', '07:56', '17:04', '', '', 'ok:Complete'],
                    ['E-0117', '08:12', '18:31', '12 min', '1.5 h', 'warn:Late'],
                    ['E-0123', '07:49', '17:01', '', '', 'ok:Complete'],
                    ['E-0131', '', '', '', '', 'info:On leave'],
                    ['E-0142', '07:58', '', '', '', 'mute:No time out'],
                ]]],
            ],
        ],
        [
            'label' => 'Leave', 'app' => 'HR and Payroll', 'nav' => $nav['hr'], 'active' => 2,
            'title' => 'Leave requests', 'meta' => '2 awaiting approval',
            'blocks' => [
                ['table', ['cols' => ['Employee', 'Type', 'Dates', 'Days', 'Balance', 'Status'], 'w' => '1fr 1.2fr 1.6fr .7fr .9fr 1.8fr', 'num' => [3, 4], 'rows' => [
                    ['E-0131', 'Vacation', 'Jan 14 to 16', '3', '9', 'ok:Approved'],
                    ['E-0109', 'Sick', 'Jan 12', '1', '6', 'ok:Approved'],
                    ['E-0126', 'Vacation', 'Jan 22 to 23', '2', '4', 'warn:Pending'],
                    ['E-0138', 'Emergency', 'Jan 19', '1', '2', 'warn:Pending'],
                    ['E-0115', 'Vacation', 'Feb 2 to 6', '5', '3', 'bad:Over balance'],
                ]]],
            ],
        ],
    ]],

    // --------------------------------------------------------------- school
    'school-management' => ['screens' => [
        [
            'label' => 'Enrollment', 'app' => 'School', 'nav' => $nav['sch'], 'active' => 0,
            'title' => 'Enrollment', 'meta' => 'School year 2026 to 2027', 'action' => 'Enroll student',
            'blocks' => [
                ['stats', [['Enrolled', '612'], ['Pending requirements', '37'], ['Sections', '24']]],
                ['table', ['cols' => ['Student no.', 'Level', 'Section', 'Requirements', 'Status'], 'w' => '1.3fr 1fr 1fr 1.4fr 1.3fr', 'rows' => [
                    ['2026-0113', 'Grade 7', '7-A', 'Complete', 'ok:Enrolled'],
                    ['2026-0114', 'Grade 7', '7-B', 'Lacking 1', 'warn:Pending'],
                    ['2026-0115', 'Grade 8', '8-A', 'Complete', 'ok:Enrolled'],
                    ['2026-0116', 'Grade 10', '10-C', 'Complete', 'info:Assessed'],
                    ['2026-0117', 'Grade 11', '11-A', 'Lacking 2', 'warn:Pending'],
                ]]],
            ],
        ],
        [
            'label' => 'Fee assessment', 'app' => 'School', 'nav' => $nav['sch'], 'active' => 3,
            'title' => 'Fee assessment', 'meta' => 'Student 2026-0113, Grade 7',
            'blocks' => [
                ['cols', ['w' => '1fr 1fr', 'items' => [
                    [['table', ['cols' => ['Fee', 'Amount'], 'w' => '2fr 1fr', 'num' => [1], 'rows' => [
                        ['Tuition', '18,000.00'],
                        ['Miscellaneous', '6,500.00'],
                        ['Laboratory', '2,000.00'],
                        ['Books', '2,000.00'],
                        ['Total assessment', '28,500.00', 'total' => 1],
                    ]]]],
                    [['table', ['cols' => ['Installment', 'Amount', 'Status'], 'w' => '1.6fr 1fr 1fr', 'num' => [1], 'rows' => [
                        ['Upon enrollment', '7,125.00', 'ok:Paid'],
                        ['Second quarter', '7,125.00', 'warn:Due'],
                        ['Third quarter', '7,125.00', 'mute:Upcoming'],
                        ['Fourth quarter', '7,125.00', 'mute:Upcoming'],
                    ]]]],
                ]]],
            ],
        ],
        [
            'label' => 'Sections', 'app' => 'School', 'nav' => $nav['sch'], 'active' => 2,
            'title' => 'Sections', 'meta' => 'Grade 7',
            'blocks' => [
                ['cols', ['w' => '3fr 2fr', 'items' => [
                    [['table', ['cols' => ['Section', 'Adviser', 'Room', 'Enrolled', 'Status'], 'w' => '.8fr 1fr 1fr 1fr 1.1fr', 'num' => [3], 'rows' => [
                        ['7-A', 'T-014', '201', '38 of 40', 'ok:Open'],
                        ['7-B', 'T-022', '202', '40 of 40', 'mute:Full'],
                        ['7-C', 'T-009', '203', '35 of 40', 'ok:Open'],
                        ['7-D', 'T-031', '204', '29 of 40', 'ok:Open'],
                    ]]]],
                    [['bars', ['label' => 'Enrolled by grade level', 'labels' => ['7', '8', '9', '10', '11', '12'], 'values' => [142, 128, 110, 96, 74, 62]]]],
                ]]],
            ],
        ],
    ]],

    // ---------------------------------------------------------------- water
    'water-billing-collection' => ['screens' => [
        [
            'label' => 'Billing cycle', 'app' => 'Water Billing', 'nav' => $nav['wat'], 'active' => 2,
            'title' => 'Billing cycle', 'meta' => 'January, due February 10', 'action' => 'Generate bills',
            'blocks' => [
                ['stats', [['Accounts', '1,248'], ['Meters read', '1,234'], ['Billed', '386,420.00'], ['Collected', '142,975.00']]],
                ['table', ['cols' => ['Account', 'Previous', 'Present', 'Used', 'Amount', 'Status'], 'w' => '1.2fr 1fr 1fr .9fr 1.1fr 1.6fr', 'num' => [1, 2, 3, 4], 'rows' => [
                    ['01-0417', '1,284', '1,302', '18', '426.00', 'ok:Paid'],
                    ['01-0418', '956', '981', '25', '610.00', 'info:Billed'],
                    ['01-0422', '2,013', '2,076', '63', '1,832.00', 'warn:Check reading'],
                    ['01-0425', '1,540', '1,551', '11', '262.00', 'info:Billed'],
                    ['01-0431', '733', '', '', '', 'mute:Not read'],
                ]]],
            ],
        ],
        [
            'label' => 'Meter readings', 'app' => 'Water Billing', 'nav' => $nav['wat'], 'active' => 1,
            'title' => 'Meter readings', 'meta' => 'Route 03',
            'blocks' => [
                ['stats', [['Read', '82 of 96'], ['Flagged', '3'], ['Average use', '19 cu m']]],
                ['table', ['cols' => ['Seq', 'Account', 'Meter no.', 'Previous', 'Present', 'Used'], 'w' => '.6fr 1.2fr 1.4fr 1fr 1fr 1.2fr', 'num' => [3, 4], 'rows' => [
                    ['041', '01-0417', 'M-58120', '1,284', '1,302', '18'],
                    ['042', '01-0418', 'M-58133', '956', '981', '25'],
                    ['043', '01-0422', 'M-60217', '2,013', '2,076', 'warn:63'],
                    ['044', '01-0425', 'M-60218', '1,540', '1,551', '11'],
                    ['045', '01-0431', 'M-61002', '733', '', 'mute:Not read'],
                ]]],
            ],
        ],
        [
            'label' => 'Collections', 'app' => 'Water Billing', 'nav' => $nav['wat'], 'active' => 3,
            'title' => 'Collections', 'meta' => 'Today',
            'blocks' => [
                ['stats', [['Receipts', '64'], ['Collected', '38,715.00'], ['Arrears collected', '6,240.00']]],
                ['cols', ['w' => '2fr 3fr', 'items' => [
                    [['bars', ['label' => 'Collections by day', 'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'], 'values' => [31, 44, 38, 52, 39, 18], 'mark' => 4]]],
                    [['table', ['cols' => ['Receipt', 'Account', 'Paid'], 'w' => '1.2fr 1.2fr 1fr', 'num' => [2], 'rows' => [
                        ['OR-20114', '01-0417', '426.00'],
                        ['OR-20115', '02-0093', '1,180.00'],
                        ['OR-20116', '03-0271', '355.00'],
                        ['OR-20117', '01-0388', '742.00'],
                    ]]]],
                ]]],
            ],
        ],
    ]],

    // ------------------------------------------------------------ retail POS
    'retail-pos-inventory' => ['screens' => [
        [
            'label' => 'Checkout', 'app' => 'Retail POS', 'nav' => $nav['pos'], 'active' => 0,
            'title' => 'Checkout', 'meta' => 'Counter 1',
            'blocks' => [
                ['cols', ['w' => '3fr 2fr', 'items' => [
                    [['items', [
                        ['Rice, 5 kg', '285.00'], ['Cooking oil, 1 L', '148.00'], ['Sugar, 1 kg', '92.00'],
                        ['Laundry soap', '24.00'], ['Canned tuna', '38.50'], ['Instant coffee', '86.00'],
                        ['Bottled water, 6s', '110.00'], ['Bath soap', '42.00'], ['Eggs, dozen', '108.00'],
                    ]]],
                    [['ticket', ['title' => 'Sale 000482', 'lines' => [
                        ['1 Rice, 5 kg', '285.00'], ['2 Canned tuna', '77.00'],
                        ['1 Cooking oil, 1 L', '148.00'], ['3 Laundry soap', '72.00'],
                    ], 'total' => '582.00', 'button' => 'Take payment']]],
                ]]],
            ],
        ],
        [
            'label' => 'Stock', 'app' => 'Retail POS', 'nav' => $nav['pos'], 'active' => 2,
            'title' => 'Stock on hand', 'meta' => '1,146 items',
            'blocks' => [
                ['stats', [['Items', '1,146'], ['Low stock', '23'], ['Out of stock', '4']]],
                ['table', ['cols' => ['Item', 'On hand', 'Reorder at', 'Price', 'Status'], 'w' => '2.4fr 1fr 1fr 1fr 1.3fr', 'num' => [1, 2, 3], 'rows' => [
                    ['Rice, 5 kg', '64', '20', '285.00', 'ok:In stock'],
                    ['Cooking oil, 1 L', '18', '24', '148.00', 'warn:Low'],
                    ['Canned tuna', '212', '60', '38.50', 'ok:In stock'],
                    ['Eggs, dozen', '0', '15', '108.00', 'bad:Out'],
                    ['Laundry soap', '96', '40', '24.00', 'ok:In stock'],
                ]]],
            ],
        ],
        [
            'label' => 'Sales report', 'app' => 'Retail POS', 'nav' => $nav['pos'], 'active' => 4,
            'title' => 'Sales report', 'meta' => 'This week',
            'blocks' => [
                ['stats', [['Sales', '184,650.00'], ['Transactions', '1,312'], ['Average sale', '140.74']]],
                ['cols', ['w' => '1fr 1fr', 'items' => [
                    [['bars', ['label' => 'Sales by day', 'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], 'values' => [22, 24, 21, 26, 31, 38, 23], 'mark' => 5]]],
                    [['table', ['cols' => ['Top items', 'Sold'], 'w' => '3fr 1fr', 'num' => [1], 'rows' => [
                        ['Rice, 5 kg', '148'], ['Canned tuna', '131'], ['Instant coffee', '96'], ['Bottled water, 6s', '88'],
                    ]]]],
                ]]],
            ],
        ],
    ]],

    // ------------------------------------------------------------------ RTW
    'ready-to-wear-business' => ['screens' => [
        [
            'label' => 'Variant stock', 'app' => 'RTW Business', 'nav' => $nav['rtw'], 'active' => 1,
            'title' => 'Variant stock', 'meta' => 'Style RTW-0218, linen button-down',
            'blocks' => [
                ['cols', ['w' => '3fr 1.3fr', 'items' => [
                    [['matrix', ['cols' => ['XS', 'S', 'M', 'L', 'XL'], 'rows' => [
                        ['White', '#F2F0EA', [4, 12, 9, 6, 2]],
                        ['Navy', '#1F2F4A', [0, 7, 14, 8, 3]],
                        ['Olive', '#6B7350', [2, 5, 0, 4, 1]],
                        ['Sand', '#CDB894', [6, 10, 11, 3, 0]],
                    ]]]],
                    [['kv', [['Price', '890.00'], ['On hand', '107 pcs'], ['Sold out', '3 variants'], ['Low', '5 variants']]]],
                ]]],
            ],
        ],
        [
            'label' => 'Orders', 'app' => 'RTW Business', 'nav' => $nav['rtw'], 'active' => 4,
            'title' => 'Customer orders', 'meta' => '7 open', 'action' => 'New order',
            'blocks' => [
                ['board', [
                    ['Reserved', [['ORD-0931', 'RTW-0218 Navy, M and L', 'warn:Unpaid'], ['ORD-0934', 'RTW-0105 Black, S', 'ok:Paid'], ['ORD-0936', 'RTW-0311 Sand, M', 'ok:Paid']]],
                    ['Packed', [['ORD-0927', 'RTW-0218 White, S, 2 pcs', 'ok:Paid'], ['ORD-0929', 'RTW-0240 Olive, L', 'ok:Paid']]],
                    ['Released', [['ORD-0922', 'RTW-0105 Black, M', 'mute:Pickup'], ['ORD-0925', 'RTW-0311 Sand, S', 'mute:Courier']]],
                ]],
            ],
        ],
        [
            'label' => 'Stock movement', 'app' => 'RTW Business', 'nav' => $nav['rtw'], 'active' => 2,
            'title' => 'Stock movement', 'meta' => 'This week',
            'blocks' => [
                ['table', ['cols' => ['Date', 'Reference', 'Style', 'Variant', 'Qty', 'Type'], 'w' => '.9fr 1.2fr 1.1fr 1.3fr .7fr 1.3fr', 'num' => [4], 'rows' => [
                    ['Jan 12', 'RCV-0088', 'RTW-0218', 'White / M', '+12', 'ok:Received'],
                    ['Jan 12', 'SAL-2204', 'RTW-0218', 'Navy / L', '-1', 'info:Sale'],
                    ['Jan 13', 'TRF-0031', 'RTW-0105', 'Black / S', '-4', 'mute:Transfer'],
                    ['Jan 13', 'RET-0012', 'RTW-0311', 'Sand / M', '+1', 'mute:Return'],
                    ['Jan 14', 'ADJ-0007', 'RTW-0240', 'Olive / M', '-1', 'warn:Adjustment'],
                ]]],
            ],
        ],
    ]],

    // ---------------------------------------------------------------- hotel
    'hotel-property-management' => ['screens' => [
        [
            'label' => 'Room rack', 'app' => 'Hotel', 'nav' => $nav['htl'], 'active' => 2,
            'title' => 'Room rack', 'meta' => 'Tonight: 18 of 24 occupied',
            'blocks' => [
                ['stats', [['Arrivals', '5'], ['Departures', '4'], ['In house', '18'], ['Vacant, clean', '3']]],
                ['tiles', ['n' => 6, 'items' => [
                    ['101', 'ok:Vacant'], ['102', 'info:Occupied'], ['103', 'info:Occupied'], ['104', 'warn:Reserved'], ['105', 'info:Occupied'], ['106', 'mute:Cleaning'],
                    ['201', 'info:Occupied'], ['202', 'ok:Vacant'], ['203', 'info:Occupied'], ['204', 'info:Occupied'], ['205', 'bad:Out of service'], ['206', 'warn:Reserved'],
                ]]],
            ],
        ],
        [
            'label' => 'Reservations', 'app' => 'Hotel', 'nav' => $nav['htl'], 'active' => 0,
            'title' => 'Reservation calendar', 'meta' => 'January 12 to 18', 'action' => 'New reservation',
            'blocks' => [
                ['grid', ['cols' => ['12', '13', '14', '15', '16', '17', '18'], 'rows' => [
                    ['101', '..bbb..'], ['102', 'bbbb...'], ['103', 'bb..ppp'], ['104', '.pp....'],
                    ['201', 'bbbbb..'], ['202', '....bbb'], ['203', 'bbb....'], ['205', 'xxxxxxx'],
                ], 'legend' => ['b' => 'Confirmed', 'p' => 'Tentative', 'x' => 'Out of service']]],
            ],
        ],
        [
            'label' => 'Guest account', 'app' => 'Hotel', 'nav' => $nav['htl'], 'active' => 4,
            'title' => 'Guest account', 'meta' => 'Stay S-2291, Room 203', 'action' => 'Check out',
            'blocks' => [
                ['cols', ['w' => '1.2fr 2fr', 'items' => [
                    [['kv', [['Arrival', 'Jan 11'], ['Departure', 'Jan 14'], ['Nights', '3'], ['Guests', '2 adults'], ['Room type', 'Deluxe']]]],
                    [['table', ['cols' => ['Date', 'Charge', 'Amount'], 'w' => '1fr 2fr 1.2fr', 'num' => [2], 'rows' => [
                        ['Jan 11', 'Room, deluxe', '2,800.00'],
                        ['Jan 12', 'Room, deluxe', '2,800.00'],
                        ['Jan 12', 'Extra bed', '500.00'],
                        ['Jan 13', 'Room, deluxe', '2,800.00'],
                        ['Jan 13', 'Laundry', '350.00'],
                        ['', 'Total charges', '9,250.00', 'total' => 1],
                    ]]]],
                ]]],
            ],
        ],
    ]],

    // ----------------------------------------------------------- restaurant
    'restaurant-management' => ['screens' => [
        [
            'label' => 'Floor', 'app' => 'Restaurant', 'nav' => $nav['rst'], 'active' => 0,
            'title' => 'Floor', 'meta' => 'Dinner shift, 9 of 15 tables in use',
            'blocks' => [
                ['cols', ['w' => '3fr 2fr', 'items' => [
                    [['tiles', ['n' => 5, 'items' => [
                        ['T1', 'info:Occupied', '4 guests'], ['T2', 'ok:Free'], ['T3', 'info:Occupied', '2 guests'], ['T4', 'warn:Billed', '3 guests'], ['T5', 'ok:Free'],
                        ['T6', 'info:Occupied', '4 guests'], ['T7', 'info:Occupied', '6 guests'], ['T8', 'ok:Free'], ['T9', 'warn:Billed', '2 guests'], ['T10', 'info:Occupied', '5 guests'],
                    ]]]],
                    [['ticket', ['title' => 'Table 6, order 0218', 'lines' => [
                        ['2 Grilled chicken', '560.00'], ['1 Beef stew', '340.00'],
                        ['1 Garlic shrimp', '390.00'], ['4 Steamed rice', '160.00'], ['1 Iced tea, pitcher', '180.00'],
                    ], 'total' => '1,630.00', 'button' => 'Send to kitchen']]],
                ]]],
            ],
        ],
        [
            'label' => 'Kitchen', 'app' => 'Restaurant', 'nav' => $nav['rst'], 'active' => 2,
            'title' => 'Kitchen tickets', 'meta' => '7 open',
            'blocks' => [
                ['board', [
                    ['Queued', [['T7, 19:04', '3 Fried rice, 2 Vegetable soup'], ['T10, 19:06', '2 Grilled chicken, 1 Beef stew']]],
                    ['Preparing', [['T6, 18:58', '2 Grilled chicken, 1 Garlic shrimp', 'warn:9 min'], ['T1, 19:01', '1 Beef stew, 4 Steamed rice', 'info:6 min'], ['T3, 19:02', '2 Vegetable soup', 'info:5 min']]],
                    ['Ready to serve', [['T4, 18:51', '1 Fried rice'], ['T9, 18:55', '2 Garlic shrimp']]],
                ]],
            ],
        ],
        [
            'label' => 'Billing', 'app' => 'Restaurant', 'nav' => $nav['rst'], 'active' => 3,
            'title' => 'Bill, table 4', 'meta' => 'Order 0214, 3 guests',
            'blocks' => [
                ['cols', ['w' => '2fr 1.4fr', 'items' => [
                    [['table', ['cols' => ['Qty', 'Item', 'Amount'], 'w' => '.5fr 3fr 1.2fr', 'num' => [2], 'rows' => [
                        ['1', 'Fried rice, platter', '260.00'],
                        ['2', 'Grilled chicken', '560.00'],
                        ['1', 'Vegetable soup', '190.00'],
                        ['3', 'Iced tea', '165.00'],
                        ['', 'Subtotal', '1,175.00', 'total' => 1],
                    ]]]],
                    [['ticket', ['title' => 'Payment', 'lines' => [['Subtotal', '1,175.00'], ['Service charge, 5%', '58.75']],
                        'total' => '1,233.75', 'total_label' => 'Amount due', 'after' => [['Cash received', '1,300.00'], ['Change', '66.25']], 'button' => 'Print receipt']]],
                ]]],
            ],
        ],
    ]],

    // --------------------------------------------------------------- coffee
    'coffee-shop-management' => ['screens' => [
        [
            'label' => 'Counter', 'app' => 'Coffee Shop', 'nav' => $nav['cof'], 'active' => 0,
            'title' => 'Counter', 'meta' => 'Order 57',
            'blocks' => [
                ['cols', ['w' => '3fr 2fr', 'items' => [
                    [['items', [
                        ['Americano', '110.00'], ['Latte', '140.00'], ['Cappuccino', '140.00'],
                        ['Mocha', '155.00'], ['Cold brew', '130.00'], ['Matcha latte', '160.00'],
                        ['Croissant', '95.00'], ['Banana bread', '85.00'], ['Cookie', '60.00'],
                    ]]],
                    [['ticket', ['title' => 'Order 57, for pickup', 'lines' => [
                        ['1 Latte, large', '160.00'], ['   Oat milk', '30.00'], ['1 Cold brew', '130.00'], ['1 Croissant', '95.00'],
                    ], 'total' => '415.00', 'button' => 'Take payment']]],
                ]]],
            ],
        ],
        [
            'label' => 'Queue', 'app' => 'Coffee Shop', 'nav' => $nav['cof'], 'active' => 1,
            'title' => 'Preparation queue', 'meta' => '6 orders',
            'blocks' => [
                ['board', [
                    ['New', [['57, counter', 'Latte large, Cold brew, Croissant'], ['D-12, delivery', '2 Mocha, 1 Banana bread']]],
                    ['Preparing', [['55, counter', '2 Americano', 'info:2 min'], ['56, counter', 'Matcha latte, Cookie', 'info:1 min']]],
                    ['Ready', [['54, counter', 'Cappuccino'], ['D-11, delivery', '3 Cold brew', 'warn:Waiting for rider']]],
                ]],
            ],
        ],
        [
            'label' => 'Inventory', 'app' => 'Coffee Shop', 'nav' => $nav['cof'], 'active' => 4,
            'title' => 'Ingredients', 'meta' => 'Updated as items are sold',
            'blocks' => [
                ['table', ['cols' => ['Ingredient', 'On hand', 'Used today', 'Reorder at', 'Status'], 'w' => '2fr 1fr 1fr 1fr 1.2fr', 'num' => [1, 2, 3], 'rows' => [
                    ['Espresso beans', '6.4 kg', '1.1 kg', '3 kg', 'ok:In stock'],
                    ['Fresh milk', '18 L', '9.5 L', '20 L', 'warn:Low'],
                    ['Oat milk', '4 L', '1.5 L', '3 L', 'ok:In stock'],
                    ['Chocolate syrup', '2.1 L', '0.4 L', '1 L', 'ok:In stock'],
                    ['Matcha powder', '0.2 kg', '0.1 kg', '0.3 kg', 'bad:Reorder'],
                    ['Cups, 12 oz', '340 pcs', '86 pcs', '200 pcs', 'ok:In stock'],
                ]]],
            ],
        ],
    ]],

    // -------------------------------------------------------------- laundry
    'laundry-management' => ['screens' => [
        [
            'label' => 'Order board', 'app' => 'Laundry', 'nav' => $nav['lau'], 'active' => 1,
            'title' => 'Orders', 'meta' => '11 in progress', 'action' => 'New order',
            'blocks' => [
                ['board', [
                    ['Received', [['L-1045', '6.0 kg wash, dry, fold', 'mute:Due today, 5 PM'], ['L-1046', '7.5 kg and 1 comforter', 'mute:Due tomorrow']]],
                    ['Washing', [['L-1043', '4.5 kg wash, dry, fold', 'mute:Due today, 3 PM'], ['L-1044', '9.0 kg wash and dry', 'mute:Due today, 4 PM']]],
                    ['Folding', [['L-1041', '5.0 kg wash, dry, fold', 'warn:Due in 20 min']]],
                    ['Ready for claim', [['L-1038', '8.0 kg, shelf B2', 'ok:Paid'], ['L-1039', '3.5 kg, shelf B3', 'warn:Balance 122.50']]],
                ]],
            ],
        ],
        [
            'label' => 'Intake', 'app' => 'Laundry', 'nav' => $nav['lau'], 'active' => 0,
            'title' => 'New order', 'meta' => 'Claim no. L-1046',
            'blocks' => [
                ['cols', ['w' => '1fr 1fr', 'items' => [
                    [['kv', [['Customer', 'C-0388'], ['Weight', '7.5 kg'], ['Items by piece', '1 comforter'], ['Due', 'Tomorrow, 10 AM'], ['Claim shelf', 'Assigned when ready']]]],
                    [['ticket', ['title' => 'Services', 'lines' => [
                        ['Wash, dry, fold, 7.5 kg', '262.50'], ['Fabric conditioner', '20.00'], ['Comforter, 1 pc', '180.00'],
                    ], 'total' => '462.50', 'button' => 'Save and print stub']]],
                ]]],
            ],
        ],
        [
            'label' => 'Payments', 'app' => 'Laundry', 'nav' => $nav['lau'], 'active' => 3,
            'title' => 'Payments', 'meta' => 'Today',
            'blocks' => [
                ['stats', [['Orders', '31'], ['Collected', '9,840.00'], ['Unpaid balance', '1,265.00'], ['Unclaimed, over 7 days', '4']]],
                ['table', ['cols' => ['Claim no.', 'Services', 'Amount', 'Paid', 'Status'], 'w' => '1fr 2.2fr 1fr 1fr 1.3fr', 'num' => [2, 3], 'rows' => [
                    ['L-1038', '8.0 kg wash, dry, fold', '280.00', '280.00', 'ok:Paid'],
                    ['L-1039', '3.5 kg wash, dry, fold', '122.50', '0.00', 'warn:On release'],
                    ['L-1041', '5.0 kg wash, dry, fold', '175.00', '175.00', 'ok:Paid'],
                    ['L-1046', '7.5 kg and 1 comforter', '462.50', '200.00', 'info:Partial'],
                ]]],
            ],
        ],
    ]],

    // ------------------------------------------------------------- car wash
    'car-wash-management' => ['screens' => [
        [
            'label' => 'Bays', 'app' => 'Car Wash', 'nav' => $nav['car'], 'active' => 1,
            'title' => 'Bays', 'meta' => '3 of 4 in use, 2 waiting',
            'blocks' => [
                ['tiles', ['n' => 4, 'items' => [
                    ['Bay 1', 'info:Wash and wax', 'Sedan, 18 min'], ['Bay 2', 'info:Full detail', 'SUV, 52 min'],
                    ['Bay 3', 'ok:Free'], ['Bay 4', 'warn:Finishing', 'Pickup, 31 min'],
                ]]],
                ['table', ['cols' => ['Job', 'Vehicle', 'Package', 'Waiting', 'Next bay'], 'w' => '1fr 1.2fr 2fr 1fr 1.2fr', 'rows' => [
                    ['J-0232', 'Hatchback', 'Basic wash', '4 min', 'ok:Bay 3'],
                    ['J-0233', 'SUV', 'Wash and vacuum', '1 min', 'mute:Queued'],
                ]]],
            ],
        ],
        [
            'label' => 'Jobs today', 'app' => 'Car Wash', 'nav' => $nav['car'], 'active' => 4,
            'title' => 'Jobs today', 'meta' => 'Collection',
            'blocks' => [
                ['stats', [['Jobs', '27'], ['Average time', '34 min'], ['Collected', '8,930.00']]],
                ['table', ['cols' => ['Job', 'Vehicle', 'Package', 'Bay', 'Time', 'Amount', 'Status'], 'w' => '.9fr 1.1fr 1.8fr .6fr .9fr 1fr 1.1fr', 'num' => [5], 'rows' => [
                    ['J-0226', 'Sedan', 'Basic wash', '1', '22 min', '180.00', 'ok:Paid'],
                    ['J-0227', 'SUV', 'Wash and wax', '3', '41 min', '450.00', 'ok:Paid'],
                    ['J-0228', 'Motorcycle', 'Basic wash', '4', '12 min', '90.00', 'ok:Paid'],
                    ['J-0229', 'Pickup', 'Wash and vacuum', '4', '31 min', '320.00', 'warn:Unpaid'],
                    ['J-0230', 'SUV', 'Full detail', '2', '52 min', '1,800.00', 'info:In bay'],
                ]]],
            ],
        ],
        [
            'label' => 'Supplies', 'app' => 'Car Wash', 'nav' => $nav['car'], 'active' => 3,
            'title' => 'Supplies', 'meta' => 'This week',
            'blocks' => [
                ['cols', ['w' => '3fr 2fr', 'items' => [
                    [['table', ['cols' => ['Supply', 'On hand', 'Issued', 'Status'], 'w' => '2fr 1fr 1fr 1.2fr', 'num' => [1, 2], 'rows' => [
                        ['Car shampoo', '14 L', '9 L', 'ok:In stock'],
                        ['Wax', '3 L', '2.5 L', 'warn:Low'],
                        ['Tire black', '6 L', '1.5 L', 'ok:In stock'],
                        ['Microfiber cloths', '40 pcs', '12 pcs', 'ok:In stock'],
                    ]]]],
                    [['bars', ['label' => 'Jobs by day', 'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], 'values' => [19, 22, 18, 24, 27, 41, 36], 'mark' => 5]]],
                ]]],
            ],
        ],
    ]],

    // ----------------------------------------------------------- pickleball
    'pickleball-court-booking' => ['screens' => [
        [
            'label' => 'Court schedule', 'app' => 'Court Booking', 'nav' => $nav['pkl'], 'active' => 0,
            'title' => 'Court schedule', 'meta' => 'Saturday, January 17', 'action' => 'New booking',
            'blocks' => [
                ['grid', ['cols' => ['6a', '7', '8', '9', '10', '11', '12p', '1', '2', '3', '4', '5', '6', '7', '8', '9'], 'rows' => [
                    ['Court 1', 'bbbb..bb...bbbbb'],
                    ['Court 2', '.bbbb....ppbbbb.'],
                    ['Court 3', 'bb..bb..bb..bbbb'],
                    ['Court 4', 'xxxx...bb..pbbb.'],
                ], 'legend' => ['b' => 'Booked', 'p' => 'Held, unpaid', 'x' => 'Blocked']]],
                ['stats', [['Booked hours', '34 of 64'], ['Held, unpaid', '3'], ['Fees today', '13,600.00']]],
            ],
        ],
        [
            'label' => 'Reservations', 'app' => 'Court Booking', 'nav' => $nav['pkl'], 'active' => 1,
            'title' => 'Reservations', 'meta' => 'Saturday, January 17',
            'blocks' => [
                ['table', ['cols' => ['Booking', 'Court', 'Time', 'Reserved by', 'Fee', 'Status'], 'w' => '1fr .9fr 1.4fr 1.4fr 1fr 1.2fr', 'num' => [4], 'rows' => [
                    ['B-0712', 'Court 1', '6 to 8 AM', 'Group of 4', '800.00', 'ok:Paid'],
                    ['B-0713', 'Court 2', '7 to 9 AM', 'Group of 6', '800.00', 'ok:Paid'],
                    ['B-0719', 'Court 2', '3 to 5 PM', 'Group of 4', '800.00', 'warn:Held'],
                    ['B-0721', 'Court 1', '5 to 8 PM', 'League night', '1,200.00', 'info:Deposit'],
                    ['B-0724', 'Court 4', '5 to 6 PM', 'Group of 2', '400.00', 'warn:Held'],
                ]]],
            ],
        ],
        [
            'label' => 'Occupancy', 'app' => 'Court Booking', 'nav' => $nav['pkl'], 'active' => 4,
            'title' => 'Occupancy', 'meta' => 'This week',
            'blocks' => [
                ['cols', ['w' => '3fr 2fr', 'items' => [
                    [['bars', ['label' => 'Booked hours by day', 'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], 'values' => [22, 26, 24, 29, 31, 34, 33], 'mark' => 5]]],
                    [['table', ['cols' => ['Time band', 'Rate per hour'], 'w' => '2fr 1.2fr', 'num' => [1], 'rows' => [
                        ['6 AM to 4 PM', '300.00'], ['4 PM to 10 PM', '400.00'], ['Weekend, all day', '400.00'],
                    ]]]],
                ]]],
            ],
        ],
    ]],

    // ------------------------------------------------------------- shipping
    'shipping-logistics' => ['screens' => [
        [
            'label' => 'Shipments', 'app' => 'Shipping', 'nav' => $nav['shp'], 'active' => 0,
            'title' => 'Shipments', 'meta' => 'Open shipments', 'action' => 'Accept shipment',
            'blocks' => [
                ['stats', [['In transit', '86'], ['For release', '14'], ['Delivered this week', '212'], ['Delayed', '3']]],
                ['table', ['cols' => ['Reference', 'Origin', 'Destination', 'Packages', 'Status'], 'w' => '1.2fr 1.5fr 1.5fr .9fr 1.4fr', 'num' => [3], 'rows' => [
                    ['SH-240118', 'Main terminal', 'Island hub', '12', 'info:In transit'],
                    ['SH-240119', 'Main terminal', 'North branch', '3', 'info:In transit'],
                    ['SH-240107', 'North branch', 'Main terminal', '40', 'warn:Delayed'],
                    ['SH-240102', 'Island hub', 'Main terminal', '7', 'ok:For release'],
                    ['SH-240121', 'Main terminal', 'South depot', '1', 'mute:Accepted'],
                ]]],
            ],
        ],
        [
            'label' => 'Tracking', 'app' => 'Shipping', 'nav' => $nav['shp'], 'active' => 1,
            'title' => 'Shipment SH-240118', 'meta' => 'Main terminal to Island hub',
            'blocks' => [
                ['cols', ['w' => '3fr 2fr', 'items' => [
                    [['timeline', [
                        ['Accepted at Main terminal', 'Jan 10, 08:20', 'done'],
                        ['Loaded', 'Jan 10, 15:45', 'done'],
                        ['Departed', 'Jan 10, 19:00', 'done'],
                        ['Arrived at Island hub', 'Jan 12, 06:30', 'now'],
                        ['Out for delivery', '', 'todo'],
                        ['Released to consignee', '', 'todo'],
                    ]]],
                    [['kv', [['Shipper', 'C-0214'], ['Consignee', 'C-0377'], ['Packages', '12'], ['Weight', '184 kg'], ['Charges', '6,440.00'], ['Payment', 'ok:Paid']]]],
                ]]],
            ],
        ],
        [
            'label' => 'Charges', 'app' => 'Shipping', 'nav' => $nav['shp'], 'active' => 3,
            'title' => 'Charges', 'meta' => 'This week',
            'blocks' => [
                ['table', ['cols' => ['Reference', 'Freight', 'Other fees', 'Total', 'Status'], 'w' => '1.3fr 1fr 1fr 1fr 1.2fr', 'num' => [1, 2, 3], 'rows' => [
                    ['SH-240118', '5,890.00', '550.00', '6,440.00', 'ok:Paid'],
                    ['SH-240119', '1,240.00', '150.00', '1,390.00', 'warn:Collect on release'],
                    ['SH-240107', '18,600.00', '1,400.00', '20,000.00', 'info:On account'],
                    ['SH-240102', '3,150.00', '250.00', '3,400.00', 'ok:Paid'],
                    ['', '28,880.00', '2,350.00', '31,230.00', 'total' => 1],
                ]]],
            ],
        ],
    ]],

    // --------------------------------------------------------- construction
    'construction-management' => ['screens' => [
        [
            'label' => 'Schedule', 'app' => 'Construction', 'nav' => $nav['con'], 'active' => 1,
            'title' => 'Two-storey warehouse', 'meta' => 'Project P-014, week 9 of 22',
            'blocks' => [
                ['stats', [['Planned progress', '41%'], ['Actual progress', '38%'], ['Budget used', '35%']]],
                ['gantt', ['cols' => ['W5', 'W6', 'W7', 'W8', 'W9', 'W10', 'W11', 'W12', 'W13', 'W14'], 'rows' => [
                    ['Foundation', 0, 3, 100],
                    ['Columns and beams', 2, 4, 80],
                    ['Ground floor slab', 4, 3, 30],
                    ['Second floor framing', 6, 3, 0],
                    ['Roofing', 8, 2, 0],
                ]]],
            ],
        ],
        [
            'label' => 'Materials', 'app' => 'Construction', 'nav' => $nav['con'], 'active' => 2,
            'title' => 'Material requests', 'meta' => 'Project P-014', 'action' => 'New request',
            'blocks' => [
                ['table', ['cols' => ['Request', 'Item', 'Qty', 'Needed', 'Status'], 'w' => '1fr 2.2fr 1.1fr 1fr 1.4fr', 'num' => [2], 'rows' => [
                    ['MR-0231', 'Cement, 40 kg', '320 bags', 'Jan 14', 'ok:Issued'],
                    ['MR-0232', 'Rebar, 16 mm', '180 pcs', 'Jan 14', 'ok:Delivered'],
                    ['MR-0233', 'Gravel', '24 cu m', 'Jan 16', 'warn:For approval'],
                    ['MR-0234', 'Formwork plywood', '90 sheets', 'Jan 19', 'info:Ordered'],
                    ['MR-0235', 'Tie wire', '12 rolls', 'Jan 19', 'warn:For approval'],
                ]]],
            ],
        ],
        [
            'label' => 'Budget', 'app' => 'Construction', 'nav' => $nav['con'], 'active' => 3,
            'title' => 'Expenses against budget', 'meta' => 'Project P-014',
            'blocks' => [
                ['table', ['cols' => ['Category', 'Budget', 'Spent', 'Remaining'], 'w' => '1.6fr 1.3fr 1.3fr 1.3fr', 'num' => [1, 2, 3], 'rows' => [
                    ['Materials', '4,200,000.00', '1,638,400.00', '2,561,600.00'],
                    ['Labor', '2,100,000.00', '742,500.00', '1,357,500.00'],
                    ['Equipment', '650,000.00', '214,000.00', '436,000.00'],
                    ['Subcontract', '900,000.00', '180,000.00', '720,000.00'],
                    ['Overhead', '350,000.00', '127,900.00', '222,100.00'],
                    ['Total', '8,200,000.00', '2,902,800.00', '5,297,200.00', 'total' => 1],
                ]]],
            ],
        ],
    ]],

    // ------------------------------------------------------- multi-business
    'multi-business-operations' => ['screens' => [
        [
            'label' => 'Group overview', 'app' => 'Multi-Business', 'nav' => $nav['mul'], 'active' => 0,
            'title' => 'Group overview', 'meta' => 'January to date',
            'blocks' => [
                ['cols', ['w' => '3fr 1.4fr', 'items' => [
                    [['table', ['cols' => ['Business', 'Sales', 'Expenses', 'Net'], 'w' => '1.8fr 1.2fr 1.2fr 1.2fr', 'num' => [1, 2, 3], 'rows' => [
                        ['A: Hardware supply', '842,300.00', '611,900.00', '230,400.00'],
                        ['B: Cafe', '318,650.00', '254,100.00', '64,550.00'],
                        ['C: Rentals', '196,000.00', '88,400.00', '107,600.00'],
                        ['Group total', '1,356,950.00', '954,400.00', '402,550.00', 'total' => 1],
                    ]]]],
                    [['bars', ['label' => 'Sales by business', 'labels' => ['A', 'B', 'C'], 'values' => [842, 319, 196]]]],
                ]]],
            ],
        ],
        [
            'label' => 'Businesses', 'app' => 'Multi-Business', 'nav' => $nav['mul'], 'active' => 1,
            'title' => 'Businesses', 'meta' => '3 registered', 'action' => 'Add business',
            'blocks' => [
                ['tiles', ['n' => 3, 'items' => [
                    ['A: Hardware supply', 'ok:Active', '12 users, 2 branches'],
                    ['B: Cafe', 'ok:Active', '6 users, 1 branch'],
                    ['C: Rentals', 'ok:Active', '3 users, 1 branch'],
                ]]],
                ['table', ['cols' => ['Business', 'Last transaction', 'Open items', 'Period'], 'w' => '2fr 1.4fr 1fr 1.2fr', 'rows' => [
                    ['A: Hardware supply', 'Today, 14:12', '8', 'ok:Open'],
                    ['B: Cafe', 'Today, 14:20', '2', 'ok:Open'],
                    ['C: Rentals', 'Yesterday', '0', 'mute:Closed'],
                ]]],
            ],
        ],
        [
            'label' => 'User access', 'app' => 'Multi-Business', 'nav' => $nav['mul'], 'active' => 2,
            'title' => 'User access', 'meta' => '21 users',
            'blocks' => [
                ['table', ['cols' => ['User', 'Role', 'Business A', 'Business B', 'Business C'], 'w' => '.8fr 1.8fr 1fr 1fr 1fr', 'rows' => [
                    ['U-01', 'Group administrator', 'ok:Full', 'ok:Full', 'ok:Full'],
                    ['U-04', 'Group accountant', 'info:Reports', 'info:Reports', 'info:Reports'],
                    ['U-07', 'Business manager', 'ok:Full', 'mute:None', 'mute:None'],
                    ['U-12', 'Cashier', 'mute:None', 'info:Selling', 'mute:None'],
                    ['U-15', 'Staff', 'mute:None', 'mute:None', 'info:Bookings'],
                ]]],
            ],
        ],
    ]],

    // ------------------------------------------------------------ inventory
    'inventory-warehouse' => ['screens' => [
        [
            'label' => 'Stock on hand', 'app' => 'Inventory', 'nav' => $nav['inv'], 'active' => 0,
            'title' => 'Stock on hand', 'meta' => 'Main warehouse',
            'blocks' => [
                ['stats', [['Products', '2,318'], ['Below reorder level', '41'], ['Receipts pending', '6']]],
                ['table', ['cols' => ['Code', 'Product', 'Location', 'On hand', 'Reorder at', 'Status'], 'w' => '1fr 2.2fr 1fr 1fr 1fr 1.2fr', 'num' => [3, 4], 'rows' => [
                    ['P-10442', 'PVC pipe, 1/2 in', 'A-03-2', '860', '300', 'ok:In stock'],
                    ['P-10518', 'Elbow fitting, 1/2 in', 'A-03-4', '240', '250', 'warn:Reorder'],
                    ['P-20077', 'Paint, white, 4 L', 'B-01-1', '96', '40', 'ok:In stock'],
                    ['P-20131', 'Paint thinner, 1 L', 'B-01-3', '12', '30', 'warn:Reorder'],
                    ['P-30902', 'Wood screws, box', 'C-02-6', '0', '50', 'bad:Out'],
                ]]],
            ],
        ],
        [
            'label' => 'Receiving', 'app' => 'Inventory', 'nav' => $nav['inv'], 'active' => 2,
            'title' => 'Receiving RR-0457', 'meta' => 'Supplier S-032, order PO-1184', 'action' => 'Post receipt',
            'blocks' => [
                ['table', ['cols' => ['Product', 'Ordered', 'Received', 'Variance', 'Status'], 'w' => '2.4fr 1fr 1fr 1fr 1.3fr', 'num' => [1, 2, 3], 'rows' => [
                    ['Elbow fitting, 1/2 in', '500', '500', '0', 'ok:Complete'],
                    ['Paint thinner, 1 L', '120', '96', '-24', 'warn:Short'],
                    ['Wood screws, box', '200', '200', '0', 'ok:Complete'],
                    ['Sandpaper, sheet', '300', '310', '+10', 'info:Over'],
                ]]],
            ],
        ],
        [
            'label' => 'Reorder list', 'app' => 'Inventory', 'nav' => $nav['inv'], 'active' => 4,
            'title' => 'Reorder list', 'meta' => '41 products at or below reorder level',
            'blocks' => [
                ['cols', ['w' => '3fr 1.5fr', 'items' => [
                    [['table', ['cols' => ['Product', 'On hand', 'Reorder at', 'Supplier'], 'w' => '2.2fr 1fr 1fr 1fr', 'num' => [1, 2], 'rows' => [
                        ['Wood screws, box', '0', '50', 'S-018'],
                        ['Paint thinner, 1 L', '12', '30', 'S-032'],
                        ['Elbow fitting, 1/2 in', '240', '250', 'S-032'],
                        ['Masking tape', '34', '60', 'S-011'],
                    ]]]],
                    [['bars', ['label' => 'Issued, last 6 weeks', 'labels' => ['W1', 'W2', 'W3', 'W4', 'W5', 'W6'], 'values' => [310, 284, 342, 298, 365, 331]]]],
                ]]],
            ],
        ],
    ]],
];
