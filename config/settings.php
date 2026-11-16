<?php

declare(strict_types=1);

$env = static function (string $key, ?string $default = null): ?string {
    $value = getenv($key);
    if ($value === false || $value === '') {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
    return $value;
};

return [
    'displayErrorDetails' => filter_var($env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOL),
    'db' => [
        'host' => $env('DB_HOST', '127.0.0.1'),
        'port' => (int) $env('DB_PORT', '3306'),
        'database' => $env('DB_DATABASE', 'db_csvbigly'),
        'username' => $env('DB_USERNAME', 'root'),
        'password' => $env('DB_PASSWORD', ''),
        'charset' => $env('DB_CHARSET', 'utf8mb4'),
    ],
    'export' => [
        'table' => $env('EXPORT_TABLE', 'orders'),
        'primary_key' => $env('EXPORT_PRIMARY_KEY', 'order_id'),
        'batch_size' => max(1, (int) $env('EXPORT_BATCH_SIZE', '1000')),
        'preview_limit' => max(1, (int) $env('PREVIEW_LIMIT', '10')),
    ],
];
