<?php

require_once('include.php');

$migrationManager = new MigrationManager();
if ($migrationManager->is_migration_finished() && current_user_can('administrator')) {
    $migrationManager->reset_migration();
    header('Location:' . get_site_url() . '/wp-admin/tools.php?page=raidboxes-migrator%2Fraidboxes-migrator.php');
    exit;
} else {
    echo 'Dieser Aufruf ist aktuell nicht gestattet!';
}
