<?php

class FileManager{

    public static $RUN_LIMIT = 1000;
    public static $MAX_ERRORS = 3;

    public function fetch_size($dir) {
        $size = $this->fetch_size_rekursiv($dir);
        $size += $this->fetch_database_size();
        return $size;
    }

    private function fetch_size_rekursiv($dir, $size = 0) {
        $olddir = getcwd();

        if(!chdir($dir)) {
            return $size;
        }
        $content = array_diff(scandir(getcwd()), array('.', '..'));
        foreach($content as $data) {
            if ( is_dir($data) && !$this->is_exclude(sprintf('%s/%s', getcwd(), $data))) {
                $size = $this->fetch_size_rekursiv($data, $size);
            }elseif(!$this->is_exclude(sprintf('%s/%s', getcwd(), $data))) {
                $size += filesize($data);
            }
        }
        chdir($olddir);
        return $size;
    }

    private function fetch_database_size() {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $db_size = 0;
        $q = $mysqli->query( 'SHOW TABLE STATUS');
        while($row = mysqli_fetch_array($q)) {
            $db_size += $row["Data_length"] + $row["Index_length"];
        }
        return $db_size;
    }

    public function fetch_list($dir, $rb = false) {
        $this->fetch_list_rekursiv($dir, $rb);
    }

    private function fetch_list_rekursiv($dir, $rb) {
        $olddir = getcwd();
        if(!chdir($dir)) {
            return ;
        }
        $content = array_diff(scandir(getcwd()), array('.', '..'));
        $cwd = getcwd();
        if($rb) {
            $cwd = $this->raidboxes_path_for_ftp($cwd);
        }
        echo( sprintf('%s/', $cwd) );
        foreach($content as $data) {
            if ( is_dir($data) && !$this->is_exclude(sprintf('%s/%s', $cwd, $data))) {
                echo("\n");
                $this->fetch_list_rekursiv($data, $rb);
            }elseif(!$this->is_exclude(sprintf('%s/%s', $cwd, $data))) {
                echo("\n");
                echo (sprintf('%s/%s', $cwd, $data));
            }
        }
        chdir($olddir);
    }

    public function dir_list($dir) {
        header('Content-Type: application/json');
        $dir_array = array();
        $dir_array = $this->dir_list_rekursiv($dir, $dir_array);
        $return_json = json_encode($dir_array);
        print_r($return_json);

    }

    private function dir_list_rekursiv($dir, $dir_array) {
        $olddir = getcwd();
        if(!chdir($dir)) {
            return $dir_array;
        }
        $content = array_diff(scandir(getcwd()), array('.', '..'));

        $cwd = $this->raidboxes_path_for_ftp(getcwd());

        foreach($content as $dir_or_file) {
            $path = sprintf('%s/%s', $cwd, $dir_or_file);
            if ( is_dir($dir_or_file) && !$this->is_exclude($path)) {;
                $dir_array[] = $path;
                $dir_array = $this->dir_list_rekursiv($dir_or_file, $dir_array);
            }
        }
        chdir($olddir);
        return $dir_array;
    }

    public function data_array($dir) {
        error_reporting(0);
        ini_set('display_errors', 0);
        $data_array = array(
            'rb_files',
            'plugin_files'
        );
        $data_array = $this->data_array_rekursiv($dir,$data_array);
        $json_string = $this->encode_my_string($data_array['plugin_files']);

        update_option('rb_file_list',$json_string);
        $return_json = $this->encode_my_string($data_array['rb_files']);

        header('Content-Type: application/json; charset=utf-8');
        print_r($return_json);
    }

    private function encode_my_string($string_to_encode) {
        $json_string = json_encode($string_to_encode);
        if (!$json_string) {
            $json_string = json_encode($string_to_encode, JSON_UNESCAPED_UNICODE);
        }
        if (!$json_string) {
            die(sprintf("Error %s encoding Json: %s", json_last_error(), json_last_error_msg()));
        }
        return $json_string;
    }

    private function data_array_rekursiv($dir, $data_array) {
        $olddir = getcwd();
        if(!chdir($dir)) {
            return null;
        }
        $content = array_diff(scandir(getcwd()), array('.', '..'));
        $cwd = getcwd();
        foreach($content as $dir_or_file) {
            if ( is_dir($dir_or_file) && !$this->is_exclude(sprintf('%s/%s', $cwd, $dir_or_file))) {
                $data_array = $this->data_array_rekursiv($dir_or_file, $data_array);
            }elseif(!$this->is_exclude(sprintf('%s/%s', $cwd, $dir_or_file))) {
                $file_string = sprintf('%s/%s', $cwd, $dir_or_file);
                $size = filesize($file_string);

                $data_array['plugin_files'][] = array(
                    'file_name' => $file_string,
                    'file_size' => $size
                );
                $data_array['rb_files'][] = array(
                    'file_name' => $this->raidboxes_path_for_ftp($file_string),
                    'file_size' => $size
                );
            }
        }
        chdir($olddir);
        return $data_array;
    }

    public function send_ftp($repeat) {
        $ftp = MigratorFTP::newFTP();

        if ($repeat) {
            $indizes = $_POST['indizes'];
            $files = array();
            $file_json = get_option('rb_file_list');
            $files_all = json_decode($file_json, true);

            foreach ($indizes as $index) {
                $files[] = $files_all[$index];
            }
            $index = 0;
        } else {
            $index = $_POST['index'];
            $file_json = get_option('rb_file_list');
            $files = json_decode($file_json, true);
        }
        $this->send_ftp_from_list($ftp, $files, $index);
    }


