<?php

return [
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: 3306,
        'dbname' => getenv('DB_NAME') ?: 'expenses_db',
        'user' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASS') ?: ''
    ]
];
