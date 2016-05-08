<?php
/**
 * cf-manager2
 * @package cf-manager2
 * @copyright Copyright (c) 2016, Diftraku
 * @author diftraku
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

// Report all errors during tests
error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Set a sane timezone
date_default_timezone_set('UTC');

use There4\Slim\Test\WebTestCase;

define('PROJECT_ROOT', realpath(__DIR__ . '/..'));

// Require dependencies and defaults
require_once PROJECT_ROOT . '/vendor/autoload.php';
require_once PROJECT_ROOT . '/data/defaults.php';
require_once CF_DIR_LIB . 'rb.php';

// Initiate a local copy of the app under test
//class LocalWebTestCase extends WebTestCase {
//    public function getSlimInstance() {
        require_once PROJECT_ROOT . '/tests/config.mock.php';
        require_once CF_DIR_DATA . 'config.dist.php';
        session_start();

        // Instantiate the app
        $settings_default = require PROJECT_ROOT . '/src/settings.php';
        if (isset($settings)) {
            // Merge settings
            $settings = array_merge($settings_default, $settings);
        }
        else {
            $settings = $settings_default;
        }
        $app = new \Slim\App($settings);
        return $app;
//    }
//};
