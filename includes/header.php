<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config.php';
}
$pageTitle = $pageTitle ?? 'قيمة | منصة تداول الموارد المستدامة والاقتصاد الدائري';
$pageDesc = $pageDesc ?? 'قيمة تربط الموارد بالفرص، وتحوّل الاستدامة إلى قيمة مشتركة للأعمال والبيئة في المملكة العربية السعودية.';
$currentPage = $currentPage ?? '';
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($pageDesc) ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23173c2d' stroke-width='2'><path d='M7 19H4.815a1.83 1.83 0 0 1-1.57-.881 1.785 1.785 0 0 1-.004-1.784L7.196 9.5'/><path d='M11 19h8.2a1.8 1.8 0 0 0 1.5-1 1.8 1.8 0 0 0 0-2l-4.7-8.2'/><path d='M11 5h2'/><path d='m7 19-3-3 3-3'/><path d='m11 19 3 3 3-3'/><path d='m13 5 3 3-3 3'/></svg>">
</head>
<body>
    <div class="topline">
        <div class="rail" style="display:flex; justify-content:space-between; align-items:center;">
            <span>المملكة العربية السعودية — تمكين الاقتصاد الدائري واستدامة الموارد</span>
            <span class="topline-end">كل مورد... له قيمة</span>
        </div>
    </div>

    <header class="site-header">
        <div class="header-inner rail">
            <a class="brand" href="<?= BASE_URL ?>/">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M7 19H4.815a1.83 1.83 0 0 1-1.57-.881 1.785 1.785 0 0 1-.004-1.784L7.196 9.5"/>
                    <path d="M11 19h8.2a1.8 1.8 0 0 0 1.5-1 1.8 1.8 0 0 0 0-2l-4.7-8.2"/>
                    <path d="M11 5h2"/>
                    <path d="m7 19-3-3 3-3"/>
                    <path d="m11 19 3 3 3-3"/>
                    <path d="m13 5 3 3-3 3"/>
                </svg>
                <div>
                    <strong>قيمة<span class="brand-dot">.</span></strong>
                    <small>منصة الموارد <span>QIMA</span></small>
                </div>
            </a>

            <nav class="main-nav">
                <a href="<?= BASE_URL ?>/" class="<?= $currentPage === 'home' ? 'active' : '' ?>">الرئيسية</a>
                <a href="<?= BASE_URL ?>/market.php" class="<?= $currentPage === 'market' ? 'active' : '' ?>">سوق الموارد</a>
                <a href="<?= BASE_URL ?>/services.php" class="<?= $currentPage === 'services' ? 'active' : '' ?>">الخدمات</a>
                <a href="<?= BASE_URL ?>/insights.php" class="<?= $currentPage === 'insights' ? 'active' : '' ?>">المؤشرات والأثر</a>
                <a href="<?= BASE_URL ?>/about.php" class="<?= $currentPage === 'about' ? 'active' : '' ?>">عن قيمة</a>
                <a href="<?= BASE_URL ?>/contact.php" class="<?= $currentPage === 'contact' ? 'active' : '' ?>">تواصل معنا</a>
            </nav>

            <div class="header-actions">
                <?php if ($user): ?>
                    <a href="<?= BASE_URL ?>/profile.php" class="header-account">مرحباً، <?= e($user['name']) ?></a>
                    <?php if (in_array($user['role'], ['admin', 'owner'])): ?>
                        <a href="<?= BASE_URL ?>/dashboard.php" class="button button-outline" style="padding:8px 14px; min-height:38px;">لوحة التحكم</a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/logout.php" class="text-link" style="color:#b91c1c;">خروج</a>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/login.php" class="header-account">دخول</a>
                    <a href="<?= BASE_URL ?>/signup.php" class="button button-dark header-cta" style="padding:9px 16px; min-height:40px;">تسجيل جديد</a>
                <?php endif; ?>

                <button type="button" class="menu-toggle" aria-label="القائمة">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/>
                    </svg>
                </button>
            </div>
        </div>
    </header>
