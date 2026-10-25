<?php
$currentAdminPage = 'db-manager';
$adminTitle = 'مدير قاعدة البيانات الرسومي (SQL Studio)';
require_once __DIR__ . '/includes/header.php';

// حماية استثنائية: محصور لـ owner فقط
if ($user['role'] !== 'owner') {
    echo '<div class="alert-box alert-error">عذراً، الوصول لمدير قاعدة البيانات محصور لمالك المنصة (Owner) فقط لأسباب أمنية.</div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$sqlQuery = trim($_POST['sql_query'] ?? '');
$activeTable = $_GET['table'] ?? '';
$sqlResult = null;
$sqlError = '';
$affectedRows = 0;
$executionTime = 0;

// قائمة الجداول
$tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

// حذف صف محدد في جدول
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_row') {
    $tbl = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['table_name']);
    $rowId = (int)$_POST['row_id'];
    try {
        $db->query("DELETE FROM `$tbl` WHERE id = $rowId");
        $sqlResult = "تم حذف السجل #$rowId بنجاح من جدول `$tbl`.";
    } catch (Exception $e) {
        $sqlError = $e->getMessage();
    }
}

// تنفيذ استعلام مخصص
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'run_sql' && !empty($sqlQuery)) {
    $startTime = microtime(true);
    try {
        // فحص إذا كان الاستعلام SELECT أم إجرائي
        if (stripos(ltrim($sqlQuery), 'SELECT') === 0 || stripos(ltrim($sqlQuery), 'SHOW') === 0 || stripos(ltrim($sqlQuery), 'DESCRIBE') === 0) {
            $stmt = $db->query($sqlQuery);
            $customRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);
        } else {
            $affectedRows = $db->exec($sqlQuery);
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);
            $sqlResult = "تم تنفيذ الاستعلام بنجاح. الأسطر المتأثرة: $affectedRows";
        }
    } catch (Exception $e) {
        $sqlError = $e->getMessage();
    }
}

