<?php

$migrator_path = dirname(dirname(__FILE__));
$root_dir = dirname(dirname(dirname($migrator_path)));
$plugins_dir = dirname($migrator_path);
require_once($root_dir.'/wp-load.php');
require_once($migrator_path . '/classes/MigrationManager.php');
require_once($migrator_path . '/classes/FileManager.php');
require_once($migrator_path.'/classes/APIManager.php');
require_once($migrator_path.'/classes/InterfaceManager.php');
require_once($migrator_path.'/classes/DatabaseManager.php');
require_once($migrator_path.'/classes/MigratorFTP.php');

// global constants
@ini_set('max_execution_time', 600);

function log_func($name, $result) {
    if(is_bool($result)) {
        $result = ($result) ? 'true' : 'false';
    }
    $log =sprintf('Executed %s wit result of: %s', $name, $result);
    var_dump($log);
}

function dd($var) {
    var_dump($var);
    exit;
}