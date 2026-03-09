<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar bg-dark text-white p-3">
    <div class="text-center mb-4">
        <h4 class="fw-bold mb-1">Comlab</h4>
        <small class="text-light opacity-75">Monitoring System</small>
    </div>

    <ul class="nav flex-column gap-2">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link text-white <?= $currentPage == 'dashboard.php' ? 'active-link' : '' ?>">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="labs.php" class="nav-link text-white <?= in_array($currentPage, ['labs.php','add_lab.php']) ? 'active-link' : '' ?>">
                <i class="bi bi-building me-2"></i> Labs
            </a>
        </li>
        <li class="nav-item">
            <a href="computers.php" class="nav-link text-white <?= in_array($currentPage, ['computers.php','add_computer.php']) ? 'active-link' : '' ?>">
                <i class="bi bi-pc-display me-2"></i> Computers
            </a>
        </li>
        <li class="nav-item">
            <a href="logs.php" class="nav-link text-white <?= $currentPage == 'logs.php' ? 'active-link' : '' ?>">
                <i class="bi bi-clock-history me-2"></i> Logs
            </a>
        </li>
        <li class="nav-item mt-3">
            <a href="logout.php" class="nav-link text-white bg-danger rounded">
                <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
        </li>
    </ul>
</div>