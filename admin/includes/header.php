<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../../backend/includes/helpers.php';
if (!isset($_SESSION['admin_id'])) {
    header('Location: ' . BASE_URL . '/admin/index.php');
    exit;
}
validateCsrf();
$page = basename($_SERVER['PHP_SELF']);

$navItems = [
    ['page' => 'dashboard.php',    'icon' => '◈', 'label' => 'ड्यासबोर्ड'],
    ['section' => true, 'label' => 'बुकिङ'],
    ['page' => 'appointments.php','icon' => '☰', 'label' => 'परामर्श'],
    ['page' => 'pooja-orders.php','icon' => '⊛', 'label' => 'पूजा अर्डर'],
    ['section' => true, 'label' => 'सामग्री'],
    ['page' => 'pooja-services.php','icon' => '✦', 'label' => 'पूजा सेवाहरू'],
    ['page' => 'articles.php',    'icon' => '✎', 'label' => 'लेखहरू'],
    ['page' => 'panchang.php',    'icon' => '◉', 'label' => 'पञ्चाङ्ग'],
    ['section' => true, 'label' => 'वित्त'],
    ['page' => 'payments.php',    'icon' => '₨', 'label' => 'भुक्तानी'],
    ['page' => 'rewards.php',     'icon' => '★', 'label' => 'पुरस्कार'],
    ['section' => true, 'label' => 'अन्य'],
    ['page' => 'messages.php',    'icon' => '✉', 'label' => 'सन्देशहरू'],
    ['page' => 'settings.php',    'icon' => '⊙', 'label' => 'सेटिङ्स'],
];

$titles = [
    'dashboard.php' => 'ड्यासबोर्ड',
    'appointments.php' => 'परामर्श व्यवस्थापन',
    'pooja-orders.php' => 'पूजा अर्डर',
    'pooja-services.php' => 'पूजा सेवाहरू',
    'articles.php' => 'लेख व्यवस्थापन',
    'panchang.php' => 'पञ्चाङ्ग',
    'payments.php' => 'भुक्तानी प्रमाणिकरण',
    'rewards.php' => 'पुरस्कार व्यवस्थापन',
    'messages.php' => 'सन्देशहरू',
    'settings.php' => 'सेटिङ्स',
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

<aside class="admin-sidebar" id="sidebar">
    <div class="sidebar-brand">
        <a href="<?= BASE_URL ?>/admin/dashboard.php" style="display:flex;align-items:center;gap:14px;color:inherit">
            <div class="sidebar-logo">ॐ</div>
            <div class="sidebar-brand-text">
                <strong>श्रीहरि ज्योतिष</strong>
                <small>प्रशासन प्रणाली</small>
            </div>
        </a>
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
        <a href="https://www.astroshreehari.com" target="_blank" rel="noreferrer">
            <span class="sidebar-icon">↗</span>
            साइट हेर्नुहोस्
        </a>
        <a href="<?= BASE_URL ?>/admin/index.php?logout=1" style="color:#f0a098">
            <span class="sidebar-icon">⊘</span>
            बाहिरिनुहोस्
        </a>
    </div>
</aside>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="admin-wrapper">
    <header class="admin-topbar">
        <div class="admin-topbar-left">
            <button class="sidebar-toggle" id="sidebarToggle">☰</button>
            <h2><?= $titles[$page] ?? 'प्रशासक' ?></h2>
        </div>
        <div class="admin-topbar-right">
            <span class="topbar-date"><?= date('Y-m-d') ?></span>
            <a href="<?= BASE_URL ?>/admin/index.php?logout=1" class="topbar-logout" title="बाहिरिनुहोस्">⊘</a>
        </div>
    </header>

    <main class="admin-main">
