<?php

return [
    'Free' => [
        'limit' => 50,
        'priority' => 1,
        'queue' => 'free',
    ],
    'Pro' => [
        'limit' => 100,
        'priority' => 2,
        'queue' => 'pro',
    ],
    'Enterprise' => [
        'limit' => 200,
        'priority' => 3,
        'queue' => 'enterprise',
    ],
];
