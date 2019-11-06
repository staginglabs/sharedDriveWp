<?php

#error_reporting(E_ALL);
#ini_set('display_errors', 1);

require_once(dirname( __FILE__ ) . '/includes/include.php' );
wp_enqueue_style( 'RB-migrator', plugins_url( '/css/style.css', __FILE__ ) );
wp_enqueue_style( 'WebFontKit', plugins_url( '/css/MyFontsWebfontsKit.css', __FILE__ ) );
wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
$pluginsurl = plugins_url();
$migrationManager = new MigrationManager();
$interfaceManager = new InterfaceManager();
$fileManager = new FileManager();
$size = $fileManager->fetch_size(ABSPATH);
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<div id="rbmigrator-main" class="box bgwhite flleft">
    <h1>RAIDBOXES-Migrator</h1>
        <div class="innerbox">
        <?php
        $migratorURL = $migrationManager->get_migrator_url();
        $site_url = get_site_url();
        if ($migratorURL !== false && $migratorURL != $site_url && strpos($site_url, "myraidbox") !== false) {
            $migrationManager->set_migration_status('COMPLETE');
            $interfaceManager->print_welcome();
        }
        else {
            if ($migrationManager->is_BOX()) {
                $interfaceManager->print_raidboxes_box_message();
            } else {
                $migration_possibility = $migrationManager->is_migration_possible();
                if ( $migration_possibility['possible'] ) {
                    $interfaceManager->print_status_information( $migrationManager->get_migration_phase());
                    if (get_option('rb_migrate_incompatible_plugins') == 'deactivated') {
                        update_option('rb_migrate_incompatible_plugins', 'checked');
                    }
                    elseif (get_option('rb_migrate_incompatible_plugins') == 'reactivated') {
                        delete_option('rb_migrate_incompatible_plugins');
                    }
                    $interfaceManager->print_incompatible_warning($migrationManager->installed_incompatible_list('names'));


                    if (!$migrationManager->is_migration_running()) {
                        $interfaceManager->print_reactivate_plugins($migrationManager->deactivated_incompatible_list());
                    }
                    else {
                        include(dirname(__FILE__).'/includes/scripts.php');
                    }

                    if ($migrationManager->is_start_allowed()) {
                        print("<br>");
                        $interfaceManager->print_php_warning($migrationManager->is_php_version_compatible());
                        print("<br>");
                        $interfaceManager->print_size_warning($migrationManager->is_size_ok($size));
                        print("<br>");
                        $interfaceManager->print_start_button($size);
                    }

                    if ($migrationManager->is_migration_finished()) {
                        $interfaceManager->print_dashboard_button();
                    }

                }
                else {
                    $interfaceManager->print_not_possible($migration_possibility['reason']);
                }
            }

        }

        ?>
        </div>
        <br>
        <hr>
        <div class="innerbox">
            <h2 id="rbsysteminfo">Deine Systeminformationen</h2>
            <?php
            $interfaceManager->print_system_information($size);
            ?>
        </div>
        <hr>
        <div id="footer" class="innerbox">
            <?php
            $url = 'https://raidboxes.de/rb_banner_pusher/migrator-horizontaler-footer-banner/';
            if ( strpos($migrationManager->get_head($url),"200")) {
                $interfaceManager->print_banner($url);
            }
            else {
                $interfaceManager->print_footer_banner_fallback();
            }
            ?>
        </div>

        <hr>
        <div id="copyright" class="grey">
            <small class="flleft">Bitte beachte, dass wir nur Inhalte kopieren, die deiner Wordpress-Seite selbst zugehörig sind.</small>
            <small class="flright">&copy; Copyright 2015-<? echo date("Y")?> <a href="https://raidboxes.io/" target="_blank" title="RAIDBOXES GmbH" class="grey">RAIDBOXES GmbH</a></small>
        </div>
    </div>
<div id="rbmigrator-banner_right" class="box flleft bgwhite">
        <?php
        $url = 'https://raidboxes.de/rb_banner_pusher/migrator-vertikaler-side-banner/';
        if ( strpos($migrationManager->get_head($url),"200")) {
            $interfaceManager->print_right_banner($url);
        }
        else {
            $interfaceManager->print_right_banner_fallback();
        }
        ?>
    </div>
<script>
    jQuery(document).ready(function($) {
        $("#rbmigrator-main").change(function () {
            var s= $("#rbmigrator-banner_right").height();
            $(this).height(s.height);
        });
    });
</script>
