<?php
require_once __DIR__ . '/config.php';
$db = getDB();

if (!isLoggedIn()) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

$user = currentUser();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (empty($name)) {
        $error = 'يرجى إدخال الاسم.';
    } else {
        $stmt = $db->prepare("UPDATE users SET name = ?, phone = ? WHERE id = ?");
        $stmt->execute([$name, $phone, $user['id']]);

        $_SESSION['user']['name'] = $name;
        $_SESSION['user']['phone'] = $phone;
        $user = $_SESSION['user'];
        $success = 'تم تحديث بيانات ملفك الشخصي بنجاح.';
    }
}

// جلب استفسارات المستخدم إذا كان بريده مطابقاً
$inqStmt = $db->prepare("SELECT * FROM inquiries WHERE email = ? ORDER BY id DESC");
$inqStmt->execute([$user['email']]);
$userInquiries = $inqStmt->fetchAll();

$pageTitle = 'الملف الشخصي | قيمة';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-banner">
    <div class="rail">
        <h1>الملف الشخصي</h1>
        <p>مرحباً بك، يمكنك إدارة بيانات حسابك والاطلاع على طلباتك السابقة عبر المنصة.</p>
    </div>
</div>

<div class="page-body rail">
    <div style="display:grid; grid-template-columns:1.2fr 1.8fr; gap:40px;">
        <div style="background:white; border:1px solid var(--line); border-radius:8px; padding:28px;">
            <h3 style="margin-bottom:18px;">بيانات الحساب</h3>

            <?php if ($success): ?>
                <div class="success-msg"><?= e($success) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="error-text" style="background:#fdecea; padding:12px; border-radius:6px; margin-bottom:16px;">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>/profile.php" style="display:grid; gap:16px;">
                <div class="field">
                    <label>الاسم الكامل</label>
                    <input type="text" name="name" required value="<?= e($user['name']) ?>">
                </div>
                <div class="field">
                    <label>البريد الإلكتروني (غير قابل للتعديل)</label>
                    <input type="email" value="<?= e($user['email']) ?>" disabled style="background:#f5f5f3;">
                </div>
                <div class="field">
                    <label>رقم الجوال</label>
                    <input type="tel" name="phone" dir="ltr" value="<?= e($user['phone'] ?? '') ?>">
                </div>
                <div class="field">
                    <label>نوع الحساب</label>
                    <span class="badge" style="width:max-content;"><?= e($user['role']) ?></span>
                </div>
                <button type="submit" class="button button-dark" style="margin-top:10px;">حفظ التعديلات</button>
            </form>
        </div>

        <div>
            <h3 style="margin-bottom:18px;">طلباتك واستفساراتك المسجلة</h3>
            <?php if (empty($userInquiries)): ?>
                <div style="background:var(--soft); padding:30px; border-radius:8px; border:1px solid var(--line); text-align:center;">
                    <p style="color:var(--gray);">لم تقم بإرسال أي طلبات أو استفسارات بعد.</p>
                    <a href="<?= BASE_URL ?>/market.php" class="button button-outline" style="margin-top:14px;">تصفح سوق الموارد واطلب الآن</a>
                </div>
            <?php else: ?>
                <div style="display:grid; gap:14px;">
                    <?php foreach ($userInquiries as $inq): ?>
                        <div style="background:white; border:1px solid var(--line); border-radius:8px; padding:18px;">
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <strong style="font-size:16px;"><?= e($inq['subject']) ?></strong>
                                <span class="badge"><?= e($inq['status']) ?></span>
                            </div>
                            <p style="font-size:13px; color:var(--gray); margin-top:8px; line-height:1.7;"><?= e($inq['details']) ?></p>
                            <span style="display:block; font-size:11px; color:#8fa895; margin-top:10px;"><?= e($inq['created_at']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
