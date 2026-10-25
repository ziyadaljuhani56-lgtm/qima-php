<?php
require_once __DIR__ . '/config.php';

$currentPage = 'about';
$pageTitle = 'عن منصة قيمة | رؤيتنا وأهدافنا';
$pageDesc = 'منصة سعودية لتمكين الاقتصاد الدائري وربط سلاسل إمداد الموارد المستدامة بين الموردين والمصانع.';

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-banner">
    <div class="rail">
        <div class="breadcrumbs">
            <a href="<?= BASE_URL ?>/">الرئيسية</a> &gt; <span>عن قيمة</span>
        </div>
        <h1>عن منصة قيمة</h1>
        <p>نحوّل الاستدامة من التزام نظري إلى قيمة اقتصادية وتشغيلية ملموسة لكافة منشآت القطاع الصناعي والتجاري.</p>
    </div>
</div>

<div class="page-body rail">
    <div class="policy-content" style="max-width:900px; margin:0 auto;">
        <span class="eyebrow">من نحن</span>
        <h2>رؤية جديدة لإدارة وتداول الموارد</h2>
        <p>
            انطلقت <strong>قيمة</strong> لتكون الجسر الرقمي والميداني الموثوق الذي يجمع بين منتجي الموارد القابلة لإعادة التدوير والمصانع والمشترين في المملكة العربية السعودية. نؤمن بأن كل مخلف أو فائض إنتاجي هو مورد ذو قيمة اقتصادية يمكن استعادتها بإدارته الذكية والربط الصحيح.
        </p>

        <h2>مهمتنا</h2>
        <p>
            تسهيل الوصول إلى أسواق الموارد المستدامة، ورفع شفافية التسعير والمواصفات، وتوفير حلول لوجستية وتقارير أثر بيئي تدعم تحقيق مستهدفات رؤية المملكة 2030 في الحفاظ على البيئة واستدامة الموارد الطبيعية.
        </p>

        <h2>قيمنا الأساسية</h2>
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:20px; margin-top:20px;">
            <div style="background:var(--soft); padding:20px; border-radius:8px; border:1px solid var(--line);">
                <h3 style="font-size:16px; margin-bottom:8px;">الشفافية والموثوقية</h3>
                <p style="font-size:13px; line-height:1.8; color:var(--gray);">التحقق من البيانات والمواصفات لضمان حقوق كافة الأطراف في كل معاملة.</p>
            </div>
            <div style="background:var(--soft); padding:20px; border-radius:8px; border:1px solid var(--line);">
                <h3 style="font-size:16px; margin-bottom:8px;">الكفاءة التشغيلية</h3>
                <p style="font-size:13px; line-height:1.8; color:var(--gray);">تقليص الهدر وسرعة نقل الموارد إلى المصانع المستفيدة بأقل تكلفة ممكنة.</p>
            </div>
            <div style="background:var(--soft); padding:20px; border-radius:8px; border:1px solid var(--line);">
                <h3 style="font-size:16px; margin-bottom:8px;">الأثر المستدام</h3>
                <p style="font-size:13px; line-height:1.8; color:var(--gray);">قياس وتوثيق المساهمة في خفض الانبعاثات وتحويل النفايات عن المرادم.</p>
            </div>
        </div>

        <div style="margin-top:40px; text-align:center;">
            <a href="<?= BASE_URL ?>/market.php" class="button button-dark">استكشف سوق الموارد</a>
            <a href="<?= BASE_URL ?>/contact.php" class="button button-outline" style="margin-right:12px;">انضم كشريك أو مورد</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
