<?php

class InterfaceManager
{

    public function print_status_information($phase) {
        switch ($phase) {
            case 3:
                $this->print_migrating_bar();
                $this->print_migrating_container();
                break;
            case 4:
                $this->print_end_bar();
                break;
            default:
                $this->print_start_bar();
                $this->print_start_container();
                break;
        }
    }

    public function print_start_bar() {
        print('<div class="status-container">
                <div class="status-item active">1. Start</div>
                <div class="status-item">2. BOX-Auswahl</div>
                <div class="status-item">3. Migration</div>
                <div class="status-item">4. Abschluss</div>
             </div> <br>');
    }

    public function print_migrating_bar() {
        print('<div class="status-container">
                <div class="status-item active">1. Start</div>
                <div class="status-item active">2. BOX-Auswahl</div>
                <div class="status-item active">3. Migration</div>
                <div class="status-item">4. Abschluss</div>
             </div> <br>');
    }

    public function print_end_bar() {
        $status = get_option('rb_migrate_status');
        if ($status == 'COMPLETE') {
        $this->print_success_end();
        } else {
            $this->print_failed_end();
        }
    }

    private function print_success_end() {
        $this->print_success_end_bar();
        $this->print_success_end_message();
    }

    private function print_failed_end() {
        $this->print_failed_end_bar();
        $this->print_failed_end_message();
    }

    private function print_success_end_message() {
        $this->print_p(sprintf('<p><b>Deine Seite wurde erfolgreich kopiert.</b> </p><br>', get_site_url()));
    }

    private function print_failed_end_message() {
        $this->print_p(sprintf('<button class="txtlft rbred rbredborder nohover nocursor bgwhite">Fehlschlag! <br>
                                        Der Umzug konnte nicht abgeschlossen werden. Das ist aber natürlich kein Problem:<br>
                                        Trage deinen Umzugswunsch einfach <a href="https://dashboard.raidboxes.de/migration/wp_migration" class="rbblue" target="_blank">hier</a> ein, wir kümmern uns dann um die komplette Migration und melden uns bei dir, wenn der Umzug abgeschlossen ist.</button>'));
    }

    private function print_success_end_bar() {
        print('<div class="status-container">
                <div class="status-item active">1. Start</div>
                <div class="status-item active">2. BOX-Auswahl</div>
                <div class="status-item active">3. Migration</div>
                <div class="status-item active">4. Abschluss</div>
             </div><br>');
    }

    private function print_failed_end_bar() {
        print('<div class="status-container">
                <div class="status-item failed">1. Start</div>
                <div class="status-item failed">2. BOX-Auswahl</div>
                <div class="status-item failed">3. Migration</div>
                <div class="status-item failed">4. Abschluss</div>
             </div><br>');
    }

    public function print_p($message) {
        print( sprintf('<p>%s</p>', $message));
    }

    public function print_raidboxes_box_message() {
        print('<button class="txtlft nohover nocursor rbred rbredborder bgwhite">
                <p>Achtung: Der RAIDBOXES-Migrator ist nicht dafür geeignet Seiten innerhalb unseres Systems umzuziehen – dafür haben wir eigene Tools entwickelt. 
                <p>Er dient lediglich dazu Webseiten von Drittanbietern in unser System zu migrieren.</p>
                <p>Wenn du deine Seite klonen möchtest, nutze hierfür bitte unser praktisches <a class="rbblue" href="https://helpcenter.raidboxes.de/raidboxes-features/webseite-klonen" target="_blank">Klonen-Feature</a>.</p>
                <p>Wenn du deine Seite an einen anderen Nutzer übertragen möchtest, nutze einfach unsere <a class="rbblue" href="https://helpcenter.raidboxes.de/agenturen-und-webdesigner/verwaltung/besitzerwechsel" target="_blank">Besitzerwechselfunktion</a>.</p>
                <p>WICHTIG: Denke bitte daran den Migrator wieder von der BOX zu löschen.</p></button>');
    }

    private function print_start_container() {
        print('<p>Hi '.ucfirst(wp_get_current_user()->user_login).',</p><p>danke, dass du den RAIDBOXES-Migrator nutzt. 
            Um den kostenlosen Umzug  zu starten, klicke jetzt auf "<i>Umzug beginnen</i>".<br /><br>
            <b>Hinweis</b>: Wir erstellen eine Kopie und nehmen keine Änderungen an deiner Live-Seite vor. <br><br>
            Weitere Informationen zur Migration  findest du <a href="https://raidboxes.de/wordpress-umzug" class="rbblue" target="_blank">hier.</a></p><br>');
    }

    private function print_migrating_container() {
        print('<p>Der Kopiervorgang wurde gestartet. Die Dauer hängt von der Größe und den Inhalten deiner Seite ab.<br><br>
                        <b>Wichtig: </b> Bitte nimm in dieser Zeit keine Änderungen an deinem WordPress vor. <br></p>');
    }

    public function print_size_warning($size_ok) {
        if ($size_ok == 'WARNING') {
            print(sprintf('<button class="txtlft rbyellowborder nohover nocursor bgwhite">WARNUNG: Deine Seite benötigt mehr als 4,5GB Speicher und passt somit nicht auf eine STARTER ODER MINI DEMO-BOX.<br>
                                   Bitte prüfe, ob du Speicher frei machen kannst, oder wähle eine BOX-Konfiguration, die mindestens PRO entspricht. <br>
                                   Um deinen Speicherplatz zu analysieren, nutze am besten unser kostenloses Plugin <a target="_blank" class="rbblue" href="/wp-admin/plugin-install.php?s=disk+usage+sunburst&tab=search&type=term">"Disk Usage Sunburst."</a> </button>'));
        }
    }

    public function print_php_warning($php_ok) {
        if ($php_ok == 'WARNING') {
            print(sprintf('<button class="txtlft rbyellowborder nohover nocursor bgwhite">WARNUNG: Deine Seite benutzt eine PHP-Version unter 7.1<br>
                                    Der Support für PHP 5.6 und 7.0 ist bereits ausgelaufen.<br> 
                                    BOXEN bei RAIDBOXES laufen standardmäßig mit PHP 7.1. <br>
                                    Beachte dies bitte, bei der Kontrolle deiner Seie nach der Migration</button>'));
        }
    }

	public function print_reactivate_plugins($plugins) {
		if (!empty($plugins)) {
			print(sprintf('<button class="txtlft rbgreenborder nohover nocursor bgwhite">Wir haben Plugins für dich deaktiviert.</br>
						Wenn du sie wieder aktivieren möchtest, klicke
                        <a class="rbblue" href="%s/raidboxes-migrator/includes/reactivate.php">hier.</a></button><br>', WP_PLUGIN_URL));
		}
	}

    public function print_incompatible_warning($incompatibles) {
        if (!empty($incompatibles)) {
            print(sprintf('<button class="txtlft rbyellowborder nohover nocursor bgwhite">WARNUNG: Auf deiner Seite
                        wurden folgende Plugins entdeckt, die mit dem RAIDBOXES-Migrator inkompatibel sind: </br>'));
            $this->print_incompatible_list($incompatibles);
            print(sprintf('Um eine erfolgreiche Migration zu gewährleisten, deaktiviere diese Plugins bitte
                        <a class="rbblue" href="%s/raidboxes-migrator/includes/deactivate.php">hier.</a></button><br>', WP_PLUGIN_URL));
        }
    }

    private function print_incompatible_list($incompatibles) {
        print '<ul>';
        foreach ($incompatibles as $plugin) {
            $this->print_incompatible_plugin($plugin);
        }
        print '</ul>';
    }

    private function print_incompatible_plugin($plugin) {
        print(sprintf('<li class="rbred">%s</li>', $plugin));
    }

    public function print_start_button($size) {
        $dashboard_url = $this->make_dashboard_string($size);
        print(sprintf('<p><a href="%s"><button class="rbblue rbblueborder bgwhite" style="margin-top: 10px;">Umzug beginnen</button></a></p>',
                      $dashboard_url));
    }

    private function make_dashboard_string($size) {
        $parts = explode('/', WP_CONTENT_URL);
        $wp_content_dir = $parts[sizeof($parts)-1];

        global $wpdb;
        $info = $wpdb->get_results('SELECT version()', ARRAY_A);
        $sqlversion= $info[0]['version()'];


        $meta = array(
            'wp_url'            =>  get_site_url(),
            'root_path'         =>  get_home_path(),
            'wp_content_dir'    =>  $wp_content_dir,
            'site_size'         =>  $size,
            'php_version'       =>  phpversion(),
            'sql_version'       =>  $sqlversion,
            'table_prefix'      =>  $wpdb->base_prefix,
            'php_memory_limit'  =>  $this->make_memory_string(),
            'os'                =>  PHP_OS,

        );
        $dashboard_url = "http://dashboard.raidboxes.de/start_migrator?";
        foreach ($meta as $key => $value) {
            $dashboard_url = sprintf("%s%s=%s&", $dashboard_url, $key, urlencode($value));
        }
        $dashboard_url = substr($dashboard_url, 0, -1);
        return $dashboard_url;
    }

    public function print_dashboard_button() {
        print(sprintf('<p><a href="http://dashboard.raidboxes.de"><button class="rbblue rbblueborder bgwhite" style="margin-top: 10px;">Zum RAIDBOXES-Dashboard</button></a>
                        <a href="%s/raidboxes-migrator/includes/reset.php"><button class="rbblue rbblueborder bgwhite" style="margin-top: 10px;">Umzug erneut starten</button></a></p>',
            WP_PLUGIN_URL));
    }

    public function print_deactivated_success() {
        print(sprintf('<p><b>Inkompatible Plugins erfolgreich deaktiviert <i class="testcheckicon fa fa-check rbblue" aria-hidden="true"></i></b></p>'));
    }

    public function print_reactivated_success() {
        print(sprintf('<p><b>Plugins erfolgreich aktiviert <i class="testcheckicon fa fa-check rbblue" aria-hidden="true"></i></b></p>'));
    }

    public function print_not_possible($reason) {
        $offline_umzug = "Du kannst aber einen <a class='rbblue' target='_blank' href='https://dashboard.raidboxes.de/migration/wp_migration'> Gratis-Umzug</a> einreichen und wir erledigen ihn für dich.";
        switch ($reason) {
            case 'VERSION' :
                $this->print_sorry('wir haben festgestellt, dass du nicht die aktuellste Version des Plugins verwendest. <br>
                                    Bitte lade dir die neueste Version <a class=rbblue target=_blank href=https://dashboard.raidboxes.de/migrator>hier</a> herunter um Fortzufahren.');
                break;
            case 'MULTISITE' :
                $this->print_sorry('wir haben festgestellt, dass du eine Multisite hast. <br>
                                    Leider sind Multisites mit unserer Infrastruktur nicht komptabil.');
                break;
            case 'OS' :
                $this->print_sorry('wir haben festgestellt, dass du auf einem Windows-Server hostest. <br>
                                    Leider kann der RB-Migrator mit Webseiten auf diesem System nicht arbeiten.<br>'. $offline_umzug);
                break;
            case 'PHP' :
                $this->print_sorry('wir haben festgestellt, dass deine PHP-Version unter 5.6 liegt.<br>
                                    Leider ist deine PHP-Version mit dem RB-Migrator inkompatibel.<br>'. $offline_umzug);
                break;
            case '403' :
                $this->print_sorry('wir haben festgestellt, dass deine Seite unsere API blockiert. <br>
                                    Leider können wir deine Seite daher nicht mit dem RB-Migrator umziehen.<br>' . $offline_umzug);
                break;
            case '404' :
                $this->print_sorry('wir haben festgestellt, dass deine Seite unsere API blockiert. <br>
                                    Leider können wir deine Seite daher nicht mit dem RB-Migrator umziehen.<br>' . $offline_umzug);
                break;
            case 'CURL' :
                $this->print_sorry('wir haben festgestellt, dass deine Seite kein PHP-cURL unterstützt. <br>
                                    Ohne diese Funktion kannst du den RB-Migrator leider nicht verwenden.<br>' . $offline_umzug);
                break;
            case 'SITEPRESS' :
                $this->print_sorry('wir haben festgestellt, dass du Sitepress verwendest. <br>
                                    Leider kann der RB-Migrator Seiten mit diesem Setup nicht umziehen.<br>' . $offline_umzug);
                break;
            case 'FTP' :
                $this->print_sorry('wir haben festgestellt, dass deine Seite die PHP-FTP-Funktionen nicht unterstützt. <br>
                                    Leider kann der RB-Migrator ohne diese Funktionen nicht arbeiten.<br>' . $offline_umzug);
                break;
        }
    }

    public function print_sorry($message) {
        print ( sprintf('<button class="txtlft rbyellow rbyellowborder nohover nocursor bgwhite">Hi '.ucfirst(wp_get_current_user()->user_login).',<br>%s</button>', $message));
    }

    public function print_welcome() {
        print('<p>Willkommen '.ucfirst(wp_get_current_user()->user_login).',</p><p>du bist erfolgreich bei RAIDBOXES angekommen.</p>
            <p>Du kannst das Plugin jetzt von dieser Seite löschen.</p>');
    }



    public function print_system_information($size) {
        $migrationManager = new MigrationManager();

        //$this->print_root_dir_information(get_home_path());

        //$this->print_root_path_information();

        //$this->print_wp_content_url_information();

        //$this->print_plugin_url_information();

        $size_string = $this->convert_byte($size);
        $this->print_size_information($size_string, $migrationManager->is_size_ok($size));

        $this->print_php_version_information($migrationManager->is_php_version_compatible());
        $this->print_sql_information();

        $this->print_table_prefix();

        $memory = $this->make_memory_string();
        $this->print_memory_limit_information($memory);

        $this->print_os_information($migrationManager->is_os_compatible());

    }

    public function print_plugin_url_information() {
        $this->print_p( sprintf('Plugin-URL: <b>%s</b>', plugins_url()));
    }

    public function print_root_dir_information($root_dir) {
        $this->print_p( sprintf('Stammverzeichnis: <b>%s</b>', $root_dir));
    }

    public function print_root_path_information() {
        $this->print_p( sprintf('Start-Pfad: <b>%s</b>', get_site_url()));
    }

    public function print_wp_content_url_information() {
        $parts = explode('/', WP_CONTENT_URL);
        $wp_content_dir = $parts[sizeof($parts)-1];
        $this->print_p( sprintf('WP-Content-DIR: <b>%s</b>', WP_CONTENT_DIR));
    }

    public function print_sql_information() {
        global $wpdb;
        $info = $wpdb->get_results('SELECT version()', ARRAY_A);
        $version = $info[0]['version()'];
        $this->print_p( sprintf('SQL-Version: <b>%s</b>', $version));
    }

    public function print_size_information($size_string, $checkresult = true) {
        $checkicon = $this->get_checkicon($checkresult);
        $this->print_p( sprintf('Seitengröße: <b>%s</b>'.$checkicon, $size_string));
    }

    public function print_php_version_information($checkresult = true) {
        $checkicon = $this->get_checkicon($checkresult);
        $this->print_p( sprintf('PHP Version: <b>%s</b>'.$checkicon, phpversion()));
    }

    public function print_memory_limit_information($memory_limit, $checkresult = true) {
        $checkicon = $this->get_checkicon($checkresult);
        $this->print_p( sprintf('PHP Memory Limit: <b>%s</b>'.$checkicon, $memory_limit ));
    }

    public function print_os_information($checkresult = true) {
        $checkicon = $this->get_checkicon($checkresult);
        $this->print_p( sprintf('Betriebssystem: <b>%s</b>'.$checkicon, PHP_OS));
    }

    private function print_table_prefix($checkresult = true) {
        global $wpdb;
        $prefix = $wpdb->base_prefix;
        $checkicon = $this->get_checkicon($checkresult);
        $this->print_p( sprintf('Tabellen-Präfix: <b>%s</b>'.$checkicon, $prefix ));
    }

    public function print_banner($url) {
        print('<iframe src="'.$url.'" frameborder="0"></iframe>');
    }

    public function print_right_banner($url) {
        print('<iframe src="'.$url.'" frameborder="0" style="height: 800px;"></iframe>');
    }

	public function print_footer_banner_fallback() {
		print('<div class="footersection flleft">
                    <div id="rblogo">
                        <a href="https://raidboxes.de/" title="RAIDBOXES GmbH" target="_blank"><img id="logorbimg" src="'.plugins_url().'/raidboxes-migrator/img/rb_logo_neu.svg" alt="Raidboxes Logo" title="RAIDBOXES GMBH" /></a>
                    </div>
                    <address class="grey">
                        Friedrich-Ebert-Straße 7<br />
                        48153 Münster<br />
                        Deutschland
                    </address>
                </div>
                <div class="footersection flleft">
                    <h3>Unternehmen</h3>
                    <ul>
                        <li><a href="https://raidboxes.de/warum-raidboxes-wordpress-hosting/" title="Warum Raidboxes" target="_blank">Über uns</a></li>
                        <li><a href="https://raidboxes.de/features/" title="Unsere Features" target="_blank">Features</a></li>
                        <li><a href="https://raidboxes.de/wordpress-agentur-hosting/" title="Features für Agenturen" target="_blank">Agenturen</a></li>
                        <li><a href="https://raidboxes.de/tarife/" title="Tarife" target="_blank">Tarife</a></li>
                        <li><a href="https://raidboxes.de/lets-encrypt-gratis-ssl-zertifikat/" title="Gratis SSL" target="_blank">Gratis-SSL</a></li>
                        <li><a href="https://raidboxes.de/blog/" title="Blog" target="_blank">Blog</a></li>
                    </ul>
                </div>
                <div class="footersection flleft">
                    <h3>Support</h3>
                    <ul>
                        <li><a href="https://raidboxes.de/kontakt/" title="Kontakt zu uns" target="_blank">Kontakt</a></li>
                        <li><a href="https://intercom.help/raidboxes/" title="FAQ Bereich" target="_blank">Direkte Hilfe</a></li>
                        <li><a href="https://raidboxes.de/impressum/" title="Raidboxes Impressum" target="_blank">Impressum</a></li>
                        <li><a href="https://raidboxes.de/datenschutz/" title="Raidboxes Datenschutz" target="_blank">Datenschutz</a></li>
                        <li><a href="https://raidboxes.de/haftungsausschluss/" title="Raidboxes Haftungsausschluss" target="_blank">Haftungsausschluss</a></li>
                        <li><a href="https://raidboxes.de/agb/" title="Raidboxes AGB" target="_blank">AGB</a></li>
                    </ul>
                </div>
                <div class="footersection flleft socials">
                    <h3>Folge uns</h3>
                    <ul>
                        <li><a href="https://www.facebook.com/raidboxes" title="Raidboxes Facebook" style="color:#4267B2;" target="_blank"><i class="fa fa-facebook-official" style="color:#4267B2;" aria-hidden="true"></i> Facebook</a></li>
                        <li><a href="https://twitter.com/Raidboxes" title="Raidboxes Twitter" style="color:#1DA1F2;" target="_blank"><i class="fa fa-twitter" style="color:#1DA1F2;" aria-hidden="true"></i> Twitter</a></li>
                        <li><a href="https://instagram.com/raidboxes/" title="Raidboxes Instagram" style="color:#000;" target="_blank"><i class="fa fa-instagram" aria-hidden="true"></i> Instagram</a></li>
                    </ul>
                </div>
                <br class="clear" />');
	}

	public function print_right_banner_fallback() {
		print('<div class="bannercontent fltleft">
                <h2>Was uns auszeichnet</h2>
                <div id="awards">
                    <img src="'.plugins_url().'/raidboxes-migrator/img/awards.jpg" title="RAIDBOXES: Super Kundenzufriedenheit, Serverstandort Deutschland, 100 Prozent Ökostrom" alt="Super Kundenzufriedenheit, Serverstandort Deutschland, 100 Prozent Ökostrom" />
                </div>
              </div>');
	}

    public function get_checkicon($checkresult) {
        switch ($checkresult) {
            case 'OK':
                $checkicon = '<i class="testcheckicon fa fa-check rbblue" aria-hidden="true"></i>';
                return $checkicon;
            case 'WARNING' :
                $checkicon = '<i class="testcheckicon fa fa-exclamation-triangle rbyellow" aria-hidden="true"></i>';
                return $checkicon;
            default :
                $checkicon = '<i class="testcheckicon fa fa-times rbred" aria-hidden="true"></i>';
                return $checkicon;
        }
    }

    public function make_memory_string() {
        $memory = ini_get('memory_limit');
        if ( is_numeric($memory)) {
            return $this->convert_byte($memory);
        } else {
            return $memory;
        }
    }

    public function convert_byte($byte) {
        $split = 0;
        while ( $byte >= 1024) {
            $byte = $byte/1024;
            $split++;
        }

        switch ($split) {
            case 0:
                $unit = 'Byte';
                break;
            case 1:
                $unit = 'KiloByte';
                break;
            case 2:
                $unit = 'MB';
                break;
            case 3:
                $unit = 'GB';
                break;
        }
        return sprintf('%s %s', round($byte, 2), $unit);
    }
}