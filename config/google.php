<?php

return [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect_uri' => env('GOOGLE_REDIRECT_CALENDAR_URI'),

    'scopes' => [
        'https://www.googleapis.com/auth/calendar',
        'https://www.googleapis.com/auth/calendar.events',
    ],

    'access_type' => 'offline',
    'approval_prompt' => 'consent',
];