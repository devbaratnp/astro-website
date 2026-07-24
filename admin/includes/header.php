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

$navMain = [
    ['page' => 'dashboard.php',    'label' => 'Dashboard',      'icon' => 'grid'],
    ['page' => 'appointments.php', 'label' => 'Appointments · परामर्श', 'icon' => 'calendar'],
    ['page' => 'pooja-orders.php', 'label' => 'Pooja Orders',    'icon' => 'flame'],
    ['page' => 'payments.php',     'label' => 'Payments',       'icon' => 'card'],
    ['page' => 'pooja-services.php','label' => 'Pooja Services', 'icon' => 'star'],
    ['page' => 'products.php',     'label' => 'Products',       'icon' => 'package'],
];

$navContent = [
    ['page' => 'articles.php',    'label' => 'Articles · लेखहरू', 'icon' => 'file'],
    ['page' => 'testimonials.php','label' => 'Testimonials',    'icon' => 'star'],
    ['page' => 'gallery.php',     'label' => 'Gallery',         'icon' => 'image'],
    ['page' => 'services.php',    'label' => 'Services · सेवाहरू', 'icon' => 'star'],
    ['page' => 'panchang.php',    'label' => 'Panchang · पञ्चाङ्ग', 'icon' => 'sun'],
];

$navSystem = [
    ['page' => 'messages.php',    'label' => 'Messages',       'icon' => 'mail'],
    ['page' => 'users.php',       'label' => 'Admin Users',    'icon' => 'settings'],
    ['page' => 'settings.php',    'label' => 'Settings',       'icon' => 'settings'],
];

$titles = [
    'dashboard.php' => 'Dashboard',
    'appointments.php' => 'Appointments',
    'pooja-orders.php' => 'Pooja Orders',
    'payments.php' => 'Payments',
    'products.php' => 'Products',
    'pooja-services.php' => 'Pooja Services',
    'articles.php' => 'Articles',
    'testimonials.php' => 'Testimonials',
    'gallery.php' => 'Gallery',
    'services.php' => 'Services',
    'users.php' => 'Admin Users',
    'messages.php' => 'Messages',
    'panchang.php' => 'Panchang',
    'settings.php' => 'Settings',
];

$icons = [
    'grid' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>',
    'calendar' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4m8-4v4M3 10h18"/></svg>',
    'flame' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 22c4 0 7-3 7-7 0-5-4-7-3-12-4 2-8 6-8 11-1-1-2-2-2-4-2 2-2 4-2 6 0 4 3 6 8 6Z"/><path d="M10 19c-1-2 1-4 3-6 0 3 2 4 1 6"/></svg>',
    'card' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20M6 15h4"/></svg>',
    'star' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m12 2 3 6 7 .9-5 4.8 1.2 6.8L12 17.3l-6.2 3.2L7 13.7 2 8.9 9 8l3-6Z"/></svg>',
    'package' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16.5 9.4 7.5 4.2M21 16V8a2 2 0 0 0-1-1.7l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.7l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><path d="M3.27 6.96 12 12.01l8.73-5.05M12 22.08V12"/></svg>',
    'file' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2h8l4 4v16H6z"/><path d="M14 2v5h5M9 12h6M9 16h6"/></svg>',
    'image' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>',
    'sun' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M2 12h2m16 0h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>',
    'mail' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>',
    'settings' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>',
    'x' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 6 12 12M18 6 6 18"/></svg>',
    'menu' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M4 12h16M4 17h16"/></svg>',
    'external' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 4h6v6M20 4 10 14"/><path d="M18 13v6a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h6"/></svg>',
    'logout' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M10 17l5-5-5-5M15 12H3M15 4h4a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-4"/></svg>',
];

