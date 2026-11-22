<?php
/**
 * includes/header.php
 * Global HTML <head> and navbar
 */
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$loggedIn    = isLoggedIn();
$user        = $loggedIn ? currentUser() : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' : '' ?>HousePrice AI</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/house-price-prediction/assets/css/style.css">
</head>
<body>

<!-- ── Top Navbar ─────────────────────────────────────────────── -->
<nav class="navbar navbar-expand-lg navbar-dark hp-navbar fixed-top shadow-sm">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand d-flex align-items-center gap-2 fw-800" href="/house-price-prediction/index.php">
            <div class="brand-icon">
                <i class="bi bi-houses-fill"></i>
            </div>
            <span>HousePrice <span class="text-accent">AI</span></span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto align-items-center gap-1">
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'index' ? 'active' : '' ?>" href="/house-price-prediction/index.php">
                        <i class="bi bi-house-door me-1"></i>Home
                    </a>
                </li>
                <?php if ($loggedIn): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="/house-price-prediction/dashboard.php">
                        <i class="bi bi-grid-1x2 me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'predict' ? 'active' : '' ?>" href="/house-price-prediction/predict.php">
                        <i class="bi bi-magic me-1"></i>Predict
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'history' ? 'active' : '' ?>" href="/house-price-prediction/history.php">
                        <i class="bi bi-clock-history me-1"></i>History
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link <?= $currentPage === 'about' ? 'active' : '' ?>" href="/house-price-prediction/about.php">
                        <i class="bi bi-info-circle me-1"></i>About
                    </a>
                </li>

                <?php if ($loggedIn): ?>
                <li class="nav-item dropdown ms-2">
                    <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown">
                        <div class="user-avatar-sm">
                            <?= strtoupper(substr($user['name'], 0, 1)) ?>
                        </div>
                        <span class="d-none d-lg-inline"><?= htmlspecialchars($user['name']) ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><span class="dropdown-item-text small text-muted"><?= htmlspecialchars($user['email']) ?></span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/house-price-prediction/dashboard.php"><i class="bi bi-grid me-2"></i>Dashboard</a></li>
                        <li><a class="dropdown-item" href="/house-price-prediction/history.php"><i class="bi bi-clock-history me-2"></i>My Predictions</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="/house-price-prediction/logout.php">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </li>
                <?php else: ?>
                <li class="nav-item ms-2">
                    <a class="btn btn-outline-light btn-sm px-3" href="/house-price-prediction/login.php">
                        <i class="bi bi-person me-1"></i>Login
                    </a>
                </li>
                <li class="nav-item ms-1">
                    <a class="btn btn-accent btn-sm px-3" href="/house-price-prediction/register.php">
                        <i class="bi bi-person-plus me-1"></i>Sign Up
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
