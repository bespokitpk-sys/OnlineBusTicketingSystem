<?php
include_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/../includes/auth.php';
include_once __DIR__ . '/../models/Schedule.php';
include_once __DIR__ . '/../models/Ticket.php';

requireRole('passenger');

$action = $_GET['action'] ?? '';
if ($action === 'book' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $schedule_id = intval($_POST['schedule_id'] ?? 0);
    $seats = intval($_POST['seats'] ?? 0);

    if ($schedule_id <= 0 || $seats <= 0) {
        $_SESSION['error'] = 'Please select a valid schedule and seat count.';
        header('Location: ' . BASE_URL . 'public/search.php');
        exit;
    }

    $schedule = Schedule::findById($schedule_id);
    if (!$schedule) {
        $_SESSION['error'] = 'Selected schedule was not found.';
        header('Location: ' . BASE_URL . 'public/search.php');
        exit;
    }

    $ticket_id = Ticket::create($_SESSION['user_id'], $schedule_id, $seats);
    header('Location: ' . BASE_URL . 'public/receipt.php?ticket_id=' . intval($ticket_id));
    exit;
}

header('Location: ' . BASE_URL . 'public/search.php');
exit;
