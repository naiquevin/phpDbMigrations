<?php

namespace phpDbMigrations\manage;

use phpDbMigrations\lib\history;
use phpDbMigrations\lib\helpers;
use phpDbMigrations\lib\db;


require 'bootstrap.php';


function run_command($_argv) {
    $cmd = $_argv[1];
    $opts = array_slice($_argv, 2);
    
    if (!in_array($cmd, array('create', 'migrate'))) {
        throw new \Exception('Invalid Command');
    }

    if ($cmd === 'create') {
        if ($opts[0] === '-n') {
            create_migration_file($opts[1]);
            return;
        } else {
            throw new \Exception('Invalid Command');
        }
    }

    if ($cmd === 'migrate') {
        $n = array_search('-n', $opts);
        $name = $n != -1 ? $opts[$n+1] : null;
        $fake = array_search('--fake', $opts) >= 0;
        $recover = !$fake && array_search('--recover', $opts) >= 0;
        run_migration($name, $fake, $recover);
        return;
    }    
}


function create_migration_file($name) {
    $name = preg_replace('/[^a-zA-Z_-]/', '_', $name);
    $arr = array(
        '{{ timestamp }}' => time(),
        '{{ name }}' => $name,
    );
    $filename = str_replace(array_keys($arr), array_values($arr), '_{{ timestamp }}_{{ name }}');
    $file = MIGRATIONS_DIR . '/' . $filename.'.php';
    $fh = fopen($file, 'w') or die("can't open file");
    fwrite($fh, helpers\code_template($filename));
    fclose($fh);
    helpers\printout('Migration code generated in file - ' . $file);
}


function run_migrations($name, $fake, $recover) {

    // ensure that the migration history table has been created.
    history\check_migration_table();

    if ($name !== null) { 
        if (history\exists($name)) {
 
            // if migration name is supplied, check if the migration
            // is already run if yes, it means backwards migration and
            // we call the migrate_backwards function instead of
            // migrate_forwards
            migrate_backwards($name, $fake, $recover);
        } else {
            $files = array($name . '.php');
            migrate_forwards($files, $fake, $recover);
        }
    } else {
        $files = helpers\get_migration_files();
        migrate_forwards($files, $fake, $recover);
    }
}


/**
 * Function to run multiple migrations that are not applied
 */
function migrate_forwards($files, $fake, $recover) {
    foreach ($files as $f) {
        include $f;
        $ns = basename($f, ".php");
 
        // check if it's already migrated, if yes, just continue
        if (history\exists($ns)) {
            message($ns . ' already migrated..skipping');
            continue;
        }

        $func = "\\phpDbMigrations\\migrations\\$ns\\forwards";
        message('Running '.$func);
 
        //if fake option is passed then dont call the functions,
        //simply add that to migration table
        if(!$fake) {
            try {
                $func();
            } catch (db\MysqlException $e) {
                if ($recover) {
                    helper\printout("    Mysql Error. Recovering from failure to run next migrations.");
                    continue;
                } else {
                    helper\printout("    Mysql Error. To recover from failure pass option --recover in command.");
                    exit;
                }
            }
        }
 
        history\create($ns);
    }
}

/**
 * Function to rollback the migration to the migration that's
 * passed as the arg. This will invoke the backwards function
 * in the migration files
 */
function migrate_backwards($name) {
    $later = history\get_later_than($name);
    foreach ($later as $m) {
        $ns = $m['migration'];
        $f = $ns . '.php';
        include $f;
        $func = "\\migrations\\$ns\\backwards";
        message('Running '. $func);
        if (!$fake) {
            try {
                $func();
            } catch (db\MysqlException $e) {
                if ($recover) {
                    helper\printout("    Mysql Error. Recovering from failure to run next migrations.");
                    continue;
                } else {
                    helper\printout("    Mysql Error. To recover from failure pass option --recover in command.");
                    exit;
                }
            }
        }

        history\delete($ns);
    }    
}

function showhelp() {
    helpers\printout("
Available commands:

    create -n <migration name>
    migrate [-n <migration name>] [--fake, --recover]

");
}

try {
    run_command($argv);
} 
catch (\Exception $e) {
    showhelp();
}

