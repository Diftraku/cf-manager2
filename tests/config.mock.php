<?php
define('CF_DB_FILENAME', CF_DIR_DATA . 'cfmanage2.test.db');
define('CF_DB_DSN', 'sqlite:' . CF_DB_FILENAME);
define('CF_LOG_FILENAME', 'test.log');
define('CF_LOG_LEVEL', Monolog\Logger::DEBUG);
define('CF_INSTANCE_NAME', 'TEST');