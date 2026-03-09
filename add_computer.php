<?php
require_once "config/db.php";
require_once "includes/auth.php";
require_login();

$error = "";
$success = "";

$labs = $conn->query("SELECT id, lab_name FROM labs ORDER BY lab_name ASC");

function generateAgentToken()
{
    return 'AGENT-' . strtoupper(bin2hex(random_bytes(8)));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $lab_id = (int)($_POST['lab_id'] ?? 0);
    $computer_name = trim($_POST['computer_name'] ?? '');
    $ip_address = trim($_POST['ip_address'] ?? '');
    $mac_address = trim($_POST['mac_address'] ?? '');

    if ($lab_id <= 0 || $computer_name === '') {
        $error = "Lab and computer name are required.";
    } else {
        $token = generateAgentToken();

        $stmt = $conn->prepare("
            INSERT INTO computers (lab_id, computer_name, agent_token, ip_address, mac_address, status)
            VALUES (?, ?, ?, ?, ?, 'offline')
        ");
        $stmt->bind_param("issss", $lab_id, $computer_name, $token, $ip_address, $mac_address);

        if ($stmt->execute()) {
            $success = "Computer added successfully. Token: " . $token;
        } else {
            $error = "Failed to add computer.";
        }
    }
}

$pageTitle = "Add Computer";
require_once "includes/header.php";
require_once "includes/sidebar.php";
?>

<div class="main-content">
    <div class="topbar"><h4 class="mb-0">Add Computer</h4></div>
    <div class="content-area">
        <div class="card table-card">
            <div class="card-body">
                <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Laboratory</label>
                        <select name="lab_id" class="form-select" required>
                            <option value="">Select Lab</option>
                            <?php while ($lab = $labs->fetch_assoc()): ?>
                                <option value="<?= $lab['id'] ?>"><?= htmlspecialchars($lab['lab_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Computer Name</label>
                        <input type="text" name="computer_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">IP Address</label>
                        <input type="text" name="ip_address" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">MAC Address</label>
                        <input type="text" name="mac_address" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary">Save Computer</button>
                    <a href="computers.php" class="btn btn-secondary">Back</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>