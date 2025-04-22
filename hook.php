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

    // 1. CSRF Compliance Declaration (Fixes your error)
    $PLUGIN_HOOKS['csrf_compliant']['masterticketsync'] = true;

    // 2. Class Registrations
    Plugin::registerClass('PluginMasterticketsyncTicketSync', [
        'addtabon' => ['Ticket']
    ]);

    // 3. Menu Entries
    $PLUGIN_HOOKS['menu_toadd']['masterticketsync'] = [
        'admin' => 'PluginMasterticketsyncMenu',
        'ticket' => 'PluginMasterticketsyncTicket'
    ];

    // 4. Hooks
    $PLUGIN_HOOKS['item_add']['masterticketsync'] = [
        'Ticket' => ['PluginMasterticketsyncTicketSync', 'handleNewTicket']
    ];
    
    $PLUGIN_HOOKS['item_update']['masterticketsync'] = [
        'Ticket' => ['PluginMasterticketsyncTicketSync', 'handleTicketUpdate']
    ];

    // 5. Config Page
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
