<?php

namespace phpDbMigrations\lib\command;

$available_commands = array('create', 'migrate');

function validate($cmd) {
    if (!in_array($cmd, array('create', 'migrate'))) {
        return false;
    }
    return true;
}

abstract class Command {

    public static function factory($_argv) {
        list($script, $cmd, $optstr) = explode(" ", $_argv, 3);
        $options = explode(' ', $optstr);

        if ($cmd === 'create') {
            return new CreateCommand('create', $options);
        } else if ($cmd === 'migrate') {
            return new MigrateCommand('migrate', $options);
        } else {
            throw new UnknownCommand('Command ' . $cmd . ' not available');
        }
    }

    public function __construct($command, $options) {
        $this->command = $command;
        $this->options = $options;
    }    

    public function is_valid() {
        return true;
    }
}


class CreateCommand extends Command {

    public function is_valid() {
        return in_array($cmd, static::$AVAILABLE_COMMANDS);
    }    

    public function __get($key) {
        list($o, $option) = explode('_', $key, 2);
        if ($option === 'name') {
            $n = array_search('-n', $this->options);
            return $n != -1 ? $this->options[$n+1] : null;
        }
        
        if ($option === 'fake') {
            return array_search('--fake', $this->options) >= 0;
        }

        if ($option === 'recover') {
            return array_search('--recover', $opts) >= 0;
        }
    }
}


class MigrateCommand extends Command {

    public function __get($key) {
        list($o, $option) = explode('_', $key, 2);
        if ($option === 'name') {
            $n = array_search('-n', $this->options);
            return $n != -1 ? $this->options[$n+1] : null;
        }
        
        if ($option === 'fake') {
            return array_search('--fake', $this->options) >= 0;
        }

        if ($option === 'recover') {
            return array_search('--recover', $opts) >= 0;
        }
    }

}





