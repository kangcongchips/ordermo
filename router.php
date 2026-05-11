<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path !== '/' && file_exists(__DIR__ . '/public' . $path)) {
    return false;
}

$_GET['url'] = ltrim($path, '/');
require __DIR__ . '/public/index.php';