    private function send_ftp_from_list(MigratorFTP $ftp, $files, $index) {
        $number_of_files = sizeof($files);
        $run = true;
        $status_url = $_POST['status_url'];
        $run_number = 0;
        while($run && $index <= $number_of_files) {
            usleep(1000*100);

            if ( $index % 25 == 0 ) {
                $status = $this->status_string_for_index($index, $number_of_files);
                $this->set_status_for_migration($status_url, $status);
                $this->save_index_option($index, $ftp);
            }
            $file_path = $files[$index]['file_name'];
            $remote_path = $this->raidboxes_path_for_ftp($file_path);
            if ($ftp->file_does_not_exist_on_server($remote_path)) {
                for($i = 0; $i < 3; $i++) {
                    $result = $ftp->send($remote_path, $file_path);
                    if($result) {
                        break;
                    } else {
                        sleep(1);
                    }
                }
            }
            //So oder so nach 3 Versuchen die nächste Datei versuchen
            $index++;
            $run_number++;
            if($run_number > self::$RUN_LIMIT) {
                $run = false;
            }
        }
        $this->save_index_option($index, $ftp);
    }

    private function save_index_option($index, MigratorFTP $ftp) {
        $tmp_name = $this->prepare_index_file($index);
        return $ftp->send('index.tmp', $tmp_name);
    }

    private function prepare_index_file($index) {
        $tmp_name = __DIR__ . '/index.tmp';
        $result = file_put_contents($tmp_name, $index);
        if ($result === false) {
            dd("Failed: Write Index");
        }
        return $tmp_name;
    }

    public function index_option() {
        $index = get_option('rb_migrate_index');
        echo $index;
    }

    private function raidboxes_path_for_ftp($file_path) {
        global $root_dir;
        return str_replace($root_dir, '', $file_path);
    }

    private function is_exclude($dir_entry) {

        global $root_dir;

        $INCLUDES = array(
            WP_CONTENT_DIR . '/plugins/',
            WP_CONTENT_DIR . '/themes/',
        );

        $EXCLUDES = array(
            $root_dir . '/wp-includes',
            $root_dir . '/wp-admin',
            $root_dir . "/index.php",
            $root_dir . "/staging",
            $root_dir . '/rb-plugins',
            $root_dir . '.htaccess',
            $root_dir . 'rb-settings.php',
            $root_dir . 'wp-blog-header.php',
            $root_dir . 'wp-config.php',
            $root_dir . 'wp-comments-post.php',
            $root_dir . 'wp-cron.php',
            $root_dir . 'wp-activate.php',
            $root_dir . 'wp-links-opml.php',
            $root_dir . 'wp-login.php',
            $root_dir . 'wp-load.php',
            $root_dir . 'wp-mail.php',
            $root_dir . 'wp-settings.php',
            $root_dir . 'wp-signup.php',
            $root_dir . 'wp-trackback.php',
            $root_dir . 'xmlrpc.php',
            WP_CONTENT_DIR . '/backup',
            WP_CONTENT_DIR . '/bps-backup',
            WP_CONTENT_DIR . '/cache',
            WP_CONTENT_DIR . '/envato-backups',
            WP_CONTENT_DIR . '/updraft',
            WP_CONTENT_DIR . '/vpbackups',
            WP_CONTENT_DIR . '/ai1wm-backups',
            WP_CONTENT_DIR . '/hostinger-page-cache',
            WP_CONTENT_DIR . '/mu-plugins',
            WP_CONTENT_DIR . '/backupwordpress-',
            WP_CONTENT_DIR . '/uploads/backupwordpress-',
            WP_CONTENT_DIR . '/force-strong-passwords',
            WP_CONTENT_DIR . '/wp-engine-common',
            WP_CONTENT_DIR . '/uploads/backup-guard/',
            WP_CONTENT_DIR . '/uploads/backwpup-',
            WP_CONTENT_DIR . 'uploads/fw-backup/',
            WP_CONTENT_DIR . 'uploads/magictoolbox_cache/',
            WP_CONTENT_DIR . 'uploads/wc-logs',
            WP_CONTENT_DIR . '/gt-cache',
            WP_CONTENT_DIR . '/plugins/akeebabackupwp/app/backups',
            '/msd/work/backup',
            '/cache/',
            '/logs/',
            'RAIDBOXES',
            '.ftpquota',
            '.md5sums',
            '.opcache',
            'sess_',
            'zend_cache--',
            'cgi-bin/',
            'access_log',
            'error_log',
            '/infinitewp/backups',
            'wp-snapshots/',
            '/.php',
            '/piwik',
            '/tCapsule/backups',
        );

        foreach ( $INCLUDES as $value ) {
            if ( strpos($dir_entry, $value) !== false ) {
                return false;
            }
        }

        foreach ( $EXCLUDES as $value ) {
            if ( strpos($dir_entry, $value) !== false ) {
                return true;
            }
        }
        return false;
    }

    private function status_string_for_index($index, $number_of_files) {
        return sprintf("Downloade Datei %s von %s",$index, $number_of_files);
    }

    protected function set_status_for_migration($status_url, $status) {
        $migrate_data = get_option('rb_migrate_data');
        $migrate_sig = get_option('rb_migrate_key');
        $curl_url = sprintf('%s?data=%s&sig=%s&status=%s', $status_url, $migrate_data, $migrate_sig, $status);
        $ch = curl_init($curl_url);
        curl_exec($ch);
        curl_close($ch);
    }
}