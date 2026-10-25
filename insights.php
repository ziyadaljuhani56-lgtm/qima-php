<?php
require_once __DIR__ . '/config.php';

$currentPage = 'insights';
$pageTitle = 'المؤشرات وتقارير الأثر | قيمة';
$pageDesc = 'مؤشرات حركة سوق الموارد المستدامة والأثر البيئي والاقتصادي للاقتصاد الدائري في المملكة.';

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-banner">
    <div class="rail">
        <div class="breadcrumbs">
            <a href="<?= BASE_URL ?>/">الرئيسية</a> &gt; <span>المؤشرات والأثر</span>
        </div>
        <h1>مؤشرات السوق وتقارير الأثر</h1>
        <p>نظرة تحليلية شاملة على حركة تداول الموارد ومعدلات استعادة القيمة ودعم معايير الاستدامة والحوكمة (ESG).</p>
    </div>
</div>

<div class="page-body rail">
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:20px; margin-bottom:40px;">
        <div class="stat-card">
            <span class="eyebrow">إجمالي المواد المتداولة</span>
            <strong>+14,200</strong>
            <span>طن متري من الموارد المفروزة والمعاد تدويرها</span>
        </div>
        <div class="stat-card">
            <span class="eyebrow">خفض الانبعاثات التقديري</span>
            <strong>22,800</strong>
            <span>طن مكافئ من ثاني أكسيد الكربون (CO2e)</span>
        </div>
        <div class="stat-card">
            <span class="eyebrow">المنشآت والمصانع الشريكة</span>
            <strong>+180</strong>
            <span>مورد ومصنع ومرفق معالجة مسجل في المنصة</span>
        </div>
        <div class="stat-card">
            <span class="eyebrow">نسبة التحويل عن المرادم</span>
            <strong>86%</strong>
            <span>من الموارد المدخلة في سلاسل القيمة المباشرة</span>
        </div>
    </div>

    <div class="policy-content" style="max-width:900px;">
        <span class="eyebrow">منهجية القياس</span>
        <h2>أهمية توثيق تقارير ESG للمنشآت الصناعية</h2>
        <p>
            تتجه الأنظمة الرقابية والأسواق التمويلية في المملكة نحو إلزامية الإفصاح البيئي والحوكمة الاجتماعية (ESG). تساعد منصة قيمة المنشآت على تتبع مسار نفاياتها ومواردها القابلة للتدوير وتوثيق الكميات عبر شهادات استلام وتقارير رقمية معتمدة تعزز تصنيف المنشأة وتدعم امتثالها التنظيمي.
        </p>

        <h2>مؤشرات تسعير الموارد</h2>
        <p>
            تتأثر أسعار المواد الخام المعاد تدويرها بعدة عوامل رئيسية: درجة الفرز والنقاء، الموقع الجغرافي للمورد، حجم الكمية المعروضة، وتكاليف النقل اللوجستي. تقدم قيمة بيانات استرشادية دورية لتمكين الموردين والمشترين من إتمام صفقات عادلة ومربحة للطرفين.
        </p>

        <div style="margin-top:30px;">
            <a href="<?= BASE_URL ?>/contact.php?subject=<?= urlencode('طلب تقرير أثر أو استشارة ESG') ?>" class="button button-dark">
                طلب استشارة أو تقرير أثر لمنشأتك
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
