<?php

namespace phpDbMigrations\bootstrap;

error_reporting(E_STRICT|E_ALL);
ini_set('display_errors', 'on');

/**
 * Path to the directory inside which the migrations code (php files)
 * will be located
 */
define('MIGRATIONS_DIR', '/home/vineet/php/migration-test/migrations');

/**
 * Path to the file in which the db config for the application will be 
 * specified.
 */
define('DB_CONFIG_PATH', '/home/vineet/php/migration-test/db_config.php');

require_once(DB_CONFIG_PATH);

define('DB_HOST', DB_HOSTNAME);
define('DB_USER', DB_USERNAME);
define('DB_PASS', DB_PASSWORD);
define('DB_NAME', DB_DATABASE);
define('DB_PRE', DB_PREFIX);

/**
 * Name of the migrations history table
 */
define('HISTORY_TABLENAME', DB_PRE . 'db_migrationhistory');

/**
 * Autoload the library
 */
require(dirname(__FILE__) . '/lib/autoload.php');

