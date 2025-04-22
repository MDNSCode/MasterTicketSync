<?php
/**
 * CSRF Token Validation for Master Ticket Sync Plugin
 * @package MasterTicketSync
 * @license GPLv2+
 */

// Prevent direct access
if (!defined('GLPI_ROOT')) {
    die("Direct access not allowed");
}

/**
 * Validates CSRF token in form submissions.
 *
 * @return void
 */
function plugin_masterticketsync_validate_csrf() {
    if (!isset($_POST['glpi_csrf_token']) || $_POST['glpi_csrf_token'] !== $_SESSION['glpi_csrf_token']) {
        die("CSRF validation failed. Please retry.");
    }
}

/**
 * Regenerates CSRF token after a successful request.
 *
 * @return void
 */
function plugin_masterticketsync_regenerate_csrf() {
    $_SESSION['glpi_csrf_token'] = bin2hex(random_bytes(32));
}
