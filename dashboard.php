<?php
require_once __DIR__ . '/config.php';
$db = getDB();

if (!isAdmin()) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

$user = currentUser();

// إحصائيات
$inquiryCount = $db->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();
$newInquiryCount = $db->query("SELECT COUNT(*) FROM inquiries WHERE status = 'new'")->fetchColumn();
$productCount = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
$userCount = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$subscriberCount = $db->query("SELECT COUNT(*) FROM newsletter_subscribers")->fetchColumn();

// تحديث حالة الاستفسار
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $inquiryId = (int)$_POST['inquiry_id'];
    $newStatus = $_POST['status'];
    $stmt = $db->prepare("UPDATE inquiries SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $inquiryId]);
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit;
}

// إضافة منتج جديد لسوق الموارد
$prodError = '';
$prodSuccess = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_product') {
    $title = trim($_POST['title'] ?? '');
    $handle = trim($_POST['handle'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $unit = trim($_POST['unit'] ?? 'طن');
    $location = trim($_POST['location'] ?? 'الرياض');
    $quantity = trim($_POST['quantity'] ?? 'متوفر');
    $description = trim($_POST['description'] ?? '');
    $imageUrl = trim($_POST['image_url'] ?? '');

    if (empty($title) || empty($handle) || empty($category)) {
        $prodError = 'يرجى إكمال البيانات الأساسية للمنتج.';
    } else {
        try {
            $pStmt = $db->prepare("INSERT INTO products (handle, title, category, price, unit, location, quantity, description, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $pStmt->execute([$handle, $title, $category, $price, $unit, $location, $quantity, $description, $imageUrl]);
            $prodSuccess = 'تمت إضافة المورد الجديد للسوق بنجاح.';
        } catch (PDOException $e) {
            $prodError = 'الرمز التعريفي (Handle) مستخدم بالفعل، يرجى اختيار رمز آخر.';
        }
    }
}

// قائمة الاستفسارات الحديثة
$inquiries = $db->query("SELECT * FROM inquiries ORDER BY id DESC LIMIT 20")->fetchAll();
// قائمة المنتجات
$allProducts = $db->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
// قائمة التصنيفات
$materials = $db->query("SELECT * FROM materials ORDER BY id ASC")->fetchAll();

$pageTitle = 'لوحة التحكم والإدارة | قيمة';
require_once __DIR__ . '/includes/header.php';
?>

<div class="dash-header">
    <div class="rail">
        <span class="eyebrow" style="color:var(--lime);">نظام الإدارة المركزي</span>
        <h1>لوحة تحكم منصة قيمة</h1>
        <p>إدارة طلبات واستفسارات المنشآت، ومتابعة سوق الموارد وقائمة المشتركين والحسابات.</p>
    </div>
</div>

<div class="dash-body rail">
    <!-- بطاقات الإحصائيات -->
    <div class="dash-grid" style="margin-bottom:35px;">
        <div class="stat-card">
            <span class="eyebrow">الاستفسارات الجديدة</span>
            <strong><?= $newInquiryCount ?></strong>
            <span>من إجمالي <?= $inquiryCount ?> طلب واستفسار</span>
        </div>
        <div class="stat-card">
            <span class="eyebrow">الموارد المعروضة بالسوق</span>
            <strong><?= $productCount ?></strong>
            <span>عروض نشطة للمصانع والمنشآت</span>
        </div>
        <div class="stat-card">
            <span class="eyebrow">المستخدمون المسجلون</span>
            <strong><?= $userCount ?></strong>
            <span>حساب مسجل بالمنصة</span>
        </div>
        <div class="stat-card">
            <span class="eyebrow">مشتركو النشرة</span>
            <strong><?= $subscriberCount ?></strong>
            <span>مهتمون بتحديثات السوق</span>
        </div>
    </div>

    <!-- جدول الاستفسارات الواردة -->
    <div style="margin-bottom:50px;">
        <h2 style="font-size:22px; margin-bottom:14px;">الاستفسارات وطلبات الموارد الحديثة</h2>
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المرسل والمنشأة</th>
                        <th>معلومات الاتصال</th>
                        <th>موضوع الطلب</th>
                        <th>التفاصيل والمواصفات</th>
                        <th>الحالة</th>
                        <th>الإجراء</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($inquiries)): ?>
                        <tr><td colspan="7" style="text-align:center; padding:30px; color:var(--gray);">لا توجد استفسارات واردة بعد.</td></tr>
                    <?php else: ?>
                        <?php foreach ($inquiries as $inq): ?>
                            <tr>
                                <td><?= $inq['id'] ?></td>
                                <td>
                                    <strong><?= e($inq['name']) ?></strong><br>
                                    <small style="color:var(--gray);"><?= e($inq['organization'] ?: 'غير محدد') ?></small>
                                </td>
                                <td>
                                    <span dir="ltr"><?= e($inq['email']) ?></span><br>
                                    <small dir="ltr" style="color:var(--gray);"><?= e($inq['phone'] ?: '-') ?></small>
                                </td>
                                <td><?= e($inq['subject']) ?></td>
                                <td style="max-width:280px; font-size:12px; line-height:1.6;"><?= nl2br(e($inq['details'])) ?></td>
                                <td>
                                    <span class="badge"><?= e($inq['status']) ?></span>
                                </td>
                                <td>
                                    <form method="POST" action="<?= BASE_URL ?>/dashboard.php" style="display:inline-flex; gap:6px;">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="inquiry_id" value="<?= $inq['id'] ?>">
                                        <select name="status" style="font-size:11px; padding:4px 6px; border-radius:4px; border:1px solid var(--line);">
                                            <option value="new" <?= $inq['status'] === 'new' ? 'selected' : '' ?>>جديد</option>
                                            <option value="reviewed" <?= $inq['status'] === 'reviewed' ? 'selected' : '' ?>>تمت المراجعة</option>
                                            <option value="contacted" <?= $inq['status'] === 'contacted' ? 'selected' : '' ?>>تم التواصل</option>
                                            <option value="closed" <?= $inq['status'] === 'closed' ? 'selected' : '' ?>>مغلق</option>
                                        </select>
                                        <button type="submit" class="button button-outline" style="padding:4px 8px; min-height:28px; font-size:11px;">تحديث</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- إدارة وإضافة منتجات سوق الموارد -->
    <div style="background:white; border:1px solid var(--line); border-radius:8px; padding:32px; margin-bottom:40px;">
        <h2 style="font-size:22px; margin-bottom:14px;">إضافة مورد أو مادة جديدة إلى السوق</h2>

        <?php if ($prodSuccess): ?>
            <div class="success-msg"><?= e($prodSuccess) ?></div>
        <?php endif; ?>
        <?php if ($prodError): ?>
            <div class="error-text" style="background:#fdecea; padding:12px; border-radius:6px; margin-bottom:16px;">
                <?= e($prodError) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/dashboard.php" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
            <input type="hidden" name="action" value="add_product">

            <div class="field">
                <label>اسم المورد / المنتج *</label>
                <input type="text" name="title" required placeholder="مثال: حبيبات بلاستيك معاد تدويره (PP)">
            </div>

            <div class="field">
                <label>الرمز التعريفي الفريد (Handle) * (بالإنجليزية بدون مسافات)</label>
                <input type="text" name="handle" dir="ltr" required placeholder="recycled-pp-pellets">
            </div>

            <div class="field">
                <label>التصنيف *</label>
                <select name="category" required>
                    <?php foreach ($materials as $m): ?>
                        <option value="<?= e($m['slug']) ?>"><?= e($m['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label>السعر (ر.س) *</label>
                <input type="number" step="0.01" name="price" required placeholder="2800.00">
            </div>

            <div class="field">
                <label>وحدة القياس</label>
                <input type="text" name="unit" value="طن" placeholder="طن، كجم، حبة...">
            </div>

            <div class="field">
                <label>الموقع الجغرافي</label>
                <input type="text" name="location" value="الرياض، المملكة العربية السعودية">
            </div>

            <div class="field">
                <label>الكمية المتوفرة</label>
                <input type="text" name="quantity" placeholder="مثال: 40 طن شهرياً">
            </div>

            <div class="field">
                <label>رابط الصورة (URL)</label>
                <input type="url" name="image_url" dir="ltr" placeholder="https://example.com/image.jpg" value="https://images.unsplash.com/photo-1530587191325-3db32d826c18?auto=format&fit=crop&w=800&q=80">
            </div>

            <div class="field full-width">
                <label>الوصف الفني للمورد والمواصفات</label>
                <textarea name="description" rows="3" placeholder="مواصفات المادة، نسبة النقاء، التعبئة..."></textarea>
            </div>

            <div class="full-width">
                <button type="submit" class="button button-dark">نشر المورد في السوق</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