// عرض بيانات الجدول المحدد
$tableData = [];
$tableColumns = [];
$tableTotalRows = 0;
if ($activeTable && in_array($activeTable, $tables)) {
    $tableColumns = $db->query("DESCRIBE `$activeTable`")->fetchAll();
    $tableTotalRows = (int)$db->query("SELECT COUNT(*) FROM `$activeTable`")->fetchColumn();
    $tableData = $db->query("SELECT * FROM `$activeTable` ORDER BY 1 DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="card-header-row" style="margin-bottom:20px;">
    <div>
        <h3>🗄️ مدير قاعدة بيانات المتجر (بديل phpMyAdmin المتكامل)</h3>
        <p style="font-size:12px; color:var(--admin-text-muted);">تحكم مباشر في جداول وبيانات MySQL مع إمكانية كتابة استعلامات مخصصة والتصدير الفوري.</p>
    </div>
    <a href="<?= BASE_URL ?>/admin/export-backup.php" class="btn btn-primary">
        💾 تحميل نسخة احتياطية كاملة (SQL Backup)
    </a>
</div>

<?php if ($sqlResult): ?><div class="alert-box alert-success"><?= e($sqlResult) ?> (<?= $executionTime ?> ms)</div><?php endif; ?>
<?php if ($sqlError): ?><div class="alert-box alert-error">خطأ في استعلام SQL: <?= e($sqlError) ?></div><?php endif; ?>

<!-- وحدة تنفيذ استعلامات SQL -->
<div class="admin-card">
    <h4 style="font-size:15px; margin-bottom:12px; color:white;">⚡ محاكي استعلامات SQL السريع</h4>
    <form method="POST" action="<?= BASE_URL ?>/admin/db-manager.php<?= $activeTable ? '?table=' . urlencode($activeTable) : '' ?>">
        <input type="hidden" name="action" value="run_sql">
        <textarea name="sql_query" rows="3" class="sql-editor" placeholder="اكتب استعلام SQL هنا (مثال: SELECT * FROM products WHERE price > 1000)"><?= e($sqlQuery) ?></textarea>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:12px;">
            <div style="display:flex; gap:8px;">
                <button type="button" class="btn btn-outline btn-sm" onclick="document.querySelector('.sql-editor').value = 'SELECT * FROM products LIMIT 10;'">عينة: المنتجات</button>
                <button type="button" class="btn btn-outline btn-sm" onclick="document.querySelector('.sql-editor').value = 'SELECT * FROM orders ORDER BY id DESC;'">عينة: الطلبات</button>
                <button type="button" class="btn btn-outline btn-sm" onclick="document.querySelector('.sql-editor').value = 'SELECT * FROM users WHERE role = \'owner\';'">عينة: الملاك</button>
            </div>
            <button type="submit" class="btn btn-primary">تشغيل الاستعلام ▶</button>
        </div>
    </form>

    <?php if (isset($customRows)): ?>
        <div style="margin-top:20px;">
            <h5 style="margin-bottom:10px; color:var(--admin-primary);">نتائج الاستعلام (<?= count($customRows) ?> صف - استغرق <?= $executionTime ?> ms):</h5>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <?php if (!empty($customRows)): ?>
                                <?php foreach (array_keys($customRows[0]) as $col): ?>
                                    <th><?= e($col) ?></th>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <th>النتيجة</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($customRows)): ?>
                            <tr><td>لا توجد بيانات مطابقة لهذا الاستعلام.</td></tr>
                        <?php else: ?>
                            <?php foreach ($customRows as $r): ?>
                                <tr>
                                    <?php foreach ($r as $val): ?>
                                        <td style="max-width:250px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                            <?= e($val ?? 'NULL') ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- مستعرض الجداول الرسومي -->
<div class="admin-card">
    <h4 style="font-size:15px; margin-bottom:14px; color:white;">📑 جداول قاعدة البيانات (قيمة MySQL):</h4>
    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:20px;">
        <?php foreach ($tables as $t): 
            $count = (int)$db->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
        ?>
            <a href="<?= BASE_URL ?>/admin/db-manager.php?table=<?= urlencode($t) ?>" class="db-tab-btn <?= $activeTable === $t ? 'active' : '' ?>">
                📁 <?= e($t) ?> <span style="opacity:0.7;">(<?= $count ?>)</span>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if ($activeTable && in_array($activeTable, $tables)): ?>
        <div class="card-header-row" style="background:var(--admin-surface); padding:14px 18px; border-radius:8px; border:1px solid var(--admin-border);">
            <div>
                <strong>الجدول المختار: <span style="color:var(--admin-primary); font-family:monospace;"><?= e($activeTable) ?></span></strong>
                <span style="font-size:12px; color:var(--admin-text-muted); margin-right:12px;">(إجمالي السجلات: <?= $tableTotalRows ?>)</span>
            </div>
            <div style="font-size:12px; color:var(--admin-text-muted);">
                يتم عرض آخر 50 سجلاً
            </div>
        </div>

        <div class="table-responsive" style="margin-top:14px;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>إجراءات</th>
                        <?php foreach ($tableColumns as $col): ?>
                            <th><?= e($col['Field']) ?> <small style="font-weight:normal; opacity:0.6; font-size:10px;">(<?= e($col['Type']) ?>)</small></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tableData)): ?>
                        <tr><td colspan="<?= count($tableColumns) + 1 ?>" style="text-align:center; padding:30px; color:var(--admin-text-muted);">هذا الجدول فارغ حالياً.</td></tr>
                    <?php else: ?>
                        <?php foreach ($tableData as $row): ?>
                            <tr>
                                <td>
                                    <?php if (isset($row['id'])): ?>
                                        <form method="POST" action="<?= BASE_URL ?>/admin/db-manager.php?table=<?= urlencode($activeTable) ?>" onsubmit="return confirm('هل تريد حذف هذا السجل نهائياً؟');">
                                            <input type="hidden" name="action" value="delete_row">
                                            <input type="hidden" name="table_name" value="<?= e($activeTable) ?>">
                                            <input type="hidden" name="row_id" value="<?= (int)$row['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" style="padding:2px 6px;">حذف</button>
                                        </form>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <?php foreach ($tableColumns as $col): 
                                    $val = $row[$col['Field']] ?? null;
                                ?>
                                    <td style="max-width:240px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?= e((string)$val) ?>">
                                        <?php if ($val === null): ?>
                                            <span style="opacity:0.4; font-style:italic;">NULL</span>
                                        <?php else: ?>
                                            <?= e(mb_strimwidth((string)$val, 0, 45, '...')) ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p style="color:var(--admin-text-muted); font-size:13px; text-align:center; padding:20px;">
            👈 اختر أحد الجداول من الأعلى لتصفح سجلاته والتحكم فيها مباشرة.
        </p>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
