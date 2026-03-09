<?php
require_once "../config/db.php";

header("Content-Type: application/json");

$token = trim($_POST['token'] ?? '');
$ip_address = trim($_POST['ip_address'] ?? '');
$mac_address = trim($_POST['mac_address'] ?? '');
$logged_in_user = trim($_POST['logged_in_user'] ?? '');
$computer_name = trim($_POST['computer_name'] ?? '');

if ($token === '') {
    echo json_encode(['success' => false, 'message' => 'Missing token']);
    exit;
}

$stmt = $conn->prepare("
    UPDATE computers
    SET computer_name = COALESCE(NULLIF(?, ''), computer_name),
        ip_address = ?,
        mac_address = ?,
        logged_in_user = ?,
        status = 'online',
        last_seen = NOW()
    WHERE agent_token = ?
");
$stmt->bind_param("sssss", $computer_name, $ip_address, $mac_address, $logged_in_user, $token);
$stmt->execute();

if ($stmt->affected_rows > 0 || $stmt->errno === 0) {
    echo json_encode(['success' => true, 'message' => 'Heartbeat updated']);
} else {
    echo json_encode(['success' => false, 'message' => 'Computer not found']);
}