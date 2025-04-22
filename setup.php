<?php
/**
 * Master Ticket Sync plugin setup
 *
 * @package MasterTicketSync
 * @license GPLv2+
 */

// Prevent direct access
if (!defined('GLPI_ROOT')) {
    die("Sorry - you can't access this file directly");
}

function plugin_version_masterticketsync() {
    return [
        'name'           => 'Master Ticket Sync',
        'version'        => '1.0.0',
        'author'         => 'Matthew Nixon',
        'license'        => 'GPLv2+',
        'homepage'       => 'https://github.com/MDNSCode/masterticketsync',
        'requirements'   => [
            'glpi' => [
                'min' => '10.0',
                'max' => '11.0'
            ],
            'php' => [
                'min' => '7.4'
            ]
        ]
    ];
}

function plugin_masterticketsync_check_prerequisites() {
    if (version_compare(GLPI_VERSION, '10.0', '<') || version_compare(GLPI_VERSION, '11.0', '>')) {
        echo "This plugin requires GLPI 10.0 to 11.0";
        return false;
    }
    return true;
}

function plugin_masterticketsync_check_config($verbose = false) {
    if ($verbose) {
        echo "Master Ticket Sync configuration is OK";
    }
    return true;
}

function plugin_masterticketsync_install() {
    global $DB;

    // Create plugin tables if needed
    $migration = new Migration(plugin_version_masterticketsync()['version']);

    // Example table creation (modify as needed)
    if (!$DB->tableExists('glpi_plugin_masterticketsync_tickets')) {
        $query = "CREATE TABLE `glpi_plugin_masterticketsync_tickets` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `tickets_id` INT(11) NOT NULL,
            `master_ticket` INT(11) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            INDEX (`tickets_id`),
            INDEX (`master_ticket`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $DB->queryOrDie($query, $DB->error());
    }

    // Add configuration if needed
    Config::setConfigurationValues('plugin:masterticketsync', [
        'sync_enabled' => 1,
        'default_category' => 0
    ]);

    return true;
}

function plugin_masterticketsync_uninstall() {
    global $DB;

    // Clean configuration
    $config = new Config();
    $config->deleteByCriteria(['context' => 'plugin:masterticketsync']);

    // Remove tables if needed (uncomment when ready)
    // $tables = [
    //     'glpi_plugin_masterticketsync_tickets'
    // ];
    // foreach ($tables as $table) {
    //     $DB->queryOrDie("DROP TABLE IF EXISTS `$table`", $DB->error());
    // }

    return true;
}
