<?php

require_once(ABSPATH.'/wp-admin/includes/plugin.php');


class MigrationManager{

    const VERSION_NUMBER    =   '1.6.7';

    const STATUS_INITIAL    =   'INITIAL';
    const STATUS_PREPARING  =   'PREPARING';
    const STATUS_DOWNLOAD   =   'DOWNLOAD';
    const STATUS_DUMP_IMP   =   'DUMP_IMPORT';
    const STATUS_DUMP_EXP   =   'DUMP_EXPORT';
    const STATUS_COMPLETE   =   'COMPLETE';
    const STATUS_FAILED     =   'FAILED';

    const INCOMPATIBLE_SPAMSHIELD       =   'wp-spamshield/wp-spamshield.php';
    #const INCOMPATIBLE_NETZBANNER       =   '';
    const INCOMPATIBLE_OP_HOURS         =   'wp-opening-hours/wp-opening-hours.php';
    const INCOMPATIBLE_NINA_FW          =   'ninjafirewall/ninjafirewall.php';
    const INCOMPATIBLE_WORDFENCE        =   'wordfence/wordfence.php';
    const INCOMPATIBLE_ABOVE_FOLD       =   'above-the-fold-optimization/abovethefold.php';
    const INCOMPATIBLE_SHIELD_SECURITY  =   'wp-simple-firewall/icwp-wpsf.php';
    const INCOMPATIBLE_PAT_LECTURES     =   'homepat-lectures/homepat-lectures.php';
    const INCOMPATIBLE_PAT_ARTICLES     =   'homepat-articles/homepat-articles.php';
    const INCOMPATIBLE_PAT_PROJECTS     =   'homepat-projects/homepat-projects.php';

    const INCOMPATIBLE_SITEPRESS        =   'sitepress-multilingual-cms/sitepress.php';


    public static $UNKNOWN_TASK = "unknown_task";
    public static $FAILED_AUTH  = "failed_auth";

    public function is_BOX() {
        $url = get_site_url() . '/RAIDBOXES/web/hostname.php';
        $headers = get_headers($url);
        $status_code = $headers[0];
        if ( strpos($status_code, '200')) {
            $result = $this->get_response($url);
            if (substr( $result, 0, 4 ) === "box-") {
                return true;
            }
        }
        return false;
    }

    public function is_BOX_result() {
        $url = get_site_url() . '/RAIDBOXES/web/hostname.php';
        $headers = get_headers($url);
        $status_code = $headers[0];
        return $status_code;
    }

    private function incompatible_plugin_array() {
        return array(
            self::INCOMPATIBLE_PAT_ARTICLES,
            self::INCOMPATIBLE_PAT_LECTURES,
            self::INCOMPATIBLE_PAT_PROJECTS,
            self::INCOMPATIBLE_ABOVE_FOLD,
            self::INCOMPATIBLE_WORDFENCE,
            self::INCOMPATIBLE_SPAMSHIELD,
            self::INCOMPATIBLE_NINA_FW,
            self::INCOMPATIBLE_OP_HOURS,
            self::INCOMPATIBLE_SHIELD_SECURITY,
        );
    }

    public function has_incompatible_plugins() {
        foreach ($this->incompatible_plugin_array() as $plugin) {
            if (is_plugin_active($plugin)) {
                return true;
            }
        }
        return false;
    }

    public function installed_incompatible_list($output = 'plugin') {
        $installed = array();
        foreach ($this->incompatible_plugin_array() as $plugin) {
            if (is_plugin_active($plugin)) {
                if ($output == 'names') {
                    $installed[$plugin]    =   $this->name_for_plugin($plugin);
                } else {
                    $installed[]  =   $plugin;
                }
            }
        }
        return $installed;
    }

    private function name_for_plugin($plugin) {
        switch ($plugin) {
            case self::INCOMPATIBLE_PAT_PROJECTS :
                return 'HomePat Projects';
            case self::INCOMPATIBLE_PAT_LECTURES :
                return 'HomePat Lectures';
            case self::INCOMPATIBLE_PAT_ARTICLES :
                return 'HomePat Articles';
            case self::INCOMPATIBLE_ABOVE_FOLD :
                return 'Above the Fold Optimization';
            case self::INCOMPATIBLE_WORDFENCE :
                return 'Wordfence';
            case self::INCOMPATIBLE_SPAMSHIELD :
                return 'WP-Spamshield';
            case self::INCOMPATIBLE_NINA_FW :
                return 'Ninja-Firewall';
            case self::INCOMPATIBLE_SITEPRESS :
                return 'Sitepress Multilingual CMS';
            case self::INCOMPATIBLE_OP_HOURS :
                return 'Opening Hours';
            case self::INCOMPATIBLE_SHIELD_SECURITY :
                return 'Shield Security';
            default:
                return 'Name nicht bekannt';
        }
    }

