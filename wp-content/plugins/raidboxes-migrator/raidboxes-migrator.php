<?php
/*
 * Plugin Name: RAIDBOXES-Migrator
 * Plugin URI:  http://raidboxes.de/umzuege
 * Description: Kopiere deine Seite zu RAIDBOXES
 * Author:      RAIDBOXES.de
 * Author URI:  https://raidboxes.de
 * Version:     1.6.7
 * License:     GPL2
 * Network:     true
 */


add_action('admin_menu', function() {
	if ( empty ( $GLOBALS['admin_page_hooks']['RAIDBOXES'] ) ) {
		add_menu_page('RAIDBOXES-Migrator', 'RAIDBOXES-Migrator', 'manage_options', __FILE__,
		              'rbmigrate_action', plugins_url().'/raidboxes-migrator/img/favicon-16x16.png');
	}
});


add_action( 'activated_plugin', 'raidboxes_migrator_activation_redirect' );
register_deactivation_hook( __FILE__, 'raidboxes_migrator_deactivate' );

function rbmigrate_action() {
		include( dirname( __FILE__ ) . '/iamapage.php' );
}


function raidboxes_migrator_activation_redirect($plugin) {
    if( $plugin == plugin_basename( __FILE__ ) ) {
        update_option('rb_migrate_status', 'INITIAL');
        update_option('rb_migrate_url', get_site_url());
        exit(wp_redirect( admin_url( 'admin.php?page=raidboxes-migrator%2Fraidboxes-migrator.php' ) ));
    }
}

function raidboxes_migrator_deactivate() {
    $data   = delete_option( 'rb_migrate_data' );
    $sig    = delete_option( 'rb_migrate_key' );
    $stat   = delete_option( 'rb_migrate_status' );
    $deac   = delete_option('rb_migrate_incompatible_plugins');
    $csv    = delete_option('rb_migrate_deactivated_plugins');
    $list   = delete_option('rb_file_list');
    while ( $data ) {
        $data = delete_option( 'rb_migrate_data' );
    }
    while ( $sig ) {
        $sig = delete_option( 'rb_migrate_key' );
    }
    while ( $stat ) {
        $stat = delete_option( 'rb_migrate_status' );
    }
    while ( $deac ) {
        $deac = delete_option('rb_migrate_incompatible_plugins');
    }
    while ( $csv ) {
        $csv = delete_option('rb_migrate_deactivated_plugins');
    }

    while($list) {
        $list   = delete_option('rb_file_list');
    }
}
