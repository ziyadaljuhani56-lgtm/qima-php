<?php
$currentAdminPage = 'inquiries';
$adminTitle = 'إدارة الاستفسارات وتوريد الموارد';
require_once __DIR__ . '/includes/header.php';

$msg = '';
$err = '';

// تحديث حالة الاستفسار
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_inquiry') {
    $inquiryId = (int)$_POST['inquiry_id'];
    $status = $_POST['status'];

    try {
        $stmt = $db->prepare("UPDATE inquiries SET status = ? WHERE id = ?");
        $stmt->execute([$status, $inquiryId]);
        $msg = 'تم تحديث حالة الاستفسار بنجاح.';
    } catch (Exception $e) {
        $err = 'فشل التحديث: ' . $e->getMessage();
    }
}

// حذف استفسار
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_inquiry') {
    $inquiryId = (int)$_POST['inquiry_id'];
    try {
        $db->prepare("DELETE FROM inquiries WHERE id = ?")->execute([$inquiryId]);
        $msg = 'تم حذف الاستفسار.';
    } catch (Exception $e) {
        $err = 'تعذر الحذف.';
    }
}

$statusFilter = $_GET['status'] ?? '';
$query = "SELECT * FROM inquiries";
$params = [];
if ($statusFilter) {
    $query .= " WHERE status = ?";
    $params[] = $statusFilter;
}
$query .= " ORDER BY id DESC";

$stmt = $db->prepare($query);
$stmt->execute($params);
$inquiries = $stmt->fetchAll();
?>

<?php if ($msg): ?><div class="alert-box alert-success"><?= e($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert-box alert-error"><?= e($err) ?></div><?php endif; ?>

<div class="admin-card">
    <div class="card-header-row">
        <div>
            <h3>طلبات واستفسارات المنشآت والمصانع (<?= count($inquiries) ?>)</h3>
            <p style="font-size:12px; color:var(--admin-text-muted); margin-top:4px;">استفسارات تداول الموارد، عروض التوريد، وحسابات تقارير ESG.</p>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="<?= BASE_URL ?>/admin/inquiries.php" class="btn btn-outline btn-sm <?= empty($statusFilter) ? 'btn-primary' : '' ?>">الكل</a>
            <a href="<?= BASE_URL ?>/admin/inquiries.php?status=new" class="btn btn-outline btn-sm <?= $statusFilter === 'new' ? 'btn-primary' : '' ?>">جديدة</a>
            <a href="<?= BASE_URL ?>/admin/inquiries.php?status=reviewed" class="btn btn-outline btn-sm <?= $statusFilter === 'reviewed' ? 'btn-primary' : '' ?>">تمت المراجعة</a>
            <a href="<?= BASE_URL ?>/admin/inquiries.php?status=contacted" class="btn btn-outline btn-sm <?= $statusFilter === 'contacted' ? 'btn-primary' : '' ?>">تم التواصل</a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>المرسل / المنشأة</th>
                    <th>معلومات الاتصال</th>
                    <th>موضوع الطلب</th>
                    <th>التفاصيل والمواصفات</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($inquiries)): ?>
                    <tr><td colspan="8" style="text-align:center; padding:30px; color:var(--admin-text-muted);">لا توجد استفسارات.</td></tr>
                <?php else: ?>
                    <?php foreach ($inquiries as $inq): ?>
                        <tr>
                            <td><?= $inq['id'] ?></td>
                            <td>
                                <strong><?= e($inq['name']) ?></strong><br>
                                <small style="color:var(--admin-text-muted);"><?= e($inq['organization'] ?: 'جهة فردية') ?></small>
                            </td>
                            <td>
                                <span dir="ltr" style="font-size:12px;"><?= e($inq['email']) ?></span><br>
                                <small dir="ltr" style="color:var(--admin-primary); font-size:11px;"><?= e($inq['phone'] ?: '-') ?></small>
                            </td>
                            <td>
                                <strong style="color:white;"><?= e($inq['subject']) ?></strong>
                            </td>
                            <td style="max-width:300px; font-size:12px; line-height:1.6; color:var(--admin-text-muted);">
                                <?= nl2br(e($inq['details'])) ?>
                            </td>
                            <td>
                                <form method="POST" action="<?= BASE_URL ?>/admin/inquiries.php" style="display:inline-flex; gap:6px;">
                                    <input type="hidden" name="action" value="update_inquiry">
                                    <input type="hidden" name="inquiry_id" value="<?= $inq['id'] ?>">
                                    <select name="status" class="input-field" style="padding:4px 8px; font-size:11px; width:auto;" onchange="this.form.submit()">
                                        <option value="new" <?= $inq['status'] === 'new' ? 'selected' : '' ?>>جديد (New)</option>
                                        <option value="reviewed" <?= $inq['status'] === 'reviewed' ? 'selected' : '' ?>>تمت المراجعة (Reviewed)</option>
                                        <option value="contacted" <?= $inq['status'] === 'contacted' ? 'selected' : '' ?>>تم التواصل (Contacted)</option>
                                        <option value="closed" <?= $inq['status'] === 'closed' ? 'selected' : '' ?>>مغلق (Closed)</option>
                                    </select>
                                </form>
                            </td>
                            <td style="font-size:11px; color:var(--admin-text-muted);">
                                <?= e(substr($inq['created_at'], 0, 10)) ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm" onclick="if(confirm('هل أنت متأكد من حذف هذا الاستفسار؟')) { document.getElementById('delInq_<?= $inq['id'] ?>').submit(); }">حذف</button>
                                <form id="delInq_<?= $inq['id'] ?>" method="POST" action="<?= BASE_URL ?>/admin/inquiries.php" style="display:none;">
                                    <input type="hidden" name="action" value="delete_inquiry">
                                    <input type="hidden" name="inquiry_id" value="<?= $inq['id'] ?>">
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
