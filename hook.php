<?php
// security check
if (!defined('GLPI_ROOT')) {
    die("Direct access not allowed");
}

function plugin_init_masterticketsync() {
    global $PLUGIN_HOOKS;

    // 1. CSRF Compliance (fixes your error)
    $PLUGIN_HOOKS['csrf_compliant']['masterticketsync'] = true;

    // 2. Add security headers
    $PLUGIN_HOOKS['add_css']['masterticketsync'] = function() {
        header("X-Content-Type-Options: nosniff");
        header("X-Frame-Options: DENY");
    };

    // 3. Register main class
    Plugin::registerClass('PluginMasterticketsyncTicket', [
        'addtabon' => ['Ticket'],
        'csrf' => true  // Explicit CSRF protection
    ]);

    // 4. Admin menu integration
    $PLUGIN_HOOKS['menu_toadd']['masterticketsync'] = [
        'admin' => 'PluginMasterticketsyncMenu'
    ];

    // 5. Ticket hooks
    $PLUGIN_HOOKS['item_add']['masterticketsync'] = [
        'Ticket' => ['PluginMasterticketsyncTicket', 'item_add_ticket']
    ];
    
    $PLUGIN_HOOKS['item_update']['masterticketsync'] = [
        'Ticket' => ['PluginMasterticketsyncTicket', 'item_update_ticket']
    ];
}
