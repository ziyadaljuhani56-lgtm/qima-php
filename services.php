<?php
require_once __DIR__ . '/config.php';
$db = getDB();

$currentPage = 'services';
$pageTitle = 'خدمات المنصة | قيمة';
$pageDesc = 'حلول متخصصة في سلاسل الإمداد، النقل اللوجستي، تقارير ESG، والتحليلات السوقية للموارد المستدامة.';

$services = $db->query("SELECT * FROM services ORDER BY id ASC")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-banner">
    <div class="rail">
        <div class="breadcrumbs">
            <a href="<?= BASE_URL ?>/">الرئيسية</a> &gt; <span>خدمات المنصة</span>
        </div>
        <h1>خدمات منصة قيمة</h1>
        <p>نقدم لشركائنا منشآت ومصانع حلولاً عملية متكاملة تعزز كفاءة استغلال الموارد وتلبي متطلبات التحول نحو الاستدامة.</p>
    </div>
</div>

<div class="page-body rail">
    <div style="display:grid; gap:40px;">
        <?php foreach ($services as $srv): 
            $items = json_decode($srv['items'] ?? '[]', true);
        ?>
            <div style="background:white; border:1px solid var(--line); border-radius:8px; padding:32px; display:grid; grid-template-columns:1.5fr 1fr; gap:35px; align-items:center;">
                <div>
                    <span class="eyebrow"><?= e($srv['short_desc']) ?></span>
                    <h2 style="margin-bottom:14px;"><?= e($srv['title']) ?></h2>
                    <p style="font-size:14px; line-height:2; color:var(--gray); margin-bottom:20px;">
                        <?= e($srv['description']) ?>
                    </p>
                    <a href="<?= BASE_URL ?>/contact.php?subject=<?= urlencode('استفسار عن خدمة: ' . $srv['title']) ?>" class="button button-dark">
                        طلب الخدمة أو التنسيق
                    </a>
                </div>
                <div style="background:var(--soft); padding:24px; border-radius:6px; border:1px solid var(--line);">
                    <h4 style="font-size:15px; margin-bottom:14px; font-weight:600;">نطاق ومزايا الخدمة:</h4>
                    <ul style="list-style:none; padding:0; display:grid; gap:10px; font-size:13px; color:var(--ink);">
                        <?php foreach ($items as $item): ?>
                            <li style="display:flex; align-items:center; gap:8px;">
                                <span style="color:#6d9448;">✔</span> <?= e($item) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
