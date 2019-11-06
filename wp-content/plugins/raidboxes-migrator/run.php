<?php

require_once('includes/include.php');

$migrationManager = new MigrationManager();
if ($migrationManager->is_start_allowed() && current_user_can('administrator')) {
    $migrationManager->start_migration();
    header('Location:' . get_site_url() . '/wp-admin/tools.php?page=raidboxes-migrator%2Fraidboxes-migrator.php');
	exit;
} else {
    echo 'Dieser Aufruf ist aktuell nicht gestattet!';
}
