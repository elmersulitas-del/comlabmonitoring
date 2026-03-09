<?php
require_once "../config/db.php";

header("Content-Type: application/json");

$token = trim($_GET['token'] ?? '');

if ($token === '') {
    echo json_encode(['success' => false, 'message' => 'Missing token']);
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

$cmd = $conn->prepare("
    SELECT id, command_type, created_at
    FROM commands
    WHERE computer_id = ? AND status = 'pending'
    ORDER BY id ASC
    LIMIT 1
");
$cmd->bind_param("i", $computer_id);
$cmd->execute();
$result = $cmd->get_result();
$command = $result->fetch_assoc();

if (!$command) {
    echo json_encode(['success' => true, 'has_command' => false]);
    exit;
}

$update = $conn->prepare("UPDATE commands SET status = 'sent' WHERE id = ?");
$update->bind_param("i", $command['id']);
$update->execute();

echo json_encode([
    'success' => true,
    'has_command' => true,
    'command_id' => (int)$command['id'],
    'command_type' => $command['command_type']
]);