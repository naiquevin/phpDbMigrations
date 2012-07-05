<?php

namespace phpDbMigrations\lib\autoload;

$libs = array(
    'db.php',
    'history.php',
    'helpers.php',
);

foreach ($libs as $lib) {
    require_once($lib);
}

