<?php
require_once "config/db.php";
require_once "includes/auth.php";
require_login();

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $lab_name = trim($_POST['lab_name'] ?? '');
    $location = trim($_POST['location'] ?? '');

    if ($lab_name === '') {
        $error = "Lab name is required.";
    } else {
        $stmt = $conn->prepare("INSERT INTO labs (lab_name, location) VALUES (?, ?)");
        $stmt->bind_param("ss", $lab_name, $location);

        if ($stmt->execute()) {
            $success = "Lab added successfully.";
        } else {
            $error = "Failed to add lab.";
        }
    }
}

$pageTitle = "Add Lab";
require_once "includes/header.php";
require_once "includes/sidebar.php";
?>

<div class="main-content">
    <div class="topbar"><h4 class="mb-0">Add Laboratory</h4></div>
    <div class="content-area">
        <div class="card table-card">
            <div class="card-body">
                <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
                <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Lab Name</label>
                        <input type="text" name="lab_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary">Save Lab</button>
                    <a href="labs.php" class="btn btn-secondary">Back</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once "includes/footer.php"; ?>