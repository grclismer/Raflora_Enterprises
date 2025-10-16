<?php
session_start();

// Clear interruption flags
unset($_SESSION['continue_transaction']);
unset($_SESSION['show_payment_modal']);

echo json_encode(['success' => true, 'message' => 'Session flags cleared']);
?>