    public function deactivate_incompatible_plugins() {
        $plugins = $this->installed_incompatible_list();
        deactivate_plugins($plugins);
        $this->set_deactivated_incompatibles($plugins);
    }

    public function deactivated_incompatible_list() {
        $deactivated = array();
        $plugins = $this->get_deactivated_incompatibles();
        foreach ($plugins as $plugin) {
            if ($plugin !== '' && !is_plugin_active($plugin)) {
                $deactivated[]    =   $this->name_for_plugin($plugin);
            }
        }
        return $deactivated;
    }

    private function set_deactivated_incompatibles($plugins) {
        $plugins_csv = implode(',', $plugins);
        update_option('rb_migrate_deactivated_plugins', $plugins_csv);
        update_option('rb_migrate_incompatible_plugins', 'deactivated');
    }

    public function reactivate_incompatible_plugins() {
        $plugins = $this->get_deactivated_incompatibles();
        foreach ($plugins as $plugin) {
            if (!is_plugin_active($plugin)) {
                activate_plugin($plugin);
            }
        }
        update_option('rb_migrate_incompatible_plugins', 'reactivated');
        delete_option('rb_migrate_deactivated_plugins');
    }

    private function get_deactivated_incompatibles() {
        $plugins_csv = get_option('rb_migrate_deactivated_plugins');
        return explode(',', $plugins_csv);
    }

    public function get_head($url) {
        if( ini_get('allow_url_fopen') && $this->check_raidboxes_online() ) {
            $header = get_headers($url);
            return $header[0];
        }
        return false;
    }

