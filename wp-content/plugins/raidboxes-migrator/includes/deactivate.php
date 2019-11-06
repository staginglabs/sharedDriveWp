<?php

require_once('include.php');

if( current_user_can('administrator') ) {
    $migrationmanger = new MigrationManager();
	$migrationmanger->deactivate_incompatible_plugins();
	header('Location:' . get_site_url() . '/wp-admin/tools.php?page=raidboxes-migrator%2Fraidboxes-migrator.php');
    exit;
}