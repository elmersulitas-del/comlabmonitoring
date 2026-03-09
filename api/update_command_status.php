<?php
require_once "../config/db.php";

header("Content-Type: application/json");

$token = trim($_POST['token'] ?? '');
$command_id = (int)($_POST['command_id'] ?? 0);
$status = trim($_POST['status'] ?? '');
$remarks = trim($_POST['remarks'] ?? '');

$allowed = ['executed', 'failed'];

if ($token === '' || $command_id <= 0 || !in_array($status, $allowed, true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM computers WHERE agent_token = ? LIMIT 1");
$stmt->bind_param("s", $token);
$stmt->execute();
$computer = $stmt->get_result()->fetch_assoc();

if (!$computer) {
    echo json_encode(['success' => false, 'message' => 'Invalid token']);
    exit;
}

$computer_id = (int)$computer['id'];

$update = $conn->prepare("
    UPDATE commands
    SET status = ?, executed_at = NOW(), remarks = ?
    WHERE id = ? AND computer_id = ?
");
$update->bind_param("ssii", $status, $remarks, $command_id, $computer_id);
$update->execute();

$details = "Command ID {$command_id} marked as {$status}. {$remarks}";
$log = $conn->prepare("INSERT INTO logs (computer_id, action, details) VALUES (?, ?, ?)");
$log_action = "agent_command_status";
$log->bind_param("iss", $computer_id, $log_action, $details);
$log->execute();

echo json_encode(['success' => true, 'message' => 'Status updated']);