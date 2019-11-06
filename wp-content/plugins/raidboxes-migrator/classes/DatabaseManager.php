<?php

class DatabaseManager {

    public function get_tables() {
        global $wpdb;

        header('Content-Type: application/json');

        $tables = $wpdb->get_results('SHOW TABLES');
        $table_array = array();
        foreach ($tables as $table) {
            $table_name = $table->{"Tables_in_$wpdb->dbname"};
            if(!$this->is_exclude_table($table_name)) {
                $table_array[] = $table_name;
            }
        }
        $json_array = json_encode($table_array, JSON_PRETTY_PRINT);
        print_r($json_array);
    }

    public function ftp_dump() {
        $table_name = $_POST['table'];
        var_dump("Export Table: " . $table_name);
        $this->send_table($table_name);
        var_dump("Done: " . $table_name);
    }

    protected function send_table($table_name) {
        global $wpdb;
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $table_name = trim($table_name);
        $limit = 50;
        $schema = $wpdb->get_row('SHOW CREATE TABLE ' . $table_name, ARRAY_A);

        $table_dump = ($schema['Create Table'] . ';' . PHP_EOL);

        $query = sprintf('SELECT COUNT(*) as rows FROM %s', $table_name);

        $count_rows = $wpdb->get_results( $query , ARRAY_A );
        $runs = $count_rows[0]['rows']/$limit;

        for ($i = 0; $i <= $runs; $i++) {

            $start_row = $limit*$i;
            $query = $wpdb->prepare('SELECT * FROM '. $table_name . ' LIMIT %d,%d' , $start_row, $limit);

            $rows = $wpdb->get_results($query, ARRAY_A);
            if( $rows ) {
                foreach ($rows as $row => $fields) {
                    $insert_string = 'INSERT INTO ' . $table_name . ' VALUES ';
                    $table_dump .= $insert_string;

                    $line = '';

                    foreach ($fields as $key => $value) {
                        $value = $mysqli->real_escape_string($value);
                        if ($value === NULL) {
                            $line .= 'NULL' . ',';
                        } else {
                            $line .= "'" . $value . "',";
                        }
                    }
                    $line = '(' . rtrim($line, ',') . ')';

                    $table_dump .= $line;
                    $table_dump .= ('; ' . PHP_EOL);
                }
            }
        }
        $this->send_dump($table_dump, $table_name);
    }

    protected function send_dump($table_dump, $table_name) {
        $ftp = MigratorFTP::newFTP();
        $file = $this->prepare_stream($table_dump);
        $result = $ftp->send($table_name . '.sql', $file);
        if (!$result) {
            dd(sprintf("Failed: FTP-Send"));
        }
    }

    private function prepare_stream($content) {
        $tmp_name = __DIR__ . '/dump.tmp';
        $result = file_put_contents($tmp_name, $content);
        if ($result === false) {
            dd("Failed: Write Dump");
        }
        return $tmp_name;
    }

    public function dump_table() {

        global $wpdb;
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        $table_name = $_POST['table'];
        $table_name = trim($table_name);
        $table_name = $mysqli->real_escape_string($table_name);


        $limit = 50;

        $schema = $wpdb->get_row('SHOW CREATE TABLE ' . $table_name, ARRAY_A);
        echo($schema['Create Table'] . ';' . PHP_EOL);

        $query = sprintf('SELECT COUNT(*) as rows FROM %s', $table_name);

        $count_rows = $wpdb->get_results( $query , ARRAY_A );
        $runs = $count_rows[0]['rows']/$limit;

        for ($i = 0; $i <= $runs; $i++) {

            $start_row = $limit*$i;


            $query = $wpdb->prepare('SELECT * FROM '. $table_name . ' LIMIT %d,%d' , $start_row, $limit);

            $rows = $wpdb->get_results($query, ARRAY_A);
            if( $rows ) {
                foreach ($rows as $row => $fields) {
                    $insert_string = 'INSERT INTO ' . $table_name . ' VALUES ';
                    echo ($insert_string);

                    $line = '';

                    foreach ($fields as $key => $value) {
                        $value = $mysqli->real_escape_string($value);
                        if ($value === NULL) {
                            $line .= 'NULL' . ',';
                        } else {
                            $line .= "'" . $value . "',";
                        }
                    }
                    $line = '(' . rtrim($line, ',') . ')';

                    echo($line);
                    echo('; ' . PHP_EOL);
                    ob_flush();
                    flush();
                }
            }
        }
    }

    private function is_exclude_table($table_name) {

        $DIRECTORY_EXCLUDES = array(
            'mclean_refs',
            'piwik',
        );


        foreach ( $DIRECTORY_EXCLUDES as $value ) {
            if ( strpos($table_name, $value) !== false ) {
                return true;
            }
        }
        return false;
    }

    /*   public function trick_dump() {
           //use for setting specific values to null or something else

           global $wpdb;
           $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

           $table_name = $_POST['table'];
           $table_name = trim($table_name);
           $table_name = $mysqli->real_escape_string($table_name);


           $limit = 50;

           $schema = $wpdb->get_row('SHOW CREATE TABLE' . $table_name, ARRAY_A);
           echo($schema['Create Table'] . ';' . PHP_EOL);

           $query = sprintf('SELECT COUNT(*) as rows FROM %s', $table_name);

           $count_rows = $wpdb->get_results( $query , ARRAY_A );
           $runs = $count_rows[0]['rows']/$limit;

           for ($i = 0; $i <= $runs; $i++) {

               $start_row = $limit*$i;


               $query = $wpdb->prepare('SELECT * FROM '. $table_name . ' LIMIT %d,%d' , $start_row, $limit);

               $rows = $wpdb->get_results($query, ARRAY_A);
               if( $rows ) {
                   foreach ($rows as $row => $fields) {
                       $counter = 0;
                       $insert_string = 'INSERT INTO ' . $table_name . ' VALUES ';
                       echo ($insert_string);

                       $line = '';

                       foreach ($fields as $key => $value) {

                           if ($counter == 0) {
                               $line .= "NULL";
                           }
                           else {
                               $value = $mysqli->real_escape_string($value);
                               if ($value === NULL) {
                                   $line .= 'NULL' . ',';
                               } else {
                                   $line .= "'" . $value . "',";
                               }
                           }
                           $counter++;
                       }
                       $line = '(' . rtrim($line, ',') . ')';

                       echo($line);
                       echo('; ' . PHP_EOL);
                       ob_flush();
                       flush();
                   }
               }
           }
       } */

}