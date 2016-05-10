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

set_include_path(dirname(__FILE__) . '/../' . PATH_SEPARATOR . get_include_path());

// Prevent session cookies
ini_set('session.use_cookies', 0);

define('PROJECT_ROOT', realpath(__DIR__ . '/..'));

// Require dependencies and defaults
require_once PROJECT_ROOT . '/vendor/autoload.php';
require_once PROJECT_ROOT . '/data/defaults.php';
require_once CF_DIR_LIB . 'rb.php';
require_once PROJECT_ROOT . '/tests/config.mock.php';
require_once CF_DIR_DATA . 'config.dist.php';


session_start();
