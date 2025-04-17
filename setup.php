<?php
/**
 * Master Ticket Sync - Plugin setup
 * @author Matthew Nixon
 * @license MIT
 */

// Plugin version constants
define('PLUGIN_MASTERTICKETSYNC_VERSION', '1.0.0');
define('PLUGIN_MASTERTICKETSYNC_MIN_GLPI', '10.0.0');
define('PLUGIN_MASTERTICKETSYNC_MAX_GLPI', '11.0.99');

/**
 * Initialize the plugin
 */
function plugin_init_masterticketsync() {
    global $PLUGIN_HOOKS;
    
    // Register only the hooks we need
    $PLUGIN_HOOKS['item_update']['masterticketsync'] = 'plugin_masterticketsync_item_update';
    $PLUGIN_HOOKS['followup_add']['masterticketsync'] = 'plugin_masterticketsync_followup_add';
    $PLUGIN_HOOKS['itilsolution_add']['masterticketsync'] = 'plugin_masterticketsync_solution_add';
}

/**
 * Plugin version information
 */
function plugin_version_masterticketsync() {
    return [
        'name'           => 'Master Ticket Sync',
        'version'        => PLUGIN_MASTERTICKETSYNC_VERSION,
        'author'         => 'Matthew Nixon',
        'license'        => 'MIT',
        'homepage'       => 'https://github.com/MDNSCode/MasterTicketSync',
        'requirements'   => [
            'glpi' => [
                'min' => PLUGIN_MASTERTICKETSYNC_MIN_GLPI,
                'max' => PLUGIN_MASTERTICKETSYNC_MAX_GLPI,
            ]
        ]
    ];
}

/**
 * Installation (no database setup needed)
 */
function plugin_masterticketsync_install() {
    return true;
}

/**
 * Uninstallation
 */
function plugin_masterticketsync_uninstall() {
    return true;
}

/**
 * Check prerequisites (PHP version, GLPI version, etc.)
 */
function plugin_masterticketsync_check_prerequisites() {
    return true;
}

/**
 * Check if plugin configuration is valid
 */
function plugin_masterticketsync_check_config($verbose = false) {
    return true;
}
?>
