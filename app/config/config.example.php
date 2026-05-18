<?php

// Copy this file to config.php and fill in your local values.
// config.php is gitignored so credentials stay out of version control.

define('BASE_URL', php_sapi_name() === 'cli-server' ? '/' : '/ordermo/public/');
define('APP_NAME', 'ordermo');

define('DB_HOST', 'localhost');
define('DB_PORT', 3306);
define('DB_NAME', 'ordermo');
define('DB_USER', 'root');
define('DB_PASS', 'your_mysql_password_here');
