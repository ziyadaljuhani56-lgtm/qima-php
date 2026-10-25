<?php
$currentAdminPage = 'customers';
$adminTitle = 'إدارة العملاء والشركاء';
require_once __DIR__ . '/includes/header.php';

$msg = '';
$err = '';

// تعديل دور/صلاحية المستخدم
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_role') {
    $targetUserId = (int)$_POST['user_id'];
    $newRole = $_POST['role'];

    // حماية حساب المالك الأساسي من خفض رتبته
    if ($targetUserId === 1 && $user['id'] !== 1) {
        $err = 'غير مسموح بتعديل صلاحيات الحساب المؤسس.';
    } else {
        try {
            $stmt = $db->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->execute([$newRole, $targetUserId]);
            $msg = 'تم تحديث صلاحيات المستخدم بنجاح.';
        } catch (Exception $e) {
            $err = 'فشل تحديث الصلاحية: ' . $e->getMessage();
        }
    }
}

// إضافة مستخدم أو عميل جديد
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_user') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'member';

    if (empty($name) || empty($email) || empty($password)) {
        $err = 'يرجى ملء الحقول الإلزامية.';
    } else {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $hash, $role]);
            $msg = 'تم إنشاء الحساب بنجاح.';
        } catch (PDOException $e) {
            $err = 'البريد الإلكتروني مسجل بالفعل.';
        }
    }
}

// حذف مستخدم
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_user') {
    $targetUserId = (int)$_POST['user_id'];
    if ($targetUserId === $user['id'] || $targetUserId === 1) {
        $err = 'لا يمكنك حذف حسابك الحالي أو حساب المالك المؤسس.';
    } else {
        try {
            $db->prepare("DELETE FROM users WHERE id = ?")->execute([$targetUserId]);
            $msg = 'تم حذف الحساب بنجاح.';
        } catch (Exception $e) {
            $err = 'تعذر حذف الحساب: ' . $e->getMessage();
        }
    }
}

$users = $db->query("SELECT * FROM users ORDER BY id ASC")->fetchAll();
?>

<?php if ($msg): ?><div class="alert-box alert-success"><?= e($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert-box alert-error"><?= e($err) ?></div><?php endif; ?>

<div class="admin-card">
    <div class="card-header-row">
        <div>
            <h3>سجل العملاء والمستخدمين (<?= count($users) ?>)</h3>
            <p style="font-size:12px; color:var(--admin-text-muted); margin-top:4px;">إدارة حسابات المنشآت، تعيين الصلاحيات (Owner / Admin / Member).</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('newUserModal').style.display = 'flex'">
            ➕ إضافة حساب جديد
        </button>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>البريد الإلكتروني</th>
                    <th>رقم الجوال</th>
                    <th>الدور والصلاحية</th>
                    <th>تاريخ التسجيل</th>
                    <th>التحكم</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td>
                            <strong><?= e($u['name']) ?></strong>
                            <?php if ($u['id'] === $user['id']): ?>
                                <small style="color:var(--admin-primary);">(أنت)</small>
                            <?php endif; ?>
                        </td>
                        <td dir="ltr"><?= e($u['email']) ?></td>
                        <td dir="ltr"><?= e($u['phone'] ?: '-') ?></td>
                        <td>
                            <form method="POST" action="<?= BASE_URL ?>/admin/customers.php" style="display:inline-flex; gap:6px; align-items:center;">
                                <input type="hidden" name="action" value="update_role">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <select name="role" class="input-field" style="padding:4px 8px; font-size:11px; width:auto;" onchange="this.form.submit()" <?= $u['id'] === 1 && $user['id'] !== 1 ? 'disabled' : '' ?>>
                                    <option value="owner" <?= $u['role'] === 'owner' ? 'selected' : '' ?>>owner (مالك)</option>
                                    <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>admin (مشرف)</option>
                                    <option value="member" <?= $u['role'] === 'member' || $u['role'] === 'user' ? 'selected' : '' ?>>member (عميل)</option>
                                </select>
                            </form>
                        </td>
                        <td style="font-size:11px; color:var(--admin-text-muted);">
                            <?= e(substr($u['created_at'], 0, 10)) ?>
                        </td>
                        <td>
                            <?php if ($u['id'] !== $user['id'] && $u['id'] !== 1): ?>
                                <button type="button" class="btn btn-danger btn-sm" onclick="if(confirm('هل أنت متأكد من حذف هذا الحساب؟')) { document.getElementById('delUser_<?= $u['id'] ?>').submit(); }">حذف</button>
                                <form id="delUser_<?= $u['id'] ?>" method="POST" action="<?= BASE_URL ?>/admin/customers.php" style="display:none;">
                                    <input type="hidden" name="action" value="delete_user">
                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                </form>
                            <?php else: ?>
                                <span style="font-size:11px; color:var(--admin-text-muted);">حساب محمي</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- نافذة إضافة مستخدم جديد -->
<div id="newUserModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); backdrop-filter:blur(4px); z-index:1000; align-items:center; justify-content:center; padding:20px;">
    <div class="admin-card" style="max-width:500px; width:100%; border:1px solid var(--admin-primary); margin:0;">
        <div class="card-header-row" style="margin-bottom:16px;">
            <h3>إنشاء حساب جديد في النظام</h3>
            <button type="button" onclick="document.getElementById('newUserModal').style.display = 'none'" class="btn btn-outline btn-sm">&times;</button>
        </div>

        <form method="POST" action="<?= BASE_URL ?>/admin/customers.php" style="display:grid; gap:14px;">
            <input type="hidden" name="action" value="create_user">

            <div class="form-group">
                <label>الاسم الكامل *</label>
                <input type="text" name="name" class="input-field" required placeholder="الاسم الثلاثي">
            </div>

            <div class="form-group">
                <label>البريد الإلكتروني *</label>
                <input type="email" name="email" dir="ltr" class="input-field" required placeholder="user@company.com">
            </div>

            <div class="form-group">
                <label>رقم الجوال</label>
                <input type="tel" name="phone" dir="ltr" class="input-field" placeholder="9665xxxxxxxx">
            </div>

            <div class="form-group">
                <label>كلمة المرور *</label>
                <input type="password" name="password" dir="ltr" class="input-field" required minlength="6" placeholder="******">
            </div>

            <div class="form-group">
                <label>الصلاحية (الدور)</label>
                <select name="role" class="input-field">
                    <option value="member">عضو عادي / عميل (Member)</option>
                    <option value="admin">مشرف متجر (Admin)</option>
                    <option value="owner">مالك كامل الصلاحيات (Owner)</option>
                </select>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:10px;">
                <button type="button" onclick="document.getElementById('newUserModal').style.display = 'none'" class="btn btn-outline">إلغاء</button>
                <button type="submit" class="btn btn-primary">إنشاء الحساب</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
