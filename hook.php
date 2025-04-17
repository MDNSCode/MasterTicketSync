<?php
function plugin_masterticketsync_register_hooks() {
    global $PLUGIN_HOOKS;
    
    $PLUGIN_HOOKS['item_update']['masterticketsync'] = 'plugin_masterticketsync_item_update';
    $PLUGIN_HOOKS['followup_add']['masterticketsync'] = 'plugin_masterticketsync_followup_add';
    $PLUGIN_HOOKS['itilsolution_add']['masterticketsync'] = 'plugin_masterticketsync_solution_add';
}
?>
