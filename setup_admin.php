<?php
require_once "config/db.php";

$full_name = "System Administrator";
$username = "admin";
$plain_password = "admin123";
$hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

$check = $conn->prepare("SELECT id FROM admins WHERE username = ? LIMIT 1");
$check->bind_param("s", $username);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo "Admin account already exists.";
    exit;
}

$stmt = $conn->prepare("INSERT INTO admins (full_name, username, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $full_name, $username, $hashed_password);

if ($stmt->execute()) {
    echo "Default admin created successfully.<br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br>";
    echo "Delete setup_admin.php after use.";
} else {
    echo "Error creating admin: " . $conn->error;
}
?>