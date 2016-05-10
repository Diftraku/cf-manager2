<?php
global $settings;
global $app;
$settings_default = require PROJECT_ROOT . '/src/settings.php';
if (isset($settings)) {
// Merge settings
$settings = array_merge($settings_default, $settings);
}
else {
$settings = $settings_default;
}

$app = new \Slim\App($settings);
require CF_DIR_SRC . 'app.php';