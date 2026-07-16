<?php

return [
    'ticket_reopen_days' => (int) env('TICKET_REOPEN_DAYS', 7),
    'ticket_max_reopens' => (int) env('TICKET_MAX_REOPENS', 2),
    'official_email_domain' => env('REXHALL_EMAIL_DOMAIN', 'rexhall.com'),
];
