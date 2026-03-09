<?php
require_once "config/db.php";
require_once "includes/auth.php";
require_login();

$computer_id = (int)($_GET['id'] ?? 0);
$action = trim($_GET['action'] ?? '');

$allowed = ['shutdown', 'restart', 'logoff'];

if ($computer_id <= 0 || !in_array($action, $allowed, true)) {
    header("Location: computers.php");
    exit;
}

$stmt = $conn->prepare("INSERT INTO commands (computer_id, command_type, status) VALUES (?, ?, 'pending')");
$stmt->bind_param("is", $computer_id, $action);
$stmt->execute();

$details = ucfirst($action) . " command queued for computer ID " . $computer_id;
$log = $conn->prepare("INSERT INTO logs (admin_id, computer_id, action, details) VALUES (?, ?, ?, ?)");
$log_action = "queue_command";
$log->bind_param("iiss", $_SESSION['admin_id'], $computer_id, $log_action, $details);
$log->execute();

header("Location: computers.php");
exit;