<?php
/**
 * cf-manager2
 * @package cf-manager2
 * @copyright Copyright (c) 2016, Diftraku
 * @author diftraku
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */
// Set up the database connection
define('CF_DB_TYPE', explode(':', CF_DB_DSN)[0]);
if (in_array(CF_DB_TYPE, ['pgsql','mysql','cubrid'])) {
    // PostgreSQL, MySQL and CUBRID require username and password
    R::setup(CF_DB_DSN, CF_DB_USERNAME, CF_DB_PASSWORD);
}
else {
    // The rest (ie. SQLite) does not require creds
    R::setup(CF_DB_DSN);
}
// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middleware
require __DIR__ . '/../src/middleware.php';

// Register routes
require __DIR__ . '/../src/routes.php';