<?php
require_once __DIR__ . '/../../config.php';

// التحقق الإداري الصارم: يسمح فقط للـ owner و admin
if (!isAdmin()) {
    header('Location: ' . BASE_URL . '/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$user = currentUser();
$db = getDB();

// إحصائيات سريعة للشارات في السايدبار
$pendingInquiries = (int)$db->query("SELECT COUNT(*) FROM inquiries WHERE status = 'new'")->fetchColumn();
$newOrdersCount = (int)$db->query("SELECT COUNT(*) FROM orders WHERE order_status = 'new'")->fetchColumn();

$currentAdminPage = $currentAdminPage ?? 'overview';
$adminTitle = $adminTitle ?? 'لوحة الإدارة المتكاملة';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($adminTitle) ?> | نظام إدارة قيمة</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/admin/assets/css/admin.css">
    <?php if (file_exists(__DIR__ . '/../assets/css/admin.css')): ?>
    <style>
        <?= file_get_contents(__DIR__ . '/../assets/css/admin.css'); ?>
    </style>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body>
    <!-- القائمة الجانبية (Sidebar) الفاخرة -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#c2ec89" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M7 19H4.815a1.83 1.83 0 0 1-1.57-.881 1.785 1.785 0 0 1-.004-1.784L7.196 9.5"/>
                <path d="M11 19h8.2a1.8 1.8 0 0 0 1.5-1 1.8 1.8 0 0 0 0-2l-4.7-8.2"/>
                <path d="M11 5h2"/>
                <path d="m7 19-3-3 3-3"/>
                <path d="m11 19 3 3 3-3"/>
                <path d="m13 5 3 3-3 3"/>
            </svg>
            <div>
                <strong>قيمة <span style="font-size:16px; font-weight:400; color:var(--admin-primary);">| لوحة الإدارة</span></strong>
                <small>منصة الاقتصاد الدائري وإدارة الموارد</small>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-title">الرئيسية والتحليلات</div>
            <a href="<?= BASE_URL ?>/admin/" class="sidebar-link <?= $currentAdminPage === 'overview' ? 'active' : '' ?>">
                <span>📊 لوحة الإحصائيات</span>
            </a>

            <div class="nav-section-title">التجارة وسوق الموارد</div>
            <a href="<?= BASE_URL ?>/admin/orders.php" class="sidebar-link <?= $currentAdminPage === 'orders' ? 'active' : '' ?>">
                <span>📦 إدارة الطلبات والمبيعات</span>
                <?php if ($newOrdersCount > 0): ?>
                    <span class="badge-count"><?= $newOrdersCount ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= BASE_URL ?>/admin/products.php" class="sidebar-link <?= $currentAdminPage === 'products' ? 'active' : '' ?>">
                <span>🏷️ المنتجات وسوق الموارد</span>
            </a>
            <a href="<?= BASE_URL ?>/admin/inquiries.php" class="sidebar-link <?= $currentAdminPage === 'inquiries' ? 'active' : '' ?>">
                <span>💬 الاستفسارات وتوريد الموارد</span>
                <?php if ($pendingInquiries > 0): ?>
                    <span class="badge-count"><?= $pendingInquiries ?></span>
                <?php endif; ?>
            </a>

            <div class="nav-section-title">المستخدمون والشركاء</div>
            <a href="<?= BASE_URL ?>/admin/customers.php" class="sidebar-link <?= $currentAdminPage === 'customers' ? 'active' : '' ?>">
                <span>👥 العملاء والمستخدمين</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="user-avatar"><?= mb_substr($user['name'], 0, 1) ?></div>
                <div class="user-info">
                    <strong><?= e($user['name']) ?></strong>
                    <small><?= e($user['role'] === 'owner' ? 'المالك الأعلى (Owner)' : 'مشرف النظام (Admin)') ?></small>
                </div>
            </div>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:6px;">
                <a href="<?= BASE_URL ?>/" target="_blank" class="btn btn-outline btn-sm" style="font-size:11px;">
                    👁️ عرض المتجر
                </a>
                <a href="<?= BASE_URL ?>/logout.php" class="btn btn-danger btn-sm" style="font-size:11px;">
                    خروج
                </a>
            </div>
        </div>
    </aside>

    <!-- المحتوى الرئيسي -->
    <div class="admin-main">
        <header class="admin-topbar">
            <div class="topbar-title">
                <button type="button" class="btn btn-outline btn-sm" id="toggleSidebarBtn" style="display:none;">☰</button>
                <h2><?= e($adminTitle) ?></h2>
            </div>
            <div class="topbar-actions">
                <span class="status-pill <?= e($user['role']) ?>">
                    ● الصلاحية: <?= e($user['role'] === 'owner' ? 'المالك' : 'مدير النظام') ?>
                </span>
                <a href="<?= BASE_URL ?>/" target="_blank" class="btn btn-outline btn-sm">
                    المتجر المباشر &larr;
                </a>
            </div>
        </header>

        <main class="admin-body">
