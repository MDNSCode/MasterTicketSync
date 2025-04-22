<?php
/**
 * Master Ticket Sync plugin hooks
 * 
 * @package MasterTicketSync
 * @license GPLv2+
 */

// Prevent direct access
if (!defined('GLPI_ROOT')) {
    die("Sorry - you can't access this file directly");
}

function plugin_init_masterticketsync() {
    global $PLUGIN_HOOKS;

    // Required hooks
    $PLUGIN_HOOKS['csrf_compliant']['masterticketsync'] = true;
    
    // Add plugin to the admin menu
    $PLUGIN_HOOKS['menu_toadd']['masterticketsync'] = [
        'admin' => 'PluginMasterticketsyncMenu'
    ];

    // Register the ticket class to show tabs on Ticket items
    Plugin::registerClass('PluginMasterticketsyncTicket', [
        'addtabon' => ['Ticket']
    ]);

    // Add configuration page
    $PLUGIN_HOOKS['config_page']['masterticketsync'] = 'front/config.form.php';

    // Add plugin to helpdesk menu (if needed)
    // $PLUGIN_HOOKS['menu_toadd']['masterticketsync'] = [
    //     'helpdesk' => 'PluginMasterticketsyncMenu'
    // ];

    // Hook for when a ticket is shown
    $PLUGIN_HOOKS['post_item_form']['masterticketsync'] = [
        'Ticket' => 'plugin_masterticketsync_postshow'
    ];

    // Hook for when a ticket is updated
    $PLUGIN_HOOKS['item_update']['masterticketsync'] = [
        'Ticket' => 'plugin_masterticketsync_ticketupdate'
    ];
}

function plugin_version_masterticketsync() {
    return plugin_version_masterticketsync();
}

function plugin_masterticketsync_postshow($params) {
    // Your code to execute after ticket form is shown
}

function plugin_masterticketsync_ticketupdate($item) {
    // Your code to execute when a ticket is updated
    if ($item->getType() == 'Ticket') {
        // Handle ticket update logic
    }
}
