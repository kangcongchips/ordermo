<?php

$path      = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$publicDir = realpath(__DIR__ . '/public');
$file      = realpath($publicDir . $path);

// Serve real static assets from public/ directly, with correct MIME types,
// regardless of which directory `php -S` was launched from.
if (
    $path !== '/'
    && $file !== false
    && is_file($file)
    && str_starts_with($file, $publicDir)
) {
    $mimes = [
        'css'   => 'text/css',
        'js'    => 'application/javascript',
        'svg'   => 'image/svg+xml',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'webp'  => 'image/webp',
        'ico'   => 'image/x-icon',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'json'  => 'application/json',
    ];
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if (isset($mimes[$ext])) {
        header('Content-Type: ' . $mimes[$ext]);
    }
    readfile($file);
    return true;
}

$_GET['url'] = ltrim($path, '/');
require __DIR__ . '/public/index.php';
