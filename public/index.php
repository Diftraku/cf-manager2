<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

// Fetch defaults and settings
require __DIR__ . '/../data/defaults.php';
if (file_exists(CF_DIR_DATA . 'config.php')) {
    // Override defaults and dist
    require CF_DIR_DATA . 'config.php';
}
require CF_DIR_DATA . 'config.dist.php';
require CF_DIR_LIB . 'rb.php';
session_start();

// Instantiate the app
$settings_default = require __DIR__ . '/../src/settings.php';
if (isset($settings)) {
    // Merge settings
    $settings = array_merge($settings_default, $settings);
}
else {
    $settings = $settings_default;
}
$app = new \Slim\App($settings);

// Require our middleware, dependencies, routes and the works
require CF_DIR_SRC . 'app.php';

// Run app
$app->run();
