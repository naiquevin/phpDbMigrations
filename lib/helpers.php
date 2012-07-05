<?php

namespace phpDbMigrations\lib\helpers;

function printout($msg) {
    if (is_array($msg)) {
        print_r($msg);
        return;
    }
        
    if (is_string($msg)) {
        echo $msg;
        echo "\n";
        return;
    }
}

/**
 * Function to collect all migration files in migrations dir in
 * an array. Migration files will be prefixes by underscore ('_')
 * and sorted in the ascending order of their creation time
 */
function get_migration_files() {
    $files = array();
    foreach (glob(MIGRATIONS_DIR . '/_*.php') as $f) {
        $files[] = $f;
    }
    usort($files, function ($a, $b) {
            $ts = array_map(function ($x) { 
                    $exp = explode('_', $x); return (int)$exp[1]; 
                }, array($a, $b));
            return $ts[0] - $ts[1];
        });
    return $files;
}


function code_template($filename) { 
    return "<?php

namespace phpDbMigrations\\migrations\\$filename;
 
use phpDbMigrations\lib\db\PDOWrapper;
use phpDbMigrations\lib\helpers;
 
function forwards() {
    helpers\\printout('    Forwards Not implemented..skipping');
    // Replace the query below with your db change query
    // PDOWrapper::exec_query('CREATE TABLE ...');
}
 
function backwards() {
    helpers\\printout('    Backwards Not implemented..skipping');
}\n"; 
}



