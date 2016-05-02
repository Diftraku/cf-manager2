<?php
/**
 * cf-manager2 - dist config
 * @package cf-manager2
 * @copyright Copyright (c) 2016, Diftraku
 * @author diftraku
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

// Define defaults if they are not already defined
// Blatantly re-using from Shimmie: https://github.com/shish/shimmie2/blob/master/core/sys_config.inc.php#L23
function _d($name, $value) {if(!defined($name)) define($name, $value);}

_d('CF_DB_DSN',         'sqlite:' . CF_DIR_DATA . 'cfmanage2.db');  // PDO DSN
_d('CF_DB_USERNAME',    '');                                        // Database username
_d('CF_DB_PASSWORD',    '');                                        // Database password
_d('CF_DB_TBL_PREFIX',  'cfm_');                                    // Database password
_d('CF_VERSION',        '0.0.1');                                   // System version
_d('CF_BUILD',          'DEV');                                     // Build type
_d('CF_TZ',             'UTC');                                     // Default timezone
_d('CF_HASH_ALGO',      'SHA256');                                  // Default hashing algorithm
_d('CF_PASS_ALGO',      PASSWORD_BCRYPT);                           // Default password_hash() algorithm
_d('CF_PROXY_CHECKIP',  true);                                      // Check for Proxy IPs

_d('CF_AUTH_HTTP',  true);                                          // Should we use HTTP auth?
_d('CF_AUTH_HTTP_REALM',  'CF-Manager 2');                          // HTTP Authentication realm

// Default overrides for Slim Framework
// These are merged in public/index.php before instantiation
$settings = [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production

        // Renderer settings
        'renderer' => [
            'template_path' => CF_DIR_TMPL,
        ],

        // Monolog settings
        'logger' => [
            'name' => 'cfm',
            'path' => CF_DIR_LOGS . 'app.log',
        ],
    ],
];