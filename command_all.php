<?php
require_once "config/db.php";
require_once "includes/auth.php";
require_login();

$action = trim($_GET['action'] ?? '');
$allowed = ['shutdown', 'restart', 'logoff'];

if (!in_array($action, $allowed, true)) {
    header("Location: computers.php");
    exit;
}

$result = $conn->query("SELECT id FROM computers");

while ($row = $result->fetch_assoc()) {
    $computer_id = (int)$row['id'];

    $insert = $conn->prepare("INSERT INTO commands (computer_id, command_type, status) VALUES (?, ?, 'pending')");
    $insert->bind_param("is", $computer_id, $action);
    $insert->execute();

    $details = ucfirst($action) . " command queued for all PCs. Computer ID " . $computer_id;
    $log = $conn->prepare("INSERT INTO logs (admin_id, computer_id, action, details) VALUES (?, ?, ?, ?)");
    $log_action = "queue_all_command";
    $log->bind_param("iiss", $_SESSION['admin_id'], $computer_id, $log_action, $details);
    $log->execute();
}

header("Location: computers.php");
exit;