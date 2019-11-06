<?php

require_once('includes/include.php');

echo get_status();

function get_status() {
    $migrationManager = new MigrationManager();

    if ($migrationManager->get_migration_status() == MigrationManager::STATUS_COMPLETE) {
        return json_encode('Erfolgreich beendet.');
    }

    $data = get_option('rb_migrate_data');
    $sig = get_option('rb_migrate_key');
    if ($data === '' || $sig === '')
        return json_encode('Migrations-Key nicht gültig!');
    $status = $migrationManager->get_response(sprintf('https://dashboard.raidboxes.de/migration/status?data=%s&sig=%s', $data, $sig));

    switch ($status) {
        case strpos($status, "Downloade") !== false :
            $migrationManager->set_migration_status('DOWNLOAD');
            $status = sprintf('DOWNLOAD %s , %s', calc_percent($status), $status);
            return json_encode($status);
        case strpos($status, "Exportiere") !== false :
            $migrationManager->set_migration_status('DUMP_EXPORT');
            $status = sprintf('EXPORT %s', calc_percent($status));
            return json_encode($status);
        case strpos($status, "Importiere") !== false :
            $migrationManager->set_migration_status('DUMP_IMPORT');
            $status = sprintf('IMPORT %s', calc_percent($status));
            return json_encode($status);
        case strpos($status, "Kein Umzug") !== false :
            $migrationManager->migration_failed();
            $status = 'Fehler beim Umzug.Der Prozess wird beendet.';
            return json_encode($status);
        case strpos($status, "Sorry...") !== false :
            $status = 'Status wird geladen...';
            return json_encode($status);
        default :
            return json_encode($status);
    }
}

function calc_percent($status) {
    $words  = explode(' ', $status);
    $count  = $words[2];
    $max    = $words[4];
    $percent = $count*100/$max;
    return round($percent);
}