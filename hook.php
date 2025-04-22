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

    // ========== CORE CSRF CONFIGURATION ==========
    $PLUGIN_HOOKS['csrf_compliant']['masterticketsync'] = true;
    
    // ========== SECURITY HEADERS ==========
    $PLUGIN_HOOKS['add_css']['masterticketsync'] = 'security_headers.php';
    
    // ========== PLUGIN REGISTRATION ==========
    Plugin::registerClass('PluginMasterticketsyncTicketSync', [
        'addtabon' => ['Ticket'],
        'csrf'     => true  // Explicit CSRF protection for class methods
    ]);

    // ========== MENU INTEGRATION ==========
    $PLUGIN_HOOKS['menu_toadd']['masterticketsync'] = [
        'admin'  => 'PluginMasterticketsyncMenu',
        'ticket' => 'PluginMasterticketsyncTicket'
    ];

    // ========== TICKET HOOKS ==========
    $PLUGIN_HOOKS['item_add']['masterticketsync'] = [
        'Ticket' => ['PluginMasterticketsyncTicketSync', 'handleNewTicket']
    ];
    
    $PLUGIN_HOOKS['item_update']['masterticketsync'] = [
        'Ticket' => ['PluginMasterticketsyncTicketSync', 'handleTicketUpdate']
    ];

    // ========== CONFIGURATION ==========
    $PLUGIN_HOOKS['config_page']['masterticketsync'] = [
        'page'  => 'front/config.form.php',
        'title' => 'Master Ticket Sync Configuration'
    ];
}

// ========== SECURITY HEADERS FILE ==========
// Create new file: security_headers.php in plugin root
// Content: <?php header("X-Content-Type-Options: nosniff");
