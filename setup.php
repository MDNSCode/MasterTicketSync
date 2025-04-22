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
    if (version_compare(GLPI_VERSION, '10.0', '<')) {
        echo "GLPI 10.0+ required";
        return false;
    }
    return true;
}

function plugin_masterticketsync_install() {
    global $DB;

    $migration = new Migration(plugin_version_masterticketsync()['version']);

    // Create the main relations table
    if (!$DB->tableExists('glpi_plugin_masterticketsync_relations')) {
        $query = "CREATE TABLE `glpi_plugin_masterticketsync_relations` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `master_ticket_id` INT(11) UNSIGNED NOT NULL,
            `slave_ticket_id` INT(11) UNSIGNED NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `master_slave_unique` (`master_ticket_id`, `slave_ticket_id`),
            KEY `master_ticket_id` (`master_ticket_id`),
            KEY `slave_ticket_id` (`slave_ticket_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $DB->queryOrDie($query, $DB->error());

        // Add foreign keys separately to ensure compatibility
        $migration->addForeignKey(
            'glpi_plugin_masterticketsync_relations',
            'master_ticket_id',
            'glpi_tickets',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $migration->addForeignKey(
            'glpi_plugin_masterticketsync_relations',
            'slave_ticket_id',
            'glpi_tickets',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    // Insert default configuration
    Config::setConfigurationValues('plugin:masterticketsync', [
        'enable_auto_sync' => 1,
        'sync_interval'    => 3600,
        'sync_priority'    => 3
    ]);

    // Create the notification template if needed
    $template = new NotificationTemplate();
    if (!$template->getFromDBByCrit(['name' => 'Master Ticket Sync Alert'])) {
        $template_id = $template->add([
            'name'     => 'Master Ticket Sync Alert',
            'comment'  => 'Notification for master-slave ticket synchronization',
            'itemtype' => 'Ticket'
        ]);
    }

    return true;
}

function plugin_masterticketsync_uninstall() {
    global $DB;

    $config = new Config();
    $config->deleteByCriteria(['context' => 'plugin:masterticketsync']);

    // Keep these commented during development
    // $tables = [
    //     'glpi_plugin_masterticketsync_relations',
    //     'glpi_plugin_masterticketsync_config'
    // ];
    // foreach ($tables as $table) {
    //     $DB->queryOrDie("DROP TABLE IF EXISTS `$table`", $DB->error());
    // }

    return true;
}
