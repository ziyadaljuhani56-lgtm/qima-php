<?php
require_once __DIR__ . '/config.php';
$db = getDB();

$currentPage = 'contact';
$pageTitle = 'تواصل مع فريق قيمة | استفسارات الموارد والشراكات';
$pageDesc = 'تواصل معنا لعرض مواردك، طلب مواد للمصانع، أو الاستفسار عن الحلول اللوجستية والخدمات.';

$success = false;
$error = '';
$defaultSubject = $_GET['subject'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $organization = trim($_POST['organization'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $details = trim($_POST['details'] ?? '');

    if (empty($name) || empty($email) || empty($subject) || empty($details)) {
        $error = 'يرجى ملء جميع الحقول الإلزامية (*).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'يرجى إدخال بريد إلكتروني صحيح.';
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO inquiries (name, email, phone, organization, subject, details) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $organization, $subject, $details]);
            $success = true;
        } catch (PDOException $e) {
            $error = 'تعذر إرسال الطلب حالياً. يرجى المحاولة لاحقاً.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-banner">
    <div class="rail">
        <div class="breadcrumbs">
            <a href="<?= BASE_URL ?>/">الرئيسية</a> &gt; <span>تواصل معنا</span>
        </div>
        <h1>تواصل مع فريق قيمة</h1>
        <p>نحن هنا لمساعدتك في ربط مواردك، تقديم عروض التوريد، والإجابة عن كافة استفسارات الشراكة والخدمات.</p>
    </div>
</div>

<div class="page-body rail">
    <div class="form-layout">
        <div>
            <?php if ($success): ?>
                <div style="background:#e8f4ec; border:1px solid #b7dfc3; color:#16643c; padding:30px; border-radius:8px; text-align:center;">
                    <div style="font-size:40px; margin-bottom:12px;">✔</div>
                    <h2 style="font-size:22px; margin-bottom:8px;">تم استلام طلبك بنجاح</h2>
                    <p style="font-size:14px; line-height:1.9;">شكرًا لتواصلك معنا. سيقوم أحد ممثلي فريق قيمة بمراجعة تفاصيل طلبك والتواصل معك خلال 24 ساعة عمل.</p>
                    <a href="<?= BASE_URL ?>/market.php" class="button button-dark" style="margin-top:20px;">العودة لسوق الموارد</a>
                </div>
            <?php else: ?>
                <?php if ($error): ?>
                    <div class="error-text" style="background:#fdecea; padding:12px 16px; border-radius:6px; margin-bottom:20px;">
                        <?= e($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= BASE_URL ?>/contact.php" class="inquiry-form">
                    <div class="field">
                        <label>الاسم الكامل *</label>
                        <input type="text" name="name" required value="<?= e($_POST['name'] ?? '') ?>" placeholder="الاسم ثلاثي">
                    </div>
                    <div class="field">
                        <label>البريد الإلكتروني *</label>
                        <input type="email" name="email" dir="ltr" required value="<?= e($_POST['email'] ?? '') ?>" placeholder="name@company.com">
                    </div>
                    <div class="field">
                        <label>رقم الجوال</label>
                        <input type="tel" name="phone" dir="ltr" value="<?= e($_POST['phone'] ?? '') ?>" placeholder="9665xxxxxxxx">
                    </div>
                    <div class="field">
                        <label>اسم المنشأة أو المصنع</label>
                        <input type="text" name="organization" value="<?= e($_POST['organization'] ?? '') ?>" placeholder="شركة / مصنع / جهة">
                    </div>
                    <div class="field full-width">
                        <label>موضوع الطلب *</label>
                        <input type="text" name="subject" required value="<?= e($_POST['subject'] ?? $defaultSubject) ?>" placeholder="مثال: توريد مواد بلاستيكية، طلب خدمات لوجستية...">
                    </div>
                    <div class="field full-width">
                        <label>تفاصيل المورد أو الاستفسار *</label>
                        <textarea name="details" rows="5" required placeholder="اذكر نوع المادة، الكميات المتوقعة، الموقع الجغرافي، وأي متطلبات فنية..."><?= e($_POST['details'] ?? '') ?></textarea>
                    </div>

                    <div class="full-width" style="margin-top:10px;">
                        <button type="submit" class="button button-dark">إرسال الاستفسار إلى فريق قيمة</button>
                    </div>
                </form>
            <?php endif; ?>
        </div>

        <aside class="form-aside">
            <h2>لنصنع قيمة مشتركة</h2>
            <p>سواء كنت مورداً يبحث عن منفذ لبيعه، أو مصنعاً يسعى لتأمين مواد خام معاد تدويرها بمواصفات دقيقة، فإن منصة قيمة تمنحك الشريك المناسب.</p>

            <ul style="list-style:none; padding:0;">
                <li><strong>البريد الإلكتروني:</strong> contact@qima.sa</li>
                <li><strong>الهاتف الموحد:</strong> +966 11 000 0000</li>
                <li><strong>ساعات العمل:</strong> الأحد - الخميس (8:00 ص - 5:00 م)</li>
                <li><strong>الموقع:</strong> الرياض، المملكة العربية السعودية</li>
            </ul>

            <div style="margin-top:24px; padding:16px; background:var(--soft); border-radius:6px; font-size:12px; line-height:1.8; color:var(--gray);">
                جميع المراسلات تخضع لسرية تامة وبياناتك محمية وفق سياسة الخصوصية المعمول بها في منصة قيمة.
            </div>
        </aside>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
