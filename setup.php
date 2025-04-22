<?php
/**
 * Master Ticket Sync - Setup
 * @package MasterTicketSync
 * @license GPLv2+
 */

if (!defined('GLPI_ROOT')) {
    die("Direct access not allowed");
}

function plugin_version_masterticketsync() {
    return [
        'name'           => 'Master Ticket Sync',
        'version'        => '1.0.1',
        'author'         => 'Your Name',
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
    if (version_compare(GLPI_VERSION, '10.0', '<')) {
        echo "GLPI 10.0+ required";
        return false;
    }
    return true;
}

function plugin_masterticketsync_install() {
    global $DB;

    $migration = new Migration(plugin_version_masterticketsync()['version']);

    if (!$DB->tableExists('glpi_plugin_masterticketsync_relations')) {
        $query = "CREATE TABLE `glpi_plugin_masterticketsync_relations` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `master_ticket_id` INT(11) NOT NULL,
            `slave_ticket_id` INT(11) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `master_slave` (`master_ticket_id`,`slave_ticket_id`),
            FOREIGN KEY (`master_ticket_id`) REFERENCES `glpi_tickets` (`id`),
            FOREIGN KEY (`slave_ticket_id`) REFERENCES `glpi_tickets` (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        $DB->queryOrDie($query, $DB->error());
    }

    Config::setConfigurationValues('plugin:masterticketsync', [
        'enable_auto_sync' => 1,
        'sync_interval'    => 3600
    ]);

    return true;
}

function plugin_masterticketsync_uninstall() {
    global $DB;

    $config = new Config();
    $config->deleteByCriteria(['context' => 'plugin:masterticketsync']);

    // Keep table for data preservation (uncomment to remove)
    // $DB->query("DROP TABLE IF EXISTS `glpi_plugin_masterticketsync_relations`");

    return true;
}
