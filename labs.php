<?php
require_once "config/db.php";
require_once "includes/auth.php";
require_login();

$pageTitle = "Labs";
require_once "includes/header.php";
require_once "includes/sidebar.php";

$labs = $conn->query("
    SELECT labs.*,
           (SELECT COUNT(*) FROM computers WHERE computers.lab_id = labs.id) AS total_computers
    FROM labs
    ORDER BY labs.id DESC
");
?>

<div class="main-content">
    <div class="topbar d-flex justify-content-between align-items-center">
        <h4 class="mb-0">Laboratories</h4>
        <a href="add_lab.php" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Add Lab</a>
    </div>

    <div class="content-area">
        <div class="card table-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Lab Name</th>
                                <th>Location</th>
                                <th>Total Computers</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($labs && $labs->num_rows > 0): ?>
                            <?php while ($lab = $labs->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $lab['id'] ?></td>
                                    <td><?= htmlspecialchars($lab['lab_name']) ?></td>
                                    <td><?= htmlspecialchars($lab['location'] ?? '-') ?></td>
                                    <td><?= $lab['total_computers'] ?></td>
                                    <td>
                                        <a href="command_lab.php?lab_id=<?= $lab['id'] ?>&action=shutdown"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Shutdown all computers in this lab?')">
                                           Shutdown Lab
                                        </a>
                                        <a href="command_lab.php?lab_id=<?= $lab['id'] ?>&action=restart"
                                           class="btn btn-sm btn-warning"
                                           onclick="return confirm('Restart all computers in this lab?')">
                                           Restart Lab
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center text-muted">No labs found.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>