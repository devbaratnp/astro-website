<?php
session_start();
require_once __DIR__ . '/config.php';
if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . '/admin/index.php');
    exit;
}
$page = basename($_SERVER['PHP_SELF']);

$navItems = [
    ['page' => 'dashboard.php',   'icon' => '📊', 'label' => 'ड्यासबोर्ड'],
    ['page' => 'appointments.php','icon' => '📅', 'label' => 'परामर्श'],
    ['page' => 'pooja-orders.php','icon' => '🙏', 'label' => 'पूजा अर्डर'],
    ['page' => 'payments.php',    'icon' => '💳', 'label' => 'भुक्तानी'],
    ['page' => 'rewards.php',     'icon' => '🎁', 'label' => 'पुरस्कार'],
    ['section' => true, 'label' => 'सेटिङ्स'],
    ['page' => 'settings.php',    'icon' => '⚙️', 'label' => 'सेटिङ्स'],
];
?>
<!DOCTYPE html>
<html lang="ne">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>प्रशासक — श्रीहरि ज्योतिष</title>
    <link rel="icon" href="<?= BASE_URL ?>/assets/favicon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/admin.css">
</head>
<body>

<!-- Sidebar -->
<aside class="admin-sidebar" id="sidebar">
    <div class="sidebar-brand">
        <img src="<?= BASE_URL ?>/assets/logo.svg" alt="श्रीहरि">
        <div class="sidebar-brand-text">
            <strong>श्रीहरि ज्योतिष</strong>
            <small>प्रशासन प्रणाली</small>
        </div>
    </div>

    <nav class="sidebar-nav">
        <?php foreach ($navItems as $item): ?>
            <?php if (!empty($item['section'])): ?>
                <div class="sidebar-section"><?= $item['label'] ?></div>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/admin/<?= $item['page'] ?>" class="sidebar-link <?= $page === $item['page'] ? 'active' : '' ?>">
                    <span class="sidebar-icon"><?= $item['icon'] ?></span>
                    <?= $item['label'] ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-admin-info">
        <div class="admin-name"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'प्रशासक') ?></div>
        <div class="admin-role"><?= htmlspecialchars($_SESSION['admin_role'] ?? 'admin') ?></div>
    </div>

    <div class="sidebar-footer">
        <a href="<?= BASE_URL ?>/admin/index.php?logout=1">
            <span class="sidebar-icon">🚪</span>
            बाहिरिनुहोस्
        </a>
    </div>
</aside>

<!-- Overlay for mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Main wrapper -->
<div class="admin-wrapper">
    <header class="admin-topbar">
        <div class="admin-topbar-left">
            <button class="sidebar-toggle" id="sidebarToggle">☰</button>
            <h2><?php
                $titles = [
                    'dashboard.php' => 'ड्यासबोर्ड',
                    'appointments.php' => 'परामर्श व्यवस्थापन',
                    'pooja-orders.php' => 'पूजा अर्डर',
                    'payments.php' => 'भुक्तानी प्रमाणिकरण',
                    'rewards.php' => 'पुरस्कार व्यवस्थापन',
                    'settings.php' => 'सेटिङ्स',
                ];
                echo $titles[$page] ?? 'प्रशासक';
            ?></h2>
        </div>
        <div class="admin-breadcrumb">
            <a href="<?= BASE_URL ?>/admin/dashboard.php">प्रशासक</a>
            <span>/</span>
            <span><?= $titles[$page] ?? $page ?></span>
        </div>
    </header>

    <main class="admin-main">
