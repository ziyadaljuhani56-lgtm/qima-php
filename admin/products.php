<?php
$currentAdminPage = 'products';
$adminTitle = 'إدارة منتجات وعروض سوق الموارد';
require_once __DIR__ . '/includes/header.php';

$msg = '';
$err = '';

// إضافة منتج جديد
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_product') {
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
        $err = 'يرجى إكمال البيانات الأساسية للمنتج.';
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO products (handle, title, category, price, unit, location, quantity, description, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$handle, $title, $category, $price, $unit, $location, $quantity, $description, $imageUrl]);
            $msg = 'تمت إضافة المورد الجديد إلى سوق المتجر بنجاح.';
        } catch (PDOException $e) {
            $err = 'الرمز التعريفي (Handle) مستخدم مسبقاً، يرجى اختيار رمز فريد.';
        }
    }
}

// تحديث منتج
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_product') {
    $productId = (int)$_POST['product_id'];
    $title = trim($_POST['title'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $unit = trim($_POST['unit'] ?? 'طن');
    $location = trim($_POST['location'] ?? 'الرياض');
    $quantity = trim($_POST['quantity'] ?? 'متوفر');
    $description = trim($_POST['description'] ?? '');
    $imageUrl = trim($_POST['image_url'] ?? '');

    try {
        $stmt = $db->prepare("UPDATE products SET title = ?, category = ?, price = ?, unit = ?, location = ?, quantity = ?, description = ?, image_url = ? WHERE id = ?");
        $stmt->execute([$title, $category, $price, $unit, $location, $quantity, $description, $imageUrl, $productId]);
        $msg = 'تم تحديث بيانات المورد بنجاح وينعكس الآن على المتجر مباشرة.';
    } catch (Exception $e) {
        $err = 'فشل تحديث المورد: ' . $e->getMessage();
    }
}

// حذف منتج
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_product') {
    $productId = (int)$_POST['product_id'];
    try {
        $db->prepare("DELETE FROM products WHERE id = ?")->execute([$productId]);
        $msg = 'تم حذف المورد من المتجر بنجاح.';
    } catch (Exception $e) {
        $err = 'تعذر حذف المورد: ' . $e->getMessage();
    }
}

$products = $db->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
$materials = $db->query("SELECT * FROM materials ORDER BY id ASC")->fetchAll();
?>