function renderNav($items, $currentPage, $icons) {
    $html = '';
    foreach ($items as $item) {
        $active = ($currentPage === $item['page']) ? ' active' : '';
        $html .= '<a href="' . BASE_URL . '/admin/' . $item['page'] . '" class="sidebar-link' . $active . '">';
        $html .= '<span class="sidebar-link-icon">' . ($icons[$item['icon']] ?? '') . '</span>';
        $html .= '<span class="sidebar-link-label">' . htmlspecialchars($item['label']) . '</span>';
        $html .= '</a>';
    }
    return $html;
}
?>
<!DOCTYPE html>
<html lang="ne">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($titles[$page] ?? 'Admin') ?> — श्रीहरि ज्योतिष</title>
    <link rel="icon" href="<?= BASE_URL ?>/assets/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;600;700;800&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/admin.css">
</head>
<body>

<aside class="sidebar" id="sidebar" aria-label="Main navigation">
    <div class="sidebar-head">
        <a href="<?= BASE_URL ?>/admin/dashboard.php" class="sidebar-brand">
            <span class="sidebar-logo" aria-hidden="true">ॐ</span>
            <span class="sidebar-brand-text">
                <strong class="sidebar-brand-name">Shreehari Admin</strong>
                <small class="sidebar-brand-sub">Management System</small>
            </span>
        </a>
        <button class="sidebar-close" id="sidebarClose" type="button" aria-label="Close navigation"><?= $icons['x'] ?></button>
    </div>
    <div class="sidebar-scroll">
        <nav class="sidebar-nav" aria-label="Main">
            <div class="sidebar-section">Main Navigation</div>
            <?= renderNav($navMain, $page, $icons) ?>
        </nav>
        <nav class="sidebar-nav" aria-label="Content">
            <div class="sidebar-section">Content Management</div>
            <?= renderNav($navContent, $page, $icons) ?>
        </nav>
        <nav class="sidebar-nav" aria-label="System">
            <div class="sidebar-section">System</div>
            <?= renderNav($navSystem, $page, $icons) ?>
        </nav>
    </div>
    <div class="sidebar-footer">
        <div class="sidebar-admin-info">
            <span class="sidebar-avatar"><?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) . strtoupper(substr($_SESSION['admin_name'] ?? 'D', 1, 1)) ?></span>
            <span class="sidebar-admin-text">
                <strong><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></strong>
                <small><?= htmlspecialchars($_SESSION['admin_role'] ?? 'administrator') ?></small>
            </span>
        </div>
        <a href="<?= BASE_URL ?>" target="_blank" rel="noreferrer" class="sidebar-link">
            <span class="sidebar-link-icon"><?= $icons['external'] ?></span>
            <span class="sidebar-link-label">View Website</span>
        </a>
        <a href="<?= BASE_URL ?>/admin/index.php?logout=1" class="sidebar-link logout-link">
            <span class="sidebar-link-icon"><?= $icons['logout'] ?></span>
            <span class="sidebar-link-label">Logout · बाहिरिनुहोस्</span>
        </a>
    </div>
</aside>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="admin-wrapper">
    <header class="admin-header">
        <div class="header-left">
            <button class="header-toggle" id="sidebarToggle" type="button" aria-label="Open navigation" aria-expanded="false" aria-controls="sidebar"><?= $icons['menu'] ?></button>
            <div class="header-title">
                <h1><?= htmlspecialchars($titles[$page] ?? 'Admin') ?></h1>
                <div class="header-breadcrumb">Admin / <?= htmlspecialchars($titles[$page] ?? '') ?></div>
            </div>
        </div>
        <div class="header-right">
            <a href="<?= BASE_URL ?>" target="_blank" rel="noreferrer" class="header-btn">
                <?= $icons['external'] ?>
                <span class="btn-label">View Site</span>
            </a>
            <div class="header-profile">
                <span class="sidebar-avatar"><?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) . strtoupper(substr($_SESSION['admin_name'] ?? 'D', 1, 1)) ?></span>
                <div>
                    <strong><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></strong>
                    <small><?= htmlspecialchars($_SESSION['admin_role'] ?? 'admin') ?></small>
                </div>
            </div>
        </div>
    </header>

    <main class="admin-main">
