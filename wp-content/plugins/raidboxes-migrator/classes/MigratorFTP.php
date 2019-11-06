<?php
class MigratorFTP {

    const DEFAULT_PORT = 21;
    const DEFAULT_TIMEOUT = 300;


    private $connection;
    private $url;
    private $port;
    private $ssl;

    public static function newFTP() {
        $box_url = $_POST['boxurl'];
        $ftp_username = $_POST['ftp_username'];
        $ftp_pw = $_POST['ftp_pw'];
        $ftp_port = $_POST['ftp_port'];

        $ftp = new MigratorFTP($box_url, $ftp_port);


        $connection_type = $ftp->ssl ? 'SSL-FTP' : 'FTP';
        if (!$ftp->connection) {
            dd(sprintf("Failed: %s-Connect for BOX %s with user %s and pw %s on port %s", $connection_type, $box_url, $ftp_username, $ftp_pw, $ftp_port));
        }

        $result = $ftp->login($ftp_username, $ftp_pw);
        if (!$result) {
            dd(sprintf("Failed: FTP-Login for BOX %s with user %s and pw %s on port %s", $box_url, $ftp_username, $ftp_pw, $ftp_port));
        }

        $ftp->setFTP_PASV();
        return $ftp;
    }

    public function __construct($url, $port = self::DEFAULT_PORT) {
        $this->url = $url;
        $this->port = $port;

        if (function_exists('ftp_ssl_connect')) {
            $this->ssl = true;
        } else {
            $this->ssl = false;
        }

        $this->connect();
    }

    private function connect() {
        for ($i= 0; $i < 3; $i++) {
            if ($this->ssl) {
                $this->connection = ftp_ssl_connect($this->url, $this->port, self::DEFAULT_TIMEOUT);
            } else {
                $this->connection = ftp_connect($this->url, $this->port, self::DEFAULT_TIMEOUT);
            }
            if ($this->connection) {
                return;
            }
        }
    }

    public function login($ftp_name, $ftp_pw) {
        return ftp_login($this->connection, $ftp_name, $ftp_pw);
    }

    public function setFTP_PASV($pass = true) {
        ftp_pasv($this->connection, $pass);
    }

    public function set_FTP_TIMEOUT($timeout = self::DEFAULT_TIMEOUT) {
        ftp_set_option($this->connection, FTP_TIMEOUT_SEC, $timeout);
    }

    public function get_FTP_TIMEOUT() {
        return ftp_get_option($this->connection, FTP_TIMEOUT_SEC);
    }

    public function send($remote_name, $file) {
        return ftp_put($this->connection, $remote_name, $file, FTP_ASCII);
    }

    public function make_dir($dir_name) {
        return ftp_mkdir($this->connection, $dir_name);
    }

    public function print_infos() {
        dd($this->get_FTP_TIMEOUT());
    }

    public function file_does_not_exist_on_server($file_name) {
        $fileSize = ftp_size($this->connection, $file_name);
        return $fileSize == -1;
    }
}