<?php
require_once __DIR__ . '/config.php';
$db = getDB();

$currentPage = 'market';
$pageTitle = 'سوق الموارد المستدامة | قيمة';
$pageDesc = 'تصفح عروض ومواد إعادة التدوير، البلاستيك، المعادن، الورق والإطارات بأسعار وتحديثات فورية.';

$category = $_GET['category'] ?? '';
$search = trim($_GET['search'] ?? '');

$materials = $db->query("SELECT * FROM materials ORDER BY id ASC")->fetchAll();

$query = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($category) {
    $query .= " AND category = ?";
    $params[] = $category;
}
if ($search) {
    $query .= " AND (title LIKE ? OR description LIKE ? OR location LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$query .= " ORDER BY id DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-banner">
    <div class="rail">
        <div class="breadcrumbs">
            <a href="<?= BASE_URL ?>/">الرئيسية</a> &gt; <span>سوق الموارد</span>
        </div>
        <h1>سوق الموارد المستدامة</h1>
        <p>تصفح عروض الموارد الصناعية والمواد القابلة للتدوير المعروضة من منشآت ومصانع موثقة بالمملكة.</p>
    </div>
</div>

<div class="page-body rail">
    <form class="filter-form" method="GET" action="<?= BASE_URL ?>/market.php">
        <div class="field" style="flex:1;">
            <label>البحث بالكلمة أو الموقع</label>
            <input type="text" name="search" placeholder="مثال: بلاستيك HDPE، كرتون، الرياض..." value="<?= e($search) ?>">
        </div>
        <div class="field">
            <label>تصنيف المادة</label>
            <select name="category">
                <option value="">جميع التصنيفات</option>
                <?php foreach ($materials as $m): ?>
                    <option value="<?= e($m['slug']) ?>" <?= $category === $m['slug'] ? 'selected' : '' ?>>
                        <?= e($m['title']) ?> (<?= e($m['english']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="button button-dark" style="align-self:end;">تصفية النتائج</button>
        <?php if ($category || $search): ?>
            <a href="<?= BASE_URL ?>/market.php" class="button button-outline" style="align-self:end;">إلغاء التصفية</a>
        <?php endif; ?>
    </form>

    <div class="market-layout">
        <aside class="market-sidebar">
            <h3>التصنيفات الرئيسية</h3>
            <a href="<?= BASE_URL ?>/market.php" class="category-link <?= empty($category) ? 'selected' : '' ?>">
                <span>جميع الموارد</span>
            </a>
            <?php foreach ($materials as $m): ?>
                <a href="<?= BASE_URL ?>/market.php?category=<?= urlencode($m['slug']) ?>" class="category-link <?= $category === $m['slug'] ? 'selected' : '' ?>">
                    <span><?= e($m['title']) ?></span>
                </a>
            <?php endforeach; ?>

            <p>
                هل ترغب في بيع أو توريد مواد صناعية أو مفروزة؟<br>
                <a href="<?= BASE_URL ?>/contact.php" class="text-link">قدّم طلب إدراج مورد &larr;</a>
            </p>
        </aside>

        <main>
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                <span style="font-size:12px; color:var(--gray);">النتائج المعروضة: <strong><?= count($products) ?></strong> مورد</span>
            </div>

            <?php if (empty($products)): ?>
                <div class="empty-state" style="padding:50px; text-align:center; background:var(--soft); border-radius:8px;">
                    <h3>لا توجد عروض تطابق هذا البحث حالياً</h3>
                    <p style="color:var(--gray); margin-top:8px;">يمكنك تقديم طلب خاص بالمواد التي تبحث عنها وسيقوم فريقنا بالتنسيق مع شبكة الموردين.</p>
                    <a href="<?= BASE_URL ?>/contact.php" class="button button-dark" style="margin-top:16px;">إرسال طلب مخصص</a>
                </div>
            <?php else: ?>
                <div class="product-grid" style="margin-top:0;">
                    <?php foreach ($products as $prod): ?>
                        <div class="product-card">
                            <img src="<?= e($prod['image_url']) ?>" alt="<?= e($prod['title']) ?>">
                            <div>
                                <span class="badge"><?= e($prod['category']) ?></span>
                                <h3 style="margin-top:10px;"><?= e($prod['title']) ?></h3>
                                <p class="price"><?= number_format($prod['price'], 2) ?> ر.س / <?= e($prod['unit']) ?></p>
                                <p style="font-size:12px; color:var(--gray); line-height:1.6; margin-bottom:14px;"><?= e(mb_strimwidth($prod['description'], 0, 95, '...')) ?></p>
                                <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--line); padding-top:10px;">
                                    <span style="font-size:11px; color:var(--gray);"><?= e($prod['location']) ?></span>
                                    <a href="<?= BASE_URL ?>/product.php?handle=<?= urlencode($prod['handle']) ?>" class="button button-outline" style="padding:6px 12px; min-height:34px; font-size:12px;">التفاصيل</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
