<?php
/**
 * Takes advantage of all calls to the plugin from RAIDBOXES
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('includes/include.php');

$task = $_POST['task'];

$migrationManager = new MigrationManager();
//
try{
    if ($migrationManager->is_API_allowed()) {
        switch ($task) {
            case 'size':
                $fileManager = new FileManager();
                echo $fileManager->fetch_size($root_dir);
                break;
            case 'dir_list':
                $fileManager = new FileManager();
                $fileManager->dir_list($root_dir, true);
                break;
            case 'data_list':
                $fileManager = new FileManager();
                $fileManager->fetch_list($root_dir, false);
                break;
            case 'rb_list':
                $fileManager = new FileManager();
                $fileManager->fetch_list($root_dir, true);
                break;
            case 'prefix':
                $migrationManager->get_prefix();
                break;
            case 'db_list':
                $databaseManager = new DatabaseManager();
                $databaseManager->get_tables();
                break;
            case 'dump_old':
                $databaseManager = new DatabaseManager();
                $databaseManager->dump_table();
                break;
            case 'ftp_dump':
                $databaseManager = new DatabaseManager();
                $databaseManager->ftp_dump();
                break;
            case 'stop':
                $migrationManager->end_migration();
                break;
            case 'ftp':
                $fileManager = new FileManager();
                $fileManager->send_ftp(false);
                break;
            case 'ftp_repeat':
                $fileManager = new FileManager();
                $fileManager->send_ftp(true);
                break;
            case 'data_json':
                $fileManager = new FileManager();
                $fileManager->data_array($root_dir);
                break;
            case 'ftp_index':
                $fileManager = new FileManager();
                $fileManager->index_option();
                break;
            default:
                print $migrationManager->return_error(MigrationManager::$UNKNOWN_TASK, $task);
        }
    } else {
        print $migrationManager->return_error(MigrationManager::$FAILED_AUTH);
    }
} catch (Exception $e) {
    print( sprintf('ERROR CODE %s: %s', $e->getCode(), $e->getMessage()) );
}



