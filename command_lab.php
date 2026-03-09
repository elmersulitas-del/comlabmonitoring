<?php
require_once "config/db.php";
require_once "includes/auth.php";
require_login();

$lab_id = (int)($_GET['lab_id'] ?? 0);
$action = trim($_GET['action'] ?? '');

$allowed = ['shutdown', 'restart', 'logoff'];

if ($lab_id <= 0 || !in_array($action, $allowed, true)) {
    header("Location: labs.php");
    exit;
}

$stmt = $conn->prepare("SELECT id FROM computers WHERE lab_id = ?");
$stmt->bind_param("i", $lab_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $computer_id = (int)$row['id'];

    $insert = $conn->prepare("INSERT INTO commands (computer_id, command_type, status) VALUES (?, ?, 'pending')");
    $insert->bind_param("is", $computer_id, $action);
    $insert->execute();

    $details = ucfirst($action) . " command queued for computer ID " . $computer_id . " in lab ID " . $lab_id;
    $log = $conn->prepare("INSERT INTO logs (admin_id, computer_id, action, details) VALUES (?, ?, ?, ?)");
    $log_action = "queue_lab_command";
    $log->bind_param("iiss", $_SESSION['admin_id'], $computer_id, $log_action, $details);
    $log->execute();
}

header("Location: labs.php");
exit;