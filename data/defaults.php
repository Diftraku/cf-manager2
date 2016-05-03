<?php
/**
 * cf-manager2 - system defaults
 * @package cf-manager2
 * @copyright Copyright (c) 2016, Diftraku
 * @author diftraku
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

// Basic directories
define('CF_DIR_DATA', __DIR__ . '/../data/');
define('CF_DIR_LIB', __DIR__ . '/../lib/');
define('CF_DIR_LOGS', __DIR__ . '/../logs/');
define('CF_DIR_PUBLIC', __DIR__ . '/../public/');
define('CF_DIR_SRC', __DIR__ . '/../src/');
define('CF_DIR_TMPL', __DIR__ . '/../templates/');
define('CF_DIR_VEND', __DIR__ . '/../vendor/');

// Database table names
define('CF_DB_TBL_USERS', 'users');
define('CF_DB_TBL_TICKETS', 'tickets');
define('CF_DB_TBL_EVENTS', 'events');

// RedBean defaults
define('REDBEAN_MODEL_PREFIX', '\\CFM2\\Models\\' );
