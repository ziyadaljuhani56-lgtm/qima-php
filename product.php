<?php
require_once __DIR__ . '/config.php';
$db = getDB();

$handle = $_GET['handle'] ?? '';
$stmt = $db->prepare("SELECT * FROM products WHERE handle = ?");
$stmt->execute([$handle]);
$product = $stmt->fetch();

if (!$product) {
    header("HTTP/1.0 404 Not Found");
    $pageTitle = 'المورد غير موجود | قيمة';
    require_once __DIR__ . '/includes/header.php';
    echo '<div class="page-body rail" style="text-align:center; padding:80px 20px;"><h2>عذراً، المورد المطلوب غير موجود</h2><p style="margin:15px 0 25px; color:var(--gray);">قد يكون تم بيع هذا المورد أو نقل العرض.</p><a href="' . BASE_URL . '/market.php" class="button button-dark">العودة لسوق الموارد</a></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = e($product['title']) . ' | قيمة';
$pageDesc = e(mb_strimwidth($product['description'], 0, 150, '...'));
$currentPage = 'market';

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-banner">
    <div class="rail">
        <div class="breadcrumbs">
            <a href="<?= BASE_URL ?>/">الرئيسية</a> &gt; <a href="<?= BASE_URL ?>/market.php">سوق الموارد</a> &gt; <span><?= e($product['title']) ?></span>
        </div>
        <h1><?= e($product['title']) ?></h1>
    </div>
</div>

<div class="page-body rail">
    <div class="detail-layout">
        <div>
            <img src="<?= e($product['image_url']) ?>" alt="<?= e($product['title']) ?>" class="detail-photo">
        </div>
        <div>
            <span class="badge" style="font-size:13px;"><?= e($product['category']) ?></span>
            <div class="detail-price">
                <?= number_format($product['price'], 2) ?> ر.س <small style="font-size:14px; color:var(--gray);">/ <?= e($product['unit']) ?></small>
            </div>

            <div style="background:var(--soft); padding:18px; border-radius:8px; border:1px solid var(--line); margin-bottom:24px;">
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; font-size:13px;">
                    <div><strong>الموقع:</strong> <?= e($product['location']) ?></div>
                    <div><strong>الكمية المتاحة:</strong> <?= e($product['quantity']) ?></div>
                    <div><strong>طريقة التوريد:</strong> شحن لوجستي منسق أو استلام موقع</div>
                    <div><strong>حالة الفحص:</strong> تم التحقق من المواصفات الفنية</div>
                </div>
            </div>

            <h3 style="font-size:18px; margin-bottom:10px;">وصف المورد والمواصفات:</h3>
            <p style="font-size:14px; line-height:2; color:var(--gray); margin-bottom:24px;">
                <?= nl2br(e($product['description'])) ?>
            </p>

            <div style="display:flex; gap:14px; flex-wrap:wrap;">
                <a href="<?= BASE_URL ?>/contact.php?subject=<?= urlencode('طلب حجز وتوريد: ' . $product['title']) ?>" class="button button-dark">
                    طلب حجز أو استفسار عن هذا المورد
                </a>
                <a href="<?= BASE_URL ?>/market.php" class="button button-outline">
                    تصفح المزيد في السوق
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