<?php if ($msg): ?><div class="alert-box alert-success"><?= e($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert-box alert-error"><?= e($err) ?></div><?php endif; ?>

<div class="admin-card">
    <div class="card-header-row">
        <div>
            <h3>الموارد والمنتجات المعروضة في المتجر (<?= count($products) ?>)</h3>
            <p style="font-size:12px; color:var(--admin-text-muted); margin-top:4px;">أي تغيير هنا ينعكس فوراً وتلقائياً على صفحات سوق الموارد وقيمة المتجر.</p>
        </div>
        <button type="button" class="btn btn-primary" onclick="document.getElementById('newProductModal').style.display = 'flex'">
            ➕ إضافة مورد جديد
        </button>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>الصورة</th>
                    <th>اسم المورد</th>
                    <th>التصنيف</th>
                    <th>السعر / الوحدة</th>
                    <th>الكمية المتاحة</th>
                    <th>الموقع الجغرافي</th>
                    <th>التحكم والإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                    <tr>
                        <td>
                            <img src="<?= e($p['image_url']) ?>" alt="<?= e($p['title']) ?>" style="width:50px; height:50px; object-fit:cover; border-radius:6px; border:1px solid var(--admin-border);">
                        </td>
                        <td>
                            <strong><?= e($p['title']) ?></strong><br>
                            <small dir="ltr" style="color:var(--admin-primary); font-size:11px;">#<?= e($p['handle']) ?></small>
                        </td>
                        <td>
                            <span class="status-pill member"><?= e($p['category']) ?></span>
                        </td>
                        <td>
                            <strong style="color:white;"><?= number_format($p['price'], 2) ?></strong> ر.س / <?= e($p['unit']) ?>
                        </td>
                        <td><?= e($p['quantity']) ?></td>
                        <td><?= e($p['location']) ?></td>
                        <td>
                            <div style="display:flex; gap:8px;">
                                <a href="<?= BASE_URL ?>/product.php?handle=<?= urlencode($p['handle']) ?>" target="_blank" class="btn btn-outline btn-sm">عرض في المتجر 🔗</a>
                                <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('editProductModal_<?= $p['id'] ?>').style.display = 'flex'">تعديل ✏️</button>
                                <button type="button" class="btn btn-danger btn-sm" onclick="if(confirm('هل أنت متأكد من حذف هذا المورد من المتجر؟')) { document.getElementById('delProduct_<?= $p['id'] ?>').submit(); }">حذف 🗑️</button>
                            </div>

                            <form id="delProduct_<?= $p['id'] ?>" method="POST" action="<?= BASE_URL ?>/admin/products.php" style="display:none;">
                                <input type="hidden" name="action" value="delete_product">
                                <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                            </form>
                        </td>
                    </tr>

                    <!-- نافذة تعديل المنتج -->
                    <div id="editProductModal_<?= $p['id'] ?>" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); backdrop-filter:blur(4px); z-index:1000; align-items:center; justify-content:center; padding:20px;">
                        <div class="admin-card" style="max-width:650px; width:100%; border:1px solid var(--admin-primary); margin:0; max-height:90vh; overflow-y:auto;">
                            <div class="card-header-row" style="margin-bottom:16px;">
                                <h3>تعديل المورد: <?= e($p['title']) ?></h3>
                                <button type="button" onclick="document.getElementById('editProductModal_<?= $p['id'] ?>').style.display = 'none'" class="btn btn-outline btn-sm">&times;</button>
                            </div>

                            <form method="POST" action="<?= BASE_URL ?>/admin/products.php" style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                                <input type="hidden" name="action" value="update_product">
                                <input type="hidden" name="product_id" value="<?= $p['id'] ?>">

                                <div class="form-group" style="grid-column:1/-1;">
                                    <label>اسم المورد *</label>
                                    <input type="text" name="title" class="input-field" required value="<?= e($p['title']) ?>">
                                </div>

                                <div class="form-group">
                                    <label>التصنيف *</label>
                                    <select name="category" class="input-field" required>
                                        <?php foreach ($materials as $m): ?>
                                            <option value="<?= e($m['slug']) ?>" <?= $p['category'] === $m['slug'] ? 'selected' : '' ?>><?= e($m['title']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>السعر (ر.س) *</label>
                                    <input type="number" step="0.01" name="price" class="input-field" required value="<?= $p['price'] ?>">
                                </div>

                                <div class="form-group">
                                    <label>وحدة القياس</label>
                                    <input type="text" name="unit" class="input-field" value="<?= e($p['unit']) ?>">
                                </div>

                                <div class="form-group">
                                    <label>الموقع</label>
                                    <input type="text" name="location" class="input-field" value="<?= e($p['location']) ?>">
                                </div>

                                <div class="form-group">
                                    <label>الكمية المتوفرة</label>
                                    <input type="text" name="quantity" class="input-field" value="<?= e($p['quantity']) ?>">
                                </div>

                                <div class="form-group">
                                    <label>رابط الصورة</label>
                                    <input type="url" name="image_url" dir="ltr" class="input-field" value="<?= e($p['image_url']) ?>">
                                </div>

                                <div class="form-group" style="grid-column:1/-1;">
                                    <label>الوصف الفني</label>
                                    <textarea name="description" rows="3" class="input-field"><?= e($p['description']) ?></textarea>
                                </div>

                                <div style="grid-column:1/-1; display:flex; justify-content:flex-end; gap:10px; margin-top:10px;">
                                    <button type="button" onclick="document.getElementById('editProductModal_<?= $p['id'] ?>').style.display = 'none'" class="btn btn-outline">إلغاء</button>
                                    <button type="submit" class="btn btn-primary">حفظ وتحديث المتجر</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- نافذة إضافة منتج جديد -->
<div id="newProductModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); backdrop-filter:blur(4px); z-index:1000; align-items:center; justify-content:center; padding:20px;">
    <div class="admin-card" style="max-width:650px; width:100%; border:1px solid var(--admin-primary); margin:0; max-height:90vh; overflow-y:auto;">
        <div class="card-header-row" style="margin-bottom:16px;">
            <h3>إضافة مورد جديد إلى سوق قيمة</h3>
            <button type="button" onclick="document.getElementById('newProductModal').style.display = 'none'" class="btn btn-outline btn-sm">&times;</button>
        </div>

        <form method="POST" action="<?= BASE_URL ?>/admin/products.php" style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <input type="hidden" name="action" value="create_product">

            <div class="form-group" style="grid-column:1/-1;">
                <label>اسم المورد / المادة *</label>
                <input type="text" name="title" class="input-field" required placeholder="مثال: حبيبات بولي بروبيلين معاد تدويرها (PP)">
            </div>

            <div class="form-group">
                <label>الرمز التعريفي الفريد (Handle) * (بالإنجليزية)</label>
                <input type="text" name="handle" dir="ltr" class="input-field" required placeholder="recycled-pp-granules">
            </div>

            <div class="form-group">
                <label>التصنيف *</label>
                <select name="category" class="input-field" required>
                    <?php foreach ($materials as $m): ?>
                        <option value="<?= e($m['slug']) ?>"><?= e($m['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>السعر (ر.س) *</label>
                <input type="number" step="0.01" name="price" class="input-field" required placeholder="2500.00">
            </div>

            <div class="form-group">
                <label>وحدة القياس</label>
                <input type="text" name="unit" class="input-field" value="طن">
            </div>

            <div class="form-group">
                <label>الموقع الجغرافي</label>
                <input type="text" name="location" class="input-field" value="الرياض، المملكة العربية السعودية">
            </div>

            <div class="form-group">
                <label>الكمية المتوفرة</label>
                <input type="text" name="quantity" class="input-field" placeholder="مثال: 50 طن شهرياً">
            </div>

            <div class="form-group" style="grid-column:1/-1;">
                <label>رابط الصورة (URL)</label>
                <input type="url" name="image_url" dir="ltr" class="input-field" value="https://images.unsplash.com/photo-1530587191325-3db32d826c18?auto=format&fit=crop&w=800&q=80">
            </div>

            <div class="form-group" style="grid-column:1/-1;">
                <label>الوصف والمواصفات</label>
                <textarea name="description" rows="3" class="input-field" placeholder="مواصفات المادة، النقاء، شهادات الفحص..."></textarea>
            </div>

            <div style="grid-column:1/-1; display:flex; justify-content:flex-end; gap:10px; margin-top:10px;">
                <button type="button" onclick="document.getElementById('newProductModal').style.display = 'none'" class="btn btn-outline">إلغاء</button>
                <button type="submit" class="btn btn-primary">نشر الآن في المتجر</button>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
