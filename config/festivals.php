<?php

return [
    /*
    Festival definitions. Each festival can be one of:
      - 'date' => 'YYYY-MM-DD' (explicit date)
      - 'dates' => ['YYYY-MM-DD', ...] (explicit dates)
      - 'month_day' => 'MM-DD' (recurring annual date)

    Options:
      - 'message' => the greeting text (supports {name} placeholder)
      - 'send_before_days' => integer, send that many days before date (default 0)
    */

    'diwali' => [
        'name' => 'Diwali',
        // Example: for festivals that vary by year, prefer 'dates' with explicit YYYY-MM-DD entries
        'dates' => [
            // add upcoming years here
            '2026-11-01',
        ],
        'message' => "Happy Diwali! Wishing you and your family joy and prosperity. — Fund Manager",
        'send_before_days' => 0,
    ],

    'holi' => [
        'name' => 'Holi',
        // Holi typically varies, but you can use month_day if you want a recurring day
        'month_day' => '03-25',
        'message' => "Happy Holi! Enjoy the colours and celebrations. — Fund Manager",
        'send_before_days' => 0,
    ],

    // Add custom festival
    'new_year' => [
        'name' => "New Year's Day",
        'month_day' => '01-01',
        'message' => "Happy New Year! Wishing you a successful year ahead. — Fund Manager",
        'send_before_days' => 0,
    ],
];
