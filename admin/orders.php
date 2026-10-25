<?php
$currentAdminPage = 'orders';
$adminTitle = 'إدارة الطلبات والمبيعات';
require_once __DIR__ . '/includes/header.php';

$msg = '';
$err = '';

// تحديث حالة الطلب والدفع
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_order') {
    $orderId = (int)$_POST['order_id'];
    $orderStatus = $_POST['order_status'];
    $paymentStatus = $_POST['payment_status'];
    $notes = trim($_POST['notes'] ?? '');

    try {
        $uStmt = $db->prepare("UPDATE orders SET order_status = ?, payment_status = ?, notes = ? WHERE id = ?");
        $uStmt->execute([$orderStatus, $paymentStatus, $notes, $orderId]);
        $msg = "تم تحديث بيانات الطلب #$orderId بنجاح.";
    } catch (Exception $e) {
        $err = "حدث خطأ أثناء التحديث: " . $e->getMessage();
    }
}

// حذف طلب
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_order') {
    $orderId = (int)$_POST['order_id'];
    try {
        $db->prepare("DELETE FROM orders WHERE id = ?")->execute([$orderId]);
        $msg = "تم حذف الطلب بنجاح.";
    } catch (Exception $e) {
        $err = "تعذر حذف الطلب: " . $e->getMessage();
    }
}

// جلب كافة الطلبات مع العناصر المرتبطة
$statusFilter = $_GET['status'] ?? '';
$query = "SELECT * FROM orders";
$params = [];
if ($statusFilter) {
    $query .= " WHERE order_status = ?";
    $params[] = $statusFilter;
}
$query .= " ORDER BY id DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>

