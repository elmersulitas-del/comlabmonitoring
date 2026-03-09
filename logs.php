<?php
require_once "config/db.php";
require_once "includes/auth.php";
require_login();

$pageTitle = "Logs";
require_once "includes/header.php";
require_once "includes/sidebar.php";

$logs = $conn->query("
    SELECT logs.*, admins.full_name AS admin_name, computers.computer_name
    FROM logs
    LEFT JOIN admins ON admins.id = logs.admin_id
    LEFT JOIN computers ON computers.id = logs.computer_id
    ORDER BY logs.id DESC
    LIMIT 200
");
?>

<div class="main-content">
    <div class="topbar">
        <h4 class="mb-0">Activity Logs</h4>
    </div>

    <div class="content-area">
        <div class="card table-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Admin</th>
                                <th>Computer</th>
                                <th>Action</th>
                                <th>Details</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($logs && $logs->num_rows > 0): ?>
                            <?php while ($row = $logs->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['admin_name'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['computer_name'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['action']) ?></td>
                                    <td><?= htmlspecialchars($row['details'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['created_at']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center text-muted">No logs found.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>