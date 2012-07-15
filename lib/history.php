<?php

namespace phpDbMigrations\lib\history;

use phpDbMigrations\lib\db\PDOWrapper;
use phpDbMigrations\lib\helpers;


class MigrationDoesNotExist extends \Exception { }

/**
 * Function to create a new migration in the history
 *
 * @param String $migration
 */
function create($migration) {
    $db = PDOWrapper::instance()->obj;
    try {
        $db->beginTransaction();
        $db->exec("INSERT INTO " . HISTORY_TABLENAME . " SET migration = '" . $migration . "'");
        $db->commit();
    } catch (\PDOException $e) {
        //Something went wrong rollback!
        $db->rollBack();
        \helpers\printout($e->getMessage());
    }
}
 
 
/**
 * Function to get a migration from the history
 *
 * @param String $migration
 * @return Array row of the table
 * @raises MigrationDoesNotExist exception if migration not found
 */
function find($migration) {
    $db = PDOWrapper::instance()->obj;
    $sql = "SELECT * FROM " . HISTORY_TABLENAME . " WHERE migration = '" . $migration . "' LIMIT 1";
    $stmt = $db->query($sql);
    $result = $stmt->fetch(\PDO::FETCH_ASSOC);
    if (!$result) {
        throw new MigrationDoesNotExist('Migration not found in history stored in the db.');
    }
    return $result;
}
 

/**
 * Function to check whether a migration exists in the history
 * which means already migrated or not
 *
 * @param String $migration
 * @return Boolean
 */
function exists($migration) {
    try {
        return (bool) find($migration);
    } catch (MigrationDoesNotExist $e) {
        return false;
    }
}
 
 
/**
 * Function to get all migrations later than the one passed in as 
 * the argument.
 * Typically this will be used while backwards migration
 *
 * @param String $migration
 * @return Array
 */
function get_later_than($migration) {
    $db = PDOWrapper::instance()->obj;
    $migration = find($migration);
    $sql = "SELECT * FROM " . HISTORY_TABLENAME . " WHERE `id` > '" . (int)$migration['id'] . "'";
    $stmt = $db->query($sql);
    $later_migrations = array();
    while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
        $later_migrations[] = $row;
    }
    return $later_migrations;
}
 
 
/**
 * Function to delete a migration from the db
 *
 * @param String $migration
 */
function delete($migration) {
    $migration = find($migration);
    $db = PDOWrapper::instance()->obj;
    $count = $db->exec("DELETE FROM " . HISTORY_TABLENAME . " WHERE migration = '" . $migration['migration'] . "'");
}
 

/**
 * Function to check whether the migration table exists or not
 * If table does't exists it will be created.
 */
function check_migration_table() {
    try {
        $db = PDOWrapper::instance()->obj;
        $query = $db->query("SELECT 1 FROM " . HISTORY_TABLENAME);
        $check = $query->fetch();
    } catch (\Exception $e ) {
        try {
            helpers\printout("Table " . HISTORY_TABLENAME . " doesn't exist. " . "\n");
            helpers\printout("Creating table now..." . "\n");
            $count = $db->exec("CREATE TABLE IF NOT EXISTS `db_migrationhistory` (
                               `id` int(11) NOT NULL AUTO_INCREMENT,
                               `migration` varchar(255) NOT NULL,
                               `applied` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                               PRIMARY KEY (`id`),
                               UNIQUE KEY `migration` (`migration`)
                               ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;");
            helpers\printout($count  . "\n");
        } catch (\Exception $e ) {
            helpers\printout($e->getMessage());
            exit;
        }
    }
}