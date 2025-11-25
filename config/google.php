<?php

return [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect_uri' => env('GOOGLE_REDIRECT_URI', 'http://localhost:8021/admin/google/callback'),
    
    'scopes' => [
        'https://www.googleapis.com/auth/calendar',
        'https://www.googleapis.com/auth/calendar.events'
    ],
    
    'access_type' => 'offline',
    'approval_prompt' => 'auto', // Cambiar de 'force' a 'auto'
];