<?php if ($msg): ?><div class="alert-box alert-success"><?= e($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert-box alert-error"><?= e($err) ?></div><?php endif; ?>

<div class="admin-card">
    <div class="card-header-row">
        <div>
            <h3>قائمة الطلبات ومبيعات المتجر (<?= count($orders) ?>)</h3>
            <p style="font-size:12px; color:var(--admin-text-muted); margin-top:4px;">متابعة عمليات الشراء، الحالات اللوجستية، وحالة سداد الفواتير.</p>
        </div>
        <div style="display:flex; gap:10px; align-items:center;">
            <a href="<?= BASE_URL ?>/admin/orders.php" class="btn btn-outline btn-sm <?= empty($statusFilter) ? 'btn-primary' : '' ?>">الكل</a>
            <a href="<?= BASE_URL ?>/admin/orders.php?status=new" class="btn btn-outline btn-sm <?= $statusFilter === 'new' ? 'btn-primary' : '' ?>">جديدة</a>
            <a href="<?= BASE_URL ?>/admin/orders.php?status=processing" class="btn btn-outline btn-sm <?= $statusFilter === 'processing' ? 'btn-primary' : '' ?>">قيد المعالجة</a>
            <a href="<?= BASE_URL ?>/admin/orders.php?status=completed" class="btn btn-outline btn-sm <?= $statusFilter === 'completed' ? 'btn-primary' : '' ?>">مكتملة</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>رقم الطلب</th>
                    <th>العميل / المنشأة</th>
                    <th>الاتصال</th>
                    <th>إجمالي المبلغ</th>
                    <th>حالة الدفع</th>
                    <th>حالة الطلب</th>
                    <th>تاريخ الإنشاء</th>
                    <th>الإجراءات والتحكم</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="8" style="text-align:center; padding:30px; color:var(--admin-text-muted);">لا توجد طلبات مطابقة.</td></tr>
                <?php else: ?>
                    <?php foreach ($orders as $ord): 
                        // جلب منتجات الطلب
                        $itemsStmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
                        $itemsStmt->execute([$ord['id']]);
                        $items = $itemsStmt->fetchAll();
                    ?>
                        <tr>
                            <td>
                                <strong style="color:var(--admin-primary);"><?= e($ord['order_number']) ?></strong>
                            </td>
                            <td>
                                <strong><?= e($ord['customer_name']) ?></strong>
                            </td>
                            <td>
                                <span dir="ltr" style="font-size:12px;"><?= e($ord['customer_phone']) ?></span><br>
                                <small dir="ltr" style="color:var(--admin-text-muted); font-size:11px;"><?= e($ord['customer_email']) ?></small>
                            </td>
                            <td>
                                <strong style="color:white;"><?= number_format($ord['total_amount'], 2) ?></strong> ر.س
                            </td>
                            <td>
                                <span class="status-pill <?= e($ord['payment_status']) ?>">
                                    <?= $ord['payment_status'] === 'paid' ? 'مدفوع' : ($ord['payment_status'] === 'refunded' ? 'مسترجع' : 'غير مدفوع') ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-pill <?= e($ord['order_status']) ?>">
                                    <?= $ord['order_status'] === 'completed' ? 'مكتمل' : ($ord['order_status'] === 'processing' ? 'قيد التنسيق' : ($ord['order_status'] === 'cancelled' ? 'ملغي' : 'جديد')) ?>
                                </span>
                            </td>
                            <td style="font-size:11px; color:var(--admin-text-muted);">
                                <?= e(substr($ord['created_at'], 0, 16)) ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('editOrderModal_<?= $ord['id'] ?>').style.display = 'flex'">
                                    تعديل الحالة ⚙️
                                </button>
                            </td>
                        </tr>

                        <!-- نافذة تعديل الطلب -->
                        <div id="editOrderModal_<?= $ord['id'] ?>" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); backdrop-filter:blur(4px); z-index:1000; align-items:center; justify-content:center; padding:20px;">
                            <div class="admin-card" style="max-width:540px; width:100%; border:1px solid var(--admin-primary); margin:0;">
                                <div class="card-header-row" style="margin-bottom:16px;">
                                    <h3>تحديث الطلب: <?= e($ord['order_number']) ?></h3>
                                    <button type="button" onclick="document.getElementById('editOrderModal_<?= $ord['id'] ?>').style.display = 'none'" class="btn btn-outline btn-sm">&times;</button>
                                </div>

                                <div style="background:var(--admin-surface); padding:14px; border-radius:8px; margin-bottom:16px; font-size:12px;">
                                    <strong>محتويات الطلب:</strong>
                                    <ul style="margin:8px 0 0 16px; color:var(--admin-text-muted);">
                                        <?php foreach ($items as $it): ?>
                                            <li><?= e($it['product_title']) ?> &times; <?= $it['quantity'] ?> (<?= number_format($it['total_price'], 2) ?> ر.س)</li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>

                                <form method="POST" action="<?= BASE_URL ?>/admin/orders.php">
                                    <input type="hidden" name="action" value="update_order">
                                    <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">

                                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:14px;">
                                        <div class="form-group">
                                            <label>حالة الطلب</label>
                                            <select name="order_status" class="input-field">
                                                <option value="new" <?= $ord['order_status'] === 'new' ? 'selected' : '' ?>>جديد (New)</option>
                                                <option value="processing" <?= $ord['order_status'] === 'processing' ? 'selected' : '' ?>>قيد التنسيق والنقل (Processing)</option>
                                                <option value="completed" <?= $ord['order_status'] === 'completed' ? 'selected' : '' ?>>مكتمل وتم الاستلام (Completed)</option>
                                                <option value="cancelled" <?= $ord['order_status'] === 'cancelled' ? 'selected' : '' ?>>ملغي (Cancelled)</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label>حالة الدفع</label>
                                            <select name="payment_status" class="input-field">
                                                <option value="unpaid" <?= $ord['payment_status'] === 'unpaid' ? 'selected' : '' ?>>غير مدفوع (Unpaid)</option>
                                                <option value="paid" <?= $ord['payment_status'] === 'paid' ? 'selected' : '' ?>>تم الدفع والتسوية (Paid)</option>
                                                <option value="refunded" <?= $ord['payment_status'] === 'refunded' ? 'selected' : '' ?>>مسترجع (Refunded)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group" style="margin-bottom:18px;">
                                        <label>ملاحظات داخلية حول التوصيل والتنسيق</label>
                                        <textarea name="notes" rows="3" class="input-field" placeholder="مثال: تم إرسال الشاحنة، مستند الاستلام جاهز..."><?= e($ord['notes']) ?></textarea>
                                    </div>

                                    <div style="display:flex; justify-content:space-between; align-items:center;">
                                        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                                        <button type="button" onclick="if(confirm('هل أنت متأكد من حذف هذا الطلب نهائياً؟')) { document.getElementById('delOrder_<?= $ord['id'] ?>').submit(); }" class="btn btn-danger btn-sm">حذف الطلب</button>
                                    </div>
                                </form>

                                <form id="delOrder_<?= $ord['id'] ?>" method="POST" action="<?= BASE_URL ?>/admin/orders.php" style="display:none;">
                                    <input type="hidden" name="action" value="delete_order">
                                    <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