    public function check_raidboxes_online() {
        if (!function_exists('curl_init')) {
            return false;
        }
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.uptimerobot.com/v2/getMonitors",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 3,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "api_key=m779960691-b041de084ee1d8399ba96816&format=json&logs=1", //Checkt ob raidboxes.de erreichbar ist
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded"
            ),
        ));
        curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return false;
        } else {
            return true;
        }
    }

    public function is_migration_possible() {
        $result = array(
            'possible'  =>  true,
            'reason'   =>  ''
        );

        if (!function_exists('curl_init')) {
            $result['possible'] =   false;
            $result['reason']  =   'CURL';
            return $result;
        }

        if (!$this->has_current_version()) {
            $result['possible'] =   false;
            $result['reason']  =   'VERSION';
            return $result;
        }

        $url = plugins_url().'/raidboxes-migrator/run.php';
        if (strpos($this->get_head($url),"403")) {
            $result['possible'] =   false;
            $result['reason']  =   '403';
            return $result;
        }

        $url = plugins_url().'/raidboxes-migrator/migratorAPI.php';
        if (strpos($this->get_head($url),"404")) {
            $result['possible'] =   false;
            $result['reason']  =   '404';
            return $result;
        }

        if (!function_exists('curl_version')) {
            $result['possible'] =   false;
            $result['reason']  =   'CURL';
            return $result;
        }
        if ($this->is_php_version_compatible() == 'NO') {
            $result['possible'] =   false;
            $result['reason']  =   'PHP';
            return $result;
        }
        if ($this->is_os_compatible() == 'NO') {
            $result['possible'] =   false;
            $result['reason']  =   'OS';
            return $result;
        }
        if (is_multisite() ) {
            $result['possible'] =   false;
            $result['reason']  =   'MULTISITE';
            return $result;
        }

        if (!$this->can_ftp_transfer()) {
            $result['possible'] =   false;
            $result['reason']  =   'FTP';
        }

        return $result;
    }

    private function can_ftp_transfer() {
        if (!function_exists('ftp_ssl_connect') && !function_exists('ftp_connect')) {
            return false;
        };
        return true;
    }

    public function check_sitepress($active = true) {
        if($active) {
            return is_plugin_active(self::INCOMPATIBLE_SITEPRESS);
        } else {
            return file_exists(WP_PLUGIN_DIR  . '/' . self::INCOMPATIBLE_SITEPRESS);
        }
    }

    public function has_current_version() {
        $current_version = $this->get_response(sprintf('https://dashboard.raidboxes.de/migration/jGaWUNZhnzhOdXjtKQ16MEQpI5cGwiCLmDK4sEh7eH5ZGRihPK03YuOYEHhMhKJ3'));
        if (self::VERSION_NUMBER != $current_version) {
            return false;
        }
        return true;
    }

    public function is_php_version_compatible() {
        if (version_compare(phpversion(),'5.6.0', '<' )) {
            return 'NO';
        }elseif (version_compare(phpversion(),'7.1.0', '<' )) {
            return 'WARNING';
        }
        return 'OK';
    }

    public function is_os_compatible() {
        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
            return 'OK';
        }
        return 'NO';
    }

    public function is_size_ok($size) {
        if ($size <= 4831838208) {
            return 'OK';
        }else {
            return 'WARNING';
        }
    }

    public function start_migration() {
        $migrationData = $_GET['data'];
        $migrationKey = $_GET['key'];
        if (!empty($migrationData) && !empty($migrationKey)) {
            $this->set_migration_auth($migrationData, $migrationKey);
            $this->set_migration_status(self::STATUS_PREPARING);
            $this->set_migrator_url();
        }
    }

    public function end_migration() {
        $this->delete_migration_auth();
        $this->set_migration_status(self::STATUS_COMPLETE);
    }

    public function migration_failed() {
        $this->delete_migration_auth();
        $this->set_migration_status(self::STATUS_FAILED);
    }

    public function delete_migration_auth() {
        $this->delete_migration_data();
        $this->delete_migration_key();
    }

    public function delete_migration_data() {
        $data = delete_option( 'rb_migrate_data' );
        while ( $data ) {
            $data = delete_option( 'rb_migrate_data' );
        }
    }

    public function delete_migration_key() {
        $sig  = delete_option( 'rb_migrate_key' );
        while ( $sig ) {
            $sig = delete_option( 'rb_migrate_key' );
        }
    }

    public function set_migration_status($status) {
        update_option('rb_migrate_status', $status);
    }

    public function get_migration_status() {
        return get_option('rb_migrate_status');
    }

    public function set_migration_auth($migration_data, $migration_key) {
        $this->set_migration_data($migration_data);
        $this->set_migration_key($migration_key);
    }

    public function set_migration_key($key) {
        update_option('rb_migrate_key', $key);

    }

    public function set_migration_data($data) {
        update_option('rb_migrate_data', $data);
    }

    public function set_migrator_url() {
        update_option('rb_migrate_url', get_site_url());
    }

    public function get_migrator_url() {
        return get_option('rb_migrate_url');
    }

    public function get_prefix() {
        global $wpdb;
        echo $wpdb->base_prefix;
    }

    public function reset_migration() {
        $this->set_migration_status('INITIAL');
        $this->delete_migration_auth();
        $this->set_migrator_url();
    }

    public function return_error($type, $arg = null) {
        switch ($type) {
            case self::$FAILED_AUTH:
                return sprintf("ERROR: %s", "Failed Authentication!");
            case self::$UNKNOWN_TASK:
                return sprintf("ERROR: %s - %s", "Unknown Task!", $arg);
            default:
                return "ERROR: Unknown";
        }
    }

    public function is_API_allowed() {
        return $this->is_call_authenticated() && $this->is_migration_running();
    }

    public function is_migration_authenticated() {
        $migrate_data = get_option('rb_migrate_data');
        $migrate_sig = get_option('rb_migrate_key');
        if ($migrate_data === '' || $migrate_sig === '')
            return false;
        $response = $this->get_response(sprintf('https://dashboard.raidboxes.de/migration/auth?data=%s&sig=%s', $migrate_data, $migrate_sig));
        if( $response === 'SUCCESS')
            return true;
        return false;
    }

    public function is_call_authenticated() {
        $migration_data = $_POST['migratorData'];
        $migration_sig = $_POST['migratorSig'];
        $data = get_option('rb_migrate_data');
        $data_array = explode(',', $data);
	    $saved_data = sprintf('%s,%s,%s', $data_array[0],$data_array[1], urlencode($data_array[2]) ) ;
	    $saved_sig = get_option('rb_migrate_key');
	    return ($migration_data == $saved_data  && $migration_sig == $saved_sig);
    }

    public function get_status() {
        try {
            $status = get_option('rb_migrate_status');
            return $status;
        } catch (Exception $e) {
            return "ERROR : Status konnte nicht aus Datenbank gelesen werden";
        }
    }

    public function get_migration_phase() {
        if ($this->is_start_allowed()) {
            return 1;
        } elseif ($this->is_migration_running()) {
            return 3;
        } elseif ($this->is_migration_finished()) {
            return 4;
        } else {
            return 0;
        }
    }

    public function is_start_allowed() {

        $forbidden_array = array(
            self::STATUS_PREPARING,
            self::STATUS_DOWNLOAD,
            self::STATUS_DUMP_IMP ,
            self::STATUS_DUMP_EXP,
            self::STATUS_COMPLETE,
            self::STATUS_FAILED,
        );

        if(!in_array($this->get_status(), $forbidden_array)) {
            return true;
        };
        return false;
    }

    public function is_migration_running() {
        $status = $this->get_status();
        return ($status == self::STATUS_PREPARING || $status == self::STATUS_DOWNLOAD || $status == self::STATUS_DUMP_EXP || $status == self::STATUS_DUMP_IMP);
    }

    public function is_migration_finished() {
        $status = $this->get_status();
        return ($status == self::STATUS_COMPLETE || $status == self::STATUS_FAILED);
    }

    public function get_response($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT ,0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60); //timeout in seconds
        $data = curl_exec($curl);
        curl_close($curl);
        return $data;
    }

}