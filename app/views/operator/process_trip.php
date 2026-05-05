<?php
require_once APP_ROOT . '/config/db.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/controllers/OperatorController.php';

requireRole('operator');
$operator = currentUser();

$action = $_POST['action'] ?? '';
$schedule_id = intval($_POST['schedule_id'] ?? 0);
$ticket_id = intval($_POST['ticket_id'] ?? 0);

$message = '';
$messageType = '';

if (empty($action) || $schedule_id == 0) {
    header('Location: dashboard.php?error=invalid_request');
    exit;
}

switch ($action) {
    case 'start_trip':
        $result = OperatorController::startTrip($schedule_id);
        $messageType = $result['success'] ? 'success' : 'error';
        $message = $result['message'];
        break;

    case 'end_trip':
        $result = OperatorController::endTrip($schedule_id);
        $messageType = $result['success'] ? 'success' : 'error';
        $message = $result['message'];
        break;

    case 'approve_payment':
        if ($ticket_id == 0) {
            $messageType = 'error';
            $message = 'Invalid ticket ID.';
        } else {
            $result = OperatorController::approvePayment($ticket_id);
            $messageType = $result['success'] ? 'success' : 'error';
            $message = $result['message'];
        }
        break;

    case 'board_passenger':
        if ($ticket_id == 0) {
            $messageType = 'error';
            $message = 'Invalid ticket ID.';
        } else {
            $result = OperatorController::boardPassenger($ticket_id);
            $messageType = $result['success'] ? 'success' : 'error';
            $message = $result['message'];
        }
        break;

    default:
        $messageType = 'error';
        $message = 'Invalid action.';
}

// Redirect back with message
$redirect = "dashboard.php?";
if ($messageType === 'success') {
    $redirect .= "success=" . urlencode($message);
} else {
    $redirect .= "error=" . urlencode($message);
}

header('Location: ' . $redirect);
exit;
?>
