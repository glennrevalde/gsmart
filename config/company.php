<?php
/**
 * Business information shown across the site.
 *
 * Anything left as null is treated as "not supplied yet": in review mode it
 * appears as a labelled placeholder, and once review mode is off it is simply
 * left out. Never fill these with invented details.
 */

return [
    'brand'      => 'GSMaRT',
    'legal_name' => 'G-SMaRT Technologies Inc.',   // used in the footer copyright line; set to null to use the brand name
    'descriptor' => 'Business systems and software solutions',

    // Contact methods. Supply real values or leave null. The site shows no
    // phone number and no office hours.
    'email'   => 'vinxvadezxz@gmail.com',
    'address' => "Cordova Catholic School Multi-Purpose Cooperative (CCSMPC) Eduardo Nunez St., Poblacion, Cordova, Cebu",   // e.g. 'Building, Street, City, Province'

    // Shown in the browser tab and search results for the home page.
    'home_title'       => 'GSMaRT | Business systems built around the way you work',
    'meta_description' => 'GSMaRT develops software for managing daily operations: billing, inventory, workforce, reservations and reporting.',

    // Services listed on the Services page and the home page. Remove any that
    // GSMaRT does not offer; the page adjusts to what is left.
    'services' => [
        ['name' => 'Custom development',
         'text' => 'When none of the existing systems fits, we design and build one around your process: your documents, your approval steps, your reports.'],
        ['name' => 'Setup and configuration',
         'text' => 'An existing system is adjusted to your rates, forms, user roles and numbering before anyone starts using it.'],
        ['name' => 'Deployment',
         'text' => 'The system is installed where it will run and your starting records are loaded, so the first working day begins with real data.'],
        ['name' => 'Training',
         'text' => 'Each role is taught the screens it will use, with your own transactions as the examples.'],
        ['name' => 'Maintenance',
         'text' => 'Corrections, adjustments and updates as your operation changes after go-live.'],
        ['name' => 'Technical support',
         'text' => 'A point of contact for questions and problems that come up in daily use.'],
    ],

    // The engagement process. This is a PROPOSED process: confirm it with
    // GSMaRT before publishing.
    'process' => [
        ['name' => 'Discuss requirements',
         'text' => 'We ask how the work is done today: who records what, on which forms, and where it goes wrong.'],
        ['name' => 'Review the suitable system',
         'text' => 'You see the closest existing system in a demo, and we note what fits as it is and what does not.'],
        ['name' => 'Configure or develop',
         'text' => 'Rates, forms and roles are set up, and anything missing is built.'],
        ['name' => 'Deploy',
         'text' => 'The system is installed and loaded with your opening records.'],
        ['name' => 'Train',
         'text' => 'Staff learn their part of the system before it replaces the old process.'],
        ['name' => 'Support',
         'text' => 'We stay available for questions, fixes and changes.'],
    ],

    // "Built for real operations" on the home page.
    'operations' => [
        ['name' => 'Transactions',     'text' => 'Sales, payments, receipts and vouchers recorded once, at the point they happen.'],
        ['name' => 'Records',          'text' => 'Customers, students, guests, employees and items kept in one place with their history.'],
        ['name' => 'Workforce',        'text' => 'Attendance, leave and payroll computed from the same employee file.'],
        ['name' => 'Scheduling',       'text' => 'Rooms, courts, bays and tables assigned by time so nothing is double-booked.'],
        ['name' => 'Customer service', 'text' => 'Order and job status that front-line staff can look up while the customer waits.'],
        ['name' => 'Billing',          'text' => 'Bills and assessments generated from readings, rates and fee schedules.'],
        ['name' => 'Reporting',        'text' => 'Daily and periodic reports drawn from the transactions already entered.'],
    ],

    // Options in the inquiry form. Keys are stored in the database.
    'inquiry_types' => [
        'demo'     => 'Request a system demo',
        'question' => 'Ask about a system',
        'custom'   => 'Discuss custom development',
        'support'  => 'Support for an existing system',
        'other'    => 'Something else',
    ],
];
