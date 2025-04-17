<?php
/**
 * Master Ticket Sync - Ticket Synchronization Logic
 * @author Matthew Nixon
 * @license MIT
 */

use Glpi\Toolbox\Log;
use Glpi\Ticket\Ticket;
use Glpi\Ticket\Followup as TicketFollowup;
use Glpi\Ticket\Ticket_Ticket;
use Glpi\ITILSolution\ITILSolution;

/**
 * Sync followups from parent to child tickets
 */
function plugin_masterticketsync_followup_add(TicketFollowup $followup) {
    global $DB;

    $ticket = new Ticket();
    if (!$ticket->getFromDB($followup->fields['tickets_id'])) {
        Log::write(
            'Parent ticket not found for followup sync',
            Log::ERROR,
            ['followup_id' => $followup->getID()]
        );
        return;
    }

    try {
        $childTickets = $DB->request([
            'SELECT' => ['id'],
            'FROM'   => 'glpi_tickets_tickets',
            'WHERE'  => [
                'tickets_id_1' => $ticket->getID(),
                'link'        => Ticket_Ticket::SON_OF
            ]
        ]);

        foreach ($childTickets as $child) {
            try {
                $childFollowup = new TicketFollowup();
                $childFollowup->add([
                    'tickets_id'       => $child['id'],
                    'content'          => $followup->fields['content'],
                    'requesttypes_id'  => $followup->fields['requesttypes_id'],
                    'is_private'       => $followup->fields['is_private'],
                    'users_id'         => $followup->fields['users_id']
                ]);
            } catch (Exception $e) {
                Log::write(
                    'Failed to add followup to child ticket',
                    Log::ERROR,
                    [
                        'parent_ticket' => $ticket->getID(),
                        'child_ticket'  => $child['id'],
                        'error'         => $e->getMessage()
                    ]
                );
            }
        }
    } catch (Exception $e) {
        Log::write(
            'Failed to retrieve child tickets',
            Log::CRITICAL,
            [
                'parent_ticket' => $ticket->getID(),
                'error'         => $e->getMessage()
            ]
        );
    }
}

/**
 * Sync status changes from parent to child tickets
 */
function plugin_masterticketsync_item_update(Ticket $ticket) {
    global $DB;

    if (!in_array('status', $ticket->updates)) {
        return;
    }

    try {
        $childTickets = $DB->request([
            'SELECT' => ['id'],
            'FROM'   => 'glpi_tickets_tickets',
            'WHERE'  => [
                'tickets_id_1' => $ticket->getID(),
                'link'         => Ticket_Ticket::SON_OF
            ]
        ]);

        foreach ($childTickets as $child) {
            try {
                $childTicket = new Ticket();
                $childTicket->update([
                    'id'     => $child['id'],
                    'status' => $ticket->fields['status']
                ]);
            } catch (Exception $e) {
                Log::write(
                    'Failed to update child ticket status',
                    Log::ERROR,
                    [
                        'parent_ticket' => $ticket->getID(),
                        'child_ticket'  => $child['id'],
                        'new_status'   => $ticket->fields['status'],
                        'error'        => $e->getMessage()
                    ]
                );
            }
        }
    } catch (Exception $e) {
        Log::write(
            'Failed to retrieve child tickets for status sync',
            Log::CRITICAL,
            [
                'parent_ticket' => $ticket->getID(),
                'error'         => $e->getMessage()
            ]
        );
    }
}

/**
 * Sync solutions from parent to child tickets
 */
function plugin_masterticketsync_solution_add(ITILSolution $solution) {
    global $DB;

    $ticket = new Ticket();
    if (!$ticket->getFromDB($solution->fields['items_id'])) {
        Log::write(
            'Parent ticket not found for solution sync',
            Log::ERROR,
            ['solution_id' => $solution->getID()]
        );
        return;
    }

    try {
        $childTickets = $DB->request([
            'SELECT' => ['id'],
            'FROM'   => 'glpi_tickets_tickets',
            'WHERE'  => [
                'tickets_id_1' => $ticket->getID(),
                'link'         => Ticket_Ticket::SON_OF
            ]
        ]);

        foreach ($childTickets as $child) {
            try {
                $childSolution = new ITILSolution();
                $childSolution->add([
                    'items_id'         => $child['id'],
                    'itemtype'         => 'Ticket',
                    'content'          => $solution->fields['content'],
                    'solutiontypes_id' => $solution->fields['solutiontypes_id'],
                    'users_id'        => $solution->fields['users_id']
                ]);
            } catch (Exception $e) {
                Log::write(
                    'Failed to add solution to child ticket',
                    Log::ERROR,
                    [
                        'parent_ticket' => $ticket->getID(),
                        'child_ticket'  => $child['id'],
                        'error'         => $e->getMessage()
                    ]
                );
            }
        }
    } catch (Exception $e) {
        Log::write(
            'Failed to retrieve child tickets for solution sync',
            Log::CRITICAL,
            [
                'parent_ticket' => $ticket->getID(),
                'error'         => $e->getMessage()
            ]
        );
    }
}
?>
