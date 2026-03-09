<?php
require_once "config/db.php";
require_once "includes/auth.php";
require_login();

$conn->query("UPDATE computers SET status='offline' WHERE last_seen IS NULL OR last_seen < (NOW() - INTERVAL 30 SECOND)");

$pageTitle = "Dashboard";
require_once "includes/header.php";
require_once "includes/sidebar.php";

$totalLabs = (int)($conn->query("SELECT COUNT(*) AS total FROM labs")->fetch_assoc()['total'] ?? 0);
$totalComputers = (int)($conn->query("SELECT COUNT(*) AS total FROM computers")->fetch_assoc()['total'] ?? 0);
$totalOnline = (int)($conn->query("SELECT COUNT(*) AS total FROM computers WHERE status='online'")->fetch_assoc()['total'] ?? 0);
$totalOffline = (int)($conn->query("SELECT COUNT(*) AS total FROM computers WHERE status='offline'")->fetch_assoc()['total'] ?? 0);
$totalPending = (int)($conn->query("SELECT COUNT(*) AS total FROM commands WHERE status='pending'")->fetch_assoc()['total'] ?? 0);

$latestComputers = $conn->query("
    SELECT computers.*, labs.lab_name
    FROM computers
    LEFT JOIN labs ON labs.id = computers.lab_id
    ORDER BY computers.id DESC
    LIMIT 10
");

$latestCommands = $conn->query("
    SELECT commands.*, computers.computer_name
    FROM commands
    LEFT JOIN computers ON computers.id = commands.computer_id
    ORDER BY commands.id DESC
    LIMIT 8
");
?>

<div class="main-content">
    <div class="topbar d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-0">Dashboard</h4>
            <small class="text-muted">Welcome, <?= htmlspecialchars($_SESSION['admin_name']) ?></small>
        </div>
        <a href="command_all.php?action=shutdown" class="btn btn-danger"
           onclick="return confirm('Shutdown ALL computers?')">
            <i class="bi bi-power me-1"></i> Shutdown All
        </a>
    </div>

    <div class="content-area">
        <div class="row g-4 mb-4">
            <div class="col-md-6 col-xl-2">
                <div class="card card-stat"><div class="card-body"><h6 class="text-muted">Labs</h6><h2><?= $totalLabs ?></h2></div></div>
            </div>
            <div class="col-md-6 col-xl-2">
                <div class="card card-stat"><div class="card-body"><h6 class="text-muted">Computers</h6><h2><?= $totalComputers ?></h2></div></div>
            </div>
            <div class="col-md-6 col-xl-2">
                <div class="card card-stat"><div class="card-body"><h6 class="text-muted">Online</h6><h2 class="text-success"><?= $totalOnline ?></h2></div></div>
            </div>
            <div class="col-md-6 col-xl-2">
                <div class="card card-stat"><div class="card-body"><h6 class="text-muted">Offline</h6><h2 class="text-danger"><?= $totalOffline ?></h2></div></div>
            </div>
            <div class="col-md-6 col-xl-2">
                <div class="card card-stat"><div class="card-body"><h6 class="text-muted">Pending Cmd</h6><h2 class="text-warning"><?= $totalPending ?></h2></div></div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card table-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Latest Computers</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Lab</th>
                                        <th>IP</th>
                                        <th>Status</th>
                                        <th>Last Seen</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if ($latestComputers && $latestComputers->num_rows > 0): ?>
                                    <?php while ($row = $latestComputers->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['computer_name']) ?></td>
                                            <td><?= htmlspecialchars($row['lab_name'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($row['ip_address'] ?? '-') ?></td>
                                            <td>
                                                <?php if ($row['status'] === 'online'): ?>
                                                    <span class="badge badge-online">Online</span>
                                                <?php else: ?>
                                                    <span class="badge badge-offline">Offline</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($row['last_seen'] ?? '-') ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="5" class="text-center text-muted">No records found.</td></tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card table-card">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Latest Commands</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>PC</th>
                                        <th>Command</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if ($latestCommands && $latestCommands->num_rows > 0): ?>
                                    <?php while ($cmd = $latestCommands->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($cmd['computer_name'] ?? '-') ?></td>
                                            <td><?= htmlspecialchars($cmd['command_type']) ?></td>
                                            <td>
                                                <span class="badge <?= $cmd['status'] === 'pending' ? 'badge-pending' : ($cmd['status'] === 'executed' ? 'badge-executed' : 'badge-offline') ?>">
                                                    <?= htmlspecialchars($cmd['status']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center text-muted">No commands yet.</td></tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>