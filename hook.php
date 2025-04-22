<?php
/**
 * Master Ticket Sync - Hooks
 * @package MasterTicketSync
 */

if (!defined('GLPI_ROOT')) {
    die("Direct access not allowed");
}

function plugin_init_masterticketsync() {
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['masterticketsync'] = true;
    
    // Register class
    Plugin::registerClass('PluginMasterticketsyncTicketSync', [
        'addtabon' => ['Ticket']
    ]);

    // Add to admin menu
    $PLUGIN_HOOKS['menu_toadd']['masterticketsync'] = [
        'admin' => 'PluginMasterticketsyncMenu'
    ];

    // Ticket hooks
    $PLUGIN_HOOKS['item_add']['masterticketsync'] = [
        'Ticket' => ['PluginMasterticketsyncTicketSync', 'handleNewTicket']
    ];
    
    $PLUGIN_HOOKS['item_update']['masterticketsync'] = [
        'Ticket' => ['PluginMasterticketsyncTicketSync', 'handleTicketUpdate']
    ];

    // Add config page
    $PLUGIN_HOOKS['config_page']['masterticketsync'] = 'front/config.form.php';
}

function plugin_masterticketsync_getAddSearchOptions($itemtype) {
    $sopt = [];
    
    if ($itemtype == 'Ticket') {
        $sopt[1000] = [
            'table'     => 'glpi_plugin_masterticketsync_relations',
            'field'     => 'master_ticket_id',
            'name'      => __('Master Ticket', 'masterticketsync'),
            'datatype'  => 'dropdown'
        ];
    }
    
    return $sopt;
}
