<?php
/**
 * Master Ticket Sync - Core Logic
 * @package MasterTicketSync
 */

if (!defined('GLPI_ROOT')) {
    die("Direct access not allowed");
}

class PluginMasterticketsyncTicketSync extends CommonDBTM {

    static $rightname = 'plugin_masterticketsync';

    static function getTypeName($nb = 0) {
        return _n('Ticket Sync', 'Ticket Syncs', $nb, 'masterticketsync');
    }

    static function canCreate() {
        return Session::haveRight('plugin_masterticketsync', CREATE);
    }

    static function handleNewTicket(Ticket $ticket) {
        if (isset($ticket->input['_master_ticket_id'])) {
            self::createRelation(
                $ticket->input['_master_ticket_id'],
                $ticket->getID()
            );
        }
    }

    static function handleTicketUpdate(Ticket $ticket) {
        if (isset($ticket->input['_master_ticket_id'])) {
            self::updateRelation(
                $ticket->input['_master_ticket_id'],
                $ticket->getID()
            );
        }
    }

    static function createRelation($masterID, $slaveID) {
        global $DB;

        if (!self::validateTickets($masterID, $slaveID)) {
            return false;
        }

        $DB->insertOrDie('glpi_plugin_masterticketsync_relations', [
            'master_ticket_id' => $masterID,
            'slave_ticket_id' => $slaveID
        ]);

        Log::history(
            $masterID,
            'Ticket',
            [0, '', sprintf(__('Linked to ticket %d', 'masterticketsync'), $slaveID)],
            'PluginMasterticketsyncTicketSync'
        );

        return true;
    }

    static function validateTickets($masterID, $slaveID) {
        $ticket = new Ticket();
        
        return $ticket->getFromDB($masterID) && 
               $ticket->getFromDB($slaveID) &&
               $masterID != $slaveID;
    }

    static function getTabNameForItem(CommonGLPI $item, $withtemplate = 0) {
        if ($item->getType() == 'Ticket') {
            return __('Master Sync', 'masterticketsync');
        }
        return '';
    }

    static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0) {
        if ($item->getType() == 'Ticket') {
            self::showSyncInterface($item);
            return true;
        }
        return false;
    }

    static function showSyncInterface(Ticket $ticket) {
        global $DB;

        $iterator = $DB->request([
            'FROM'   => 'glpi_plugin_masterticketsync_relations',
            'WHERE'  => [
                'OR' => [
                    ['master_ticket_id' => $ticket->getID()],
                    ['slave_ticket_id' => $ticket->getID()]
                ]
            ]
        ]);

        // Render your interface here
    }
}
