<?php

namespace phpDbMigrations\lib\db;

use phpDbMigrations\lib\helpers;


class MysqlException extends \Exception {} 


class PDOWrapper {
    private static $_instance;

    public static function instance() {
        if (self::$_instance == null) {
            self::$_instance = new PDOWrapper();
        }
        return self::$_instance;
    }

    public $obj;

    public function __construct() {
        $this->obj = new \PDO(sprintf('mysql:host=%s;dbname=%s;charset=UTF8', DB_HOST, DB_NAME),
                              DB_USER, 
                              DB_PASS, 
                              array(
                                  \PDO::ATTR_EMULATE_PREPARES => false, 
                                  \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
                              ));
    }

    /**
     * Helper function for executing a single query and saving all the
     * boilerplate code in migrations
     */
    public static function exec_query($sql) {
        $db = self::instance()->obj;
        try {
            $db->beginTransaction();            
            $db->exec($sql);
            $db->commit();
            helpers\printout('Ok');
        } catch (PDOException $e) {
            //Something went wrong rollback!
            $db->rollBack();
            throw new MysqlException($e->getMessage());
        }
    }
}

