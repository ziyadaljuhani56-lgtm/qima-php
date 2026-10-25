<?php
$currentAdminPage = 'overview';
$adminTitle = 'لوحة الإحصائيات العامة والتحليلات';
require_once __DIR__ . '/includes/header.php';

// استعلامات الإحصائيات الفعلية
$totalRevenue = (float)$db->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE payment_status = 'paid'")->fetchColumn();
$totalOrders = (int)$db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalProducts = (int)$db->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalCustomers = (int)$db->query("SELECT COUNT(*) FROM users WHERE role IN ('member', 'user')")->fetchColumn();
$totalInquiries = (int)$db->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();

// آخر 5 طلبات
$recentOrders = $db->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5")->fetchAll();
// آخر 5 استفسارات
$recentInquiries = $db->query("SELECT * FROM inquiries ORDER BY id DESC LIMIT 5")->fetchAll();
// إحصائية توزيع الموارد حسب التصنيف
$categoryStats = $db->query("SELECT category, COUNT(*) as count FROM products GROUP BY category")->fetchAll();
?>

<!-- بطاقات الـ KPIs الرئيسية -->
<div class="admin-grid-4">
    <div class="kpi-card">
        <div class="kpi-head">
            <span class="kpi-title">إجمالي المبيعات المحصلة</span>
            <div class="kpi-icon">💰</div>
        </div>
        <div class="kpi-value"><?= number_format($totalRevenue, 2) ?> <small style="font-size:14px; font-weight:normal; color:var(--admin-primary);">ر.س</small></div>
        <div class="kpi-sub">
            <span class="kpi-badge">محصل فعلياً</span>
            <span>من الطلبات المكتملة</span>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-head">
            <span class="kpi-title">إجمالي الطلبات المسجلة</span>
            <div class="kpi-icon">📦</div>
        </div>
        <div class="kpi-value"><?= $totalOrders ?></div>
        <div class="kpi-sub">
            <span>تشمل المكتملة، الجارية، والجديدة</span>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-head">
            <span class="kpi-title">عروض سوق الموارد</span>
            <div class="kpi-icon">🏷️</div>
        </div>
        <div class="kpi-value"><?= $totalProducts ?></div>
        <div class="kpi-sub">
            <span class="kpi-badge">نشطة في المتجر</span>
            <span>بلاستيك، كرتون، معادن...</span>
        </div>
    </div>

    <div class="kpi-card">
        <div class="kpi-head">
            <span class="kpi-title">العملاء والشركاء المسجلين</span>
            <div class="kpi-icon">👥</div>
        </div>
        <div class="kpi-value"><?= $totalCustomers ?></div>
        <div class="kpi-sub">
            <span>منشآت ومصانع وأفراد</span>
        </div>
    </div>
</div>

<!-- الرسوم البيانية التفاعلية -->
<div style="display:grid; grid-template-columns: 2fr 1fr; gap:20px; margin-bottom:28px;">
    <div class="admin-card">
        <div class="card-header-row">
            <h3>📈 النشاط المالي وحجم التعاملات (آخر الطلبات)</h3>
        </div>
        <div style="height: 250px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <div class="admin-card">
        <div class="card-header-row">
            <h3>♻️ توزيع الموارد في المتجر</h3>
        </div>
        <div style="height: 250px; display:flex; justify-content:center; align-items:center;">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>
</div>

<!-- الجداول السريعة (آخر الطلبات والاستفسارات) -->
<div style="display:grid; grid-template-columns: 1.2fr 1fr; gap:20px;">
    <!-- آخر الطلبات -->
    <div class="admin-card">
        <div class="card-header-row">
            <h3>📦 أحدث طلبات الموارد</h3>
            <a href="<?= BASE_URL ?>/admin/orders.php" class="btn btn-outline btn-sm">عرض كل الطلبات &larr;</a>
        </div>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>رقم الطلب</th>
                        <th>العميل</th>
                        <th>المبلغ</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentOrders)): ?>
                        <tr><td colspan="4" style="text-align:center; color:var(--admin-text-muted);">لا توجد طلبات حتى الآن.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentOrders as $ord): ?>
                            <tr>
                                <td>
                                    <strong style="color:var(--admin-primary);"><?= e($ord['order_number']) ?></strong><br>
                                    <small style="color:var(--admin-text-muted); font-size:10px;"><?= e(substr($ord['created_at'], 0, 10)) ?></small>
                                </td>
                                <td><?= e($ord['customer_name']) ?></td>
                                <td><?= number_format($ord['total_amount'], 2) ?> ر.س</td>
                                <td>
                                    <span class="status-pill <?= e($ord['order_status']) ?>">
                                        <?= e($ord['order_status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- آخر الاستفسارات -->
    <div class="admin-card">
        <div class="card-header-row">
            <h3>💬 أحدث استفسارات المنشآت</h3>
            <a href="<?= BASE_URL ?>/admin/inquiries.php" class="btn btn-outline btn-sm">عرض الكل &larr;</a>
        </div>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>المرسل</th>
                        <th>الموضوع</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentInquiries)): ?>
                        <tr><td colspan="3" style="text-align:center; color:var(--admin-text-muted);">لا توجد استفسارات.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentInquiries as $inq): ?>
                            <tr>
                                <td>
                                    <strong><?= e($inq['name']) ?></strong><br>
                                    <small style="color:var(--admin-text-muted);"><?= e($inq['organization'] ?: 'جهة غير محددة') ?></small>
                                </td>
                                <td style="max-width:180px;"><?= e($inq['subject']) ?></td>
                                <td>
                                    <span class="status-pill <?= e($inq['status']) ?>">
                                        <?= e($inq['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // رسم بياني للتعاملات المالية
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revCtx, {
        type: 'line',
        data: {
            labels: ['الأسبوع 1', 'الأسبوع 2', 'الأسبوع 3', 'الأسبوع الحالي'],
            datasets: [{
                label: 'حجم التعاملات (ر.س)',
                data: [3500, 7800, 12400, <?= (int)$totalRevenue ?>],
                borderColor: '#c2ec89',
                backgroundColor: 'rgba(194, 236, 137, 0.1)',
                tension: 0.3,
                fill: true,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { color: 'rgba(255,255,255,0.06)' }, ticks: { color: '#95a89b' } },
                x: { grid: { display: false }, ticks: { color: '#95a89b' } }
            }
        }
    });

    // رسم بياني لتوزيع المواد
    const catCtx = document.getElementById('categoryChart').getContext('2d');
    const catLabels = <?= json_encode(array_column($categoryStats, 'category')) ?>;
    const catCounts = <?= json_encode(array_column($categoryStats, 'count')) ?>;
    new Chart(catCtx, {
        type: 'doughnut',
        data: {
            labels: catLabels.length ? catLabels : ['بلاستيك', 'معادن', 'ورق', 'إطارات'],
            datasets: [{
                data: catCounts.length ? catCounts : [3, 2, 2, 1],
                backgroundColor: ['#c2ec89', '#10b981', '#3b82f6', '#f59e0b', '#8b5cf6'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { color: '#e6f0e8', boxWidth: 12 } }
            }
        }
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
