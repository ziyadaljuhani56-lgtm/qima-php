<?php
require_once __DIR__ . '/config.php';
$db = getDB();

if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/profile.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['passwordConfirm'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        $error = 'يرجى ملء جميع الحقول المطلوبة (*).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'يرجى إدخال بريد إلكتروني صحيح.';
    } elseif (strlen($password) < 6) {
        $error = 'كلمة المرور يجب ألا تقل عن 6 خانات.';
    } elseif ($password !== $passwordConfirm) {
        $error = 'كلمتا المرور غير متطابقتين.';
    } else {
        // فحص وجود البريد مسبقاً
        $check = $db->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $error = 'هذا البريد الإلكتروني مسجل مسبقاً لدينا.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, 'user')");
            $stmt->execute([$name, $email, $phone, $hash]);

            $newId = $db->lastInsertId();
            $_SESSION['user'] = [
                'id' => $newId,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'role' => 'user',
            ];

            header('Location: ' . BASE_URL . '/profile.php');
            exit;
        }
    }
}

$pageTitle = 'إنشاء حساب جديد | قيمة';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-body rail">
    <div class="auth-card">
        <div class="auth-head">
            <span class="auth-mark">♻</span>
            <h1>أنشئ حسابك على قيمة</h1>
            <p>سجّل للوصول إلى سوق الموارد، إدارة طلباتك، ومتابعة فرص الاقتصاد الدائري بالمملكة.</p>
        </div>

        <?php if ($error): ?>
            <div class="error-text" style="background:#fdecea; padding:12px; border-radius:6px; margin-bottom:16px;">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/signup.php" class="auth-form">
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
                <input type="tel" name="phone" dir="ltr" value="<?= e($_POST['phone'] ?? '') ?>" placeholder="مثال: 9665xxxxxxxx">
            </div>

            <div class="field">
                <label>كلمة المرور *</label>
                <div class="pw-wrap">
                    <input type="password" name="password" dir="ltr" required minlength="6">
                    <button type="button" class="pw-toggle">👁</button>
                </div>
            </div>

            <div class="field">
                <label>تأكيد كلمة المرور *</label>
                <div class="pw-wrap">
                    <input type="password" name="passwordConfirm" dir="ltr" required minlength="6">
                    <button type="button" class="pw-toggle">👁</button>
                </div>
            </div>

            <button type="submit" class="button button-dark" style="margin-top:10px;">إنشاء الحساب</button>
        </form>

        <div class="auth-foot">
            لديك حساب بالفعل؟ <a href="<?= BASE_URL ?>/login.php">سجّل الدخول</a>
        </div>

        <ul class="auth-points">
            <li>✔ الدخول بالبريد الإلكتروني أو رقم الجوال</li>
            <li>✔ متابعة طلباتك وفرصك في مكان واحد</li>
            <li>✔ بياناتك محمية وفق سياسة الخصوصية</li>
        </ul>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
