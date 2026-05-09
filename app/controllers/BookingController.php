<?php
require_once APP_ROOT . '/config/db.php';
require_once APP_ROOT . '/app/core/Auth.php';
require_once APP_ROOT . '/app/models/Schedule.php';
require_once APP_ROOT . '/app/models/Ticket.php';

requireRole('passenger'); // kept here as this file is a standalone action script

$action = $_GET['action'] ?? '';
if ($action === 'book' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $schedule_id = intval($_POST['schedule_id'] ?? 0);
    $seats = intval($_POST['seats'] ?? 0);

    if ($schedule_id <= 0 || $seats <= 0) {
        $_SESSION['error'] = 'Please select a valid schedule and seat count.';
        header('Location: ' . BASE_URL . 'search');
        exit;
    }

    $schedule = Schedule::findById($schedule_id);
    if (!$schedule) {
        $_SESSION['error'] = 'Selected schedule was not found.';
        header('Location: ' . BASE_URL . 'search');
        exit;
    }

    $ticket_id = Ticket::create($_SESSION['user_id'], $schedule_id, $seats);
    header('Location: ' . BASE_URL . 'receipt?ticket_id=' . intval($ticket_id));
    exit;
}

header('Location: ' . BASE_URL . 'search');
exit;
