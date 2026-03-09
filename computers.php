<?php
require_once "config/db.php";
require_once "includes/auth.php";
require_login();

$conn->query("UPDATE computers SET status='offline' WHERE last_seen IS NULL OR last_seen < (NOW() - INTERVAL 30 SECOND)");

$pageTitle = "Computers";
require_once "includes/header.php";
require_once "includes/sidebar.php";

$computers = $conn->query("
    SELECT computers.*, labs.lab_name
    FROM computers
    LEFT JOIN labs ON computers.lab_id = labs.id
    ORDER BY computers.id DESC
");
?>

<div class="main-content">
    <div class="topbar d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Computers</h4>
        <div class="d-flex gap-2">
            <a href="command_all.php?action=shutdown" class="btn btn-danger" onclick="return confirm('Shutdown all computers?')">Shutdown All</a>
            <a href="add_computer.php" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Add Computer</a>
        </div>
    </div>

    <div class="content-area">
        <div class="card table-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Computer</th>
                                <th>Lab</th>
                                <th>Token</th>
                                <th>IP</th>
                                <th>MAC</th>
                                <th>User</th>
                                <th>Status</th>
                                <th>Last Seen</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($computers && $computers->num_rows > 0): ?>
                            <?php while ($row = $computers->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['computer_name']) ?></td>
                                    <td><?= htmlspecialchars($row['lab_name'] ?? '-') ?></td>
                                    <td class="token-box"><?= htmlspecialchars($row['agent_token'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['ip_address'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['mac_address'] ?? '-') ?></td>
                                    <td><?= htmlspecialchars($row['logged_in_user'] ?? '-') ?></td>
                                    <td>
                                        <?php if ($row['status'] === 'online'): ?>
    <span class="badge bg-success text-white">Online</span>
<?php else: ?>
    <span class="badge bg-danger text-white">Offline</span>
<?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['last_seen'] ?? '-') ?></td>
                                    <td class="d-flex flex-column gap-1">
                                        <a href="command_pc.php?id=<?= $row['id'] ?>&action=shutdown"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Shutdown this computer?')">Shutdown</a>
                                        <a href="command_pc.php?id=<?= $row['id'] ?>&action=restart"
                                           class="btn btn-sm btn-warning"
                                           onclick="return confirm('Restart this computer?')">Restart</a>
                                        <a href="command_pc.php?id=<?= $row['id'] ?>&action=logoff"
                                           class="btn btn-sm btn-secondary"
                                           onclick="return confirm('Log off this computer?')">Logoff</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="10" class="text-center text-muted">No computers found.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 text-muted small">
                    Install the Python agent on each PC and set the matching token there.
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>