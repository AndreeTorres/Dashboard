<?php
// Router para el servidor embebido de PHP.
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$publicPath = __DIR__ . '/public' . $uri;

if ($uri !== '/' && file_exists($publicPath) && !is_dir($publicPath)) {
    return false; // sirve estático (CSS/JS/imágenes)
}

require_once __DIR__ . '/public/index.php';
