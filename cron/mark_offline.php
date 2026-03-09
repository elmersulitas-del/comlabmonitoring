<?php
require_once "../config/db.php";

$conn->query("UPDATE computers SET status='offline' WHERE last_seen IS NULL OR last_seen < (NOW() - INTERVAL 30 SECOND)");
echo "Offline status updated.";