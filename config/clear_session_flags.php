<?php

// =======================================================================
// PHP SCRIPT START - TIMEZONE CORRECTION
// =======================================================================

// Example: Set the timezone to Manila (Philippines Standard Time)
date_default_timezone_set('Asia/Manila');
session_start();

// Clear interruption flags
unset($_SESSION['continue_transaction']);
unset($_SESSION['show_payment_modal']);

echo json_encode(['success' => true, 'message' => 'Session flags cleared']);
?>