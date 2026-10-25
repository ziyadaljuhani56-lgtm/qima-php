<?php
require_once __DIR__ . '/config.php';
$db = getDB();

$currentPage = 'home';
$pageTitle = 'منصة قيمة | سوق الموارد المستدامة والاقتصاد الدائري';

// جلب تصنيفات الموارد من قاعدة البيانات
$materials = $db->query("SELECT * FROM materials ORDER BY id ASC")->fetchAll();
// جلب عينات من منتجات السوق
$products = $db->query("SELECT * FROM products ORDER BY id DESC LIMIT 3")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <img src="https://images.hostinger.com/05440944-e936-47b5-8754-a4b0142d3228/ec73c3db-6d41-4133-b5f4-2af110f99566.png" class="hero-photo" alt="قيمة - منصة تداول الموارد">
    <div class="hero-shade"></div>
    <div class="hero-content rail">
        <span class="hero-eyebrow"><span></span> منصة متخصصة في تداول الموارد المستدامة بالمملكة</span>
        <h1>نربط الموارد بالفرص،<br>ونصنع <span>قيمة</span> مستدامة.</h1>
        <div class="hero-copy">
            <p>سوق موثوق يربط موردي الموارد القابلة لإعادة التدوير بالمصانع والجهات المستفيدة، لرفع كفاءة سلاسل الإمداد وتمكين الاقتصاد الدائري.</p>
        </div>
        <div class="hero-buttons">
            <a href="<?= BASE_URL ?>/market.php" class="button button-lime">تصفح سوق الموارد</a>
            <a href="<?= BASE_URL ?>/contact.php" class="hero-more">اعرض مواردك معنا &larr;</a>
        </div>
        <div class="hero-assurances">
            <span>✓ توثيق بيانات الموردين والمشترين</span>
            <span>✓ حلول لوجستية ومتابعة سلاسل الإمداد</span>
            <span>✓ متوافق مع مستهدفات الاستدامة 2030</span>
        </div>

        <div class="resource-note">
            <div class="resource-note-icon">♻</div>
            <div>
                <strong>اقتصاد دائري متكامل</strong>
                <small>تمكين المصانع والمنشآت من استعادة القيمة</small>
            </div>
        </div>
    </div>
</section>

<!-- قسم تصنيفات الموارد -->
<section class="materials-section rail">
    <div class="section-heading">
        <div>
            <span class="eyebrow">قطاعات الموارد المتاحة</span>
            <h2>المواد الأكثر طلباً في السوق</h2>
        </div>
        <a href="<?= BASE_URL ?>/market.php" class="text-link">عرض جميع الموارد &larr;</a>
    </div>

    <div class="material-grid">
        <?php foreach ($materials as $index => $mat): ?>
            <a href="<?= BASE_URL ?>/market.php?category=<?= urlencode($mat['slug']) ?>" class="material-card">
                <span class="material-index"><?= sprintf("%02d", $index + 1) ?></span>
                <div class="material-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                </div>
                <h3><?= e($mat['title']) ?></h3>
                <div>
                    <span><?= e($mat['english']) ?></span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- عروض مميزة من السوق -->
<section class="market-preview">
    <div class="rail">
        <div class="market-preview-bar">
            <span><span class="status-dot"></span> عروض وتوريدات نشطة في السوق</span>
            <span>تحديثات مستمرة لأسعار وفرص الموارد</span>
        </div>

        <div class="product-grid">
            <?php foreach ($products as $prod): ?>
                <div class="product-card">
                    <img src="<?= e($prod['image_url']) ?>" alt="<?= e($prod['title']) ?>">
                    <div>
                        <span class="badge"><?= e($prod['category']) ?></span>
                        <h3 style="margin-top:10px;"><?= e($prod['title']) ?></h3>
                        <p class="price"><?= number_format($prod['price'], 2) ?> ر.س / <?= e($prod['unit']) ?></p>
                        <p style="font-size:12px; color:var(--gray); margin-bottom:14px;"><?= e($prod['location']) ?> (<?= e($prod['quantity']) ?>)</p>
                        <a href="<?= BASE_URL ?>/product.php?handle=<?= urlencode($prod['handle']) ?>" class="button button-outline" style="width:100%;">تفاصيل المورد</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- قسم الحلول والخدمات -->
<section class="solutions-section">
    <div class="rail solutions-layout">
        <div class="solutions-intro">
            <span class="eyebrow">خدمات متكاملة</span>
            <h2>حلول عملية لدعم <span>سلاسل الإمداد</span> الدائرية.</h2>
            <p>نقدّم بنية خدمية متكاملة لربط منشأتك بالفرص السوقية، من التنسيق اللوجستي والنقل، وحتى إصدار تقارير الاستدامة المعتمدة.</p>
            <a href="<?= BASE_URL ?>/services.php" class="button button-lime">اكتشف جميع الخدمات</a>
        </div>
        <div>
            <div class="solution-row">
                <div class="solution-icon">🚚</div>
                <div>
                    <span class="small-label">اللوجستيات</span>
                    <h3>النقل والتنسيق اللوجستي</h3>
                    <p>ربط آمن وموثوق لحركة الموارد بين المنشآت والمصانع ومراكز الفرز.</p>
                </div>
            </div>
            <div class="solution-row">
                <div class="solution-icon">📊</div>
                <div>
                    <span class="small-label">التقارير</span>
                    <h3>تقارير الأثر والاستدامة (ESG)</h3>
                    <p>توثيق وتحليل كميات الموارد المعاد تدويرها ومعدلات خفض البصمة الكربونية.</p>
                </div>
            </div>
            <div class="solution-row">
                <div class="solution-icon">📈</div>
                <div>
                    <span class="small-label">البيانات</span>
                    <h3>التحليلات السوقية والتسعير</h3>
                    <p>مؤشرات تسعير استرشادية مبنية على حركة العرض والطلب وجودة المواد.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- مسار العمل (Process) -->
<section class="process-section rail">
    <div class="section-heading">
        <div>
            <span class="eyebrow">كيف تعمل منصة قيمة؟</span>
            <h2>خطوات بسيطة لتحويل الموارد إلى قيمة</h2>
        </div>
    </div>
    <div class="process-grid">
        <article>
            <span>01</span>
            <h3>سجّل وحدد مواردك</h3>
            <p>أنشئ حسابك واعرض الموارد المتوفرة لديك أو تصفح الموارد والكميات المطلوبة للمصانع.</p>
        </article>
        <article>
            <span>02</span>
            <h3>الاتفاق والتحقق</h3>
            <p>نساعدك في مطابقة المواصفات، المعاينة، وتحديد متطلبات التسعير والشحن المناسبة.</p>
        </article>
        <article>
            <span>03</span>
            <h3>التنفيذ والتوثيق</h3>
            <p>إتمام حركة الموارد اللوجستية وتوثيق الأثر البيئي عبر تقارير رسمية تفيد منشأتك.</p>
        </article>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
