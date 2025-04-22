function plugin_init_masterticketsync() {  // ← All lowercase
    global $PLUGIN_HOOKS;
    
    $PLUGIN_HOOKS = [
        'csrf_compliant' => ['masterticketsync' => true],  // ← Consistent lowercase
        'menu_toadd'     => ['admin' => 'PluginMasterticketsyncMenu'],  // ← Add this
        'config_page'    => ['masterticketsync' => 'front/config.form.php']  // ← Add if needed
    ];
    
    // Register your ticket class
    Plugin::registerClass('PluginMasterticketsyncTicket', [
        'addtabon' => ['Ticket']
    ]);
}
