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
 * Define constants for the database config.
 *
 * If your project has a database config file then use it by requiring it
 * and setting the PDBM_* constants below using those constants. 
 *
 * If there for any reason you cannot require the file where the db config
 * is defined say for eg. that file also executes some php code then 
 * define them here again. 
 * 
 * In short choose the best approach depending on your project
 */

require('/home/vineet/php/migration-test/db_config.php');

define('PDBM_HOST', DB_HOSTNAME);
define('PDBM_USER', DB_USERNAME);
define('PDBM_PASS', DB_PASSWORD);
define('PDBM_NAME', DB_DATABASE);
define('PDBM_PRE', DB_PREFIX);

/**
 * Name of the migrations history table
 */
define('HISTORY_TABLENAME', PDBM_PRE . 'db_migrationhistory');

/**
 * Autoload the library
 */
require(dirname(__FILE__) . '/lib/autoload.php');

