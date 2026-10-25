<?php
require_once __DIR__ . '/config.php';
$db = getDB();

if (isLoggedIn()) {
    header('Location: ' . BASE_URL . '/profile.php');
    exit;
}

$error = '';
$success = '';

// معالجة تسجيل الدخول
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $identity = trim($_POST['identity'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($identity) || empty($password)) {
        $error = 'يرجى إدخال البريد الإلكتروني أو رقم الجوال وكلمة المرور.';
    } else {
        // البحث بالبريد أو رقم الجوال
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? OR phone = ? LIMIT 1");
        $stmt->execute([$identity, $identity]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'phone' => $user['phone'],
                'role' => $user['role'],
            ];

            if (in_array($user['role'], ['admin', 'owner'])) {
                header('Location: ' . BASE_URL . '/dashboard.php');
            } else {
                header('Location: ' . BASE_URL . '/profile.php');
            }
            exit;
        } else {
            $error = 'بيانات الدخول غير صحيحة. يرجى التحقق من البريد/الجوال وكلمة المرور.';
        }
    }
}

// معالجة استعادة كلمة المرور
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'forgot') {
    $email = trim($_POST['email'] ?? '');
    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $db->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
        $stmt->execute([$token, $expires, $email]);

        $success = 'إذا كان هذا البريد مسجلاً لدينا، فستتلقى رابطاً لإعادة ضبط كلمة المرور.';
    } else {
        $error = 'يرجى إدخال بريد إلكتروني صحيح.';
    }
}

$pageTitle = 'تسجيل الدخول | قيمة';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-body rail">
    <div class="auth-card">
        <div class="auth-head">
            <span class="auth-mark">♻</span>
            <h1>أهلاً بعودتك إلى قيمة</h1>
            <p>سجّل الدخول ببريدك الإلكتروني أو رقم جوالك لمتابعة طلباتك وفرصك في سوق الموارد.</p>
        </div>

        <?php if ($error): ?>
            <div class="error-text" style="background:#fdecea; padding:12px; border-radius:6px; margin-bottom:16px;">
                <?= e($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-msg">
                <?= e($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/login.php" class="auth-form">
            <input type="hidden" name="action" value="login">

            <div class="field">
                <label>البريد الإلكتروني أو رقم الجوال *</label>
                <input type="text" name="identity" dir="ltr" required value="<?= e($_POST['identity'] ?? '') ?>" placeholder="name@company.com أو 9665xxxxxxxx">
            </div>

            <div class="field">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <label style="margin:0;">كلمة المرور *</label>
                    <button type="button" id="open-forgot-modal" style="background:none; border:none; padding:0; color:var(--forest); font-size:12px; text-decoration:underline; cursor:pointer;">
                        نسيت كلمة المرور؟
                    </button>
                </div>
                <div class="pw-wrap">
                    <input type="password" name="password" dir="ltr" required minlength="6">
                    <button type="button" class="pw-toggle" aria-label="إظهار كلمة المرور">👁</button>
                </div>
            </div>

            <button type="submit" class="button button-dark" style="margin-top:10px;">تسجيل الدخول</button>
        </form>

        <div class="auth-foot">
            ليس لديك حساب؟ <a href="<?= BASE_URL ?>/signup.php">أنشئ حساباً جديداً</a>
        </div>

        <ul class="auth-points">
            <li>✔ الدخول بالبريد الإلكتروني أو رقم الجوال</li>
            <li>✔ متابعة طلباتك وعروضك في مكان واحد</li>
            <li>✔ بياناتك محمية وفق سياسة الخصوصية</li>
        </ul>
    </div>
</div>

<!-- نافذة نسيت كلمة المرور المنبثقة -->
<div id="forgot-modal" class="modal-overlay">
    <div class="modal-box">
        <button type="button" id="close-forgot-modal" class="modal-close">&times;</button>
        <div class="auth-head" style="margin-bottom:20px;">
            <span class="auth-mark" style="width:48px; height:48px; margin-bottom:12px;">🔑</span>
            <h2 style="font-size:20px;">استعادة كلمة المرور</h2>
            <p style="font-size:13px; margin-top:8px;">أدخل بريدك الإلكتروني المسجل لإرسال رابط إعادة الضبط.</p>
        </div>
        <form method="POST" action="<?= BASE_URL ?>/login.php" style="display:grid; gap:16px;">
            <input type="hidden" name="action" value="forgot">
            <div class="field">
                <label>البريد الإلكتروني المسجل</label>
                <input type="email" name="email" dir="ltr" required placeholder="name@company.com">
            </div>
            <button type="submit" class="button button-dark" style="width:100%;">إرسال رابط الاستعادة</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
