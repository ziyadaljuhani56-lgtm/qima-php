<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config.php';
}
?>
    <section class="newsletter">
        <div class="rail newsletter-inner">
            <div>
                <span class="eyebrow">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    ابقَ على اتصال بالسوق
                </span>
                <h2>فرص جديدة، في بريدك.</h2>
                <p>سجّل اهتمامك لتصلك تحديثات الموارد والعروض ومعلومات السوق عند توفرها.</p>
            </div>
            <div>
                <form action="<?= BASE_URL ?>/subscribe.php" method="POST" class="newsletter-form">
                    <div class="email-row">
                        <input name="email" type="email" dir="ltr" placeholder="name@company.com" required maxlength="191">
                        <button type="submit" class="button button-dark">سجّل اهتمامك</button>
                    </div>
                    <label class="consent">
                        <input name="consent" type="checkbox" required>
                        أوافق على استقبال التحديثات وفق <a href="<?= BASE_URL ?>/privacy.php">سياسة الخصوصية</a>.
                    </label>
                </form>
            </div>
        </div>
    </section>

    <footer class="site-footer">
        <div class="footer-main rail">
            <div>
                <a class="footer-brand" href="<?= BASE_URL ?>/">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7 19H4.815a1.83 1.83 0 0 1-1.57-.881 1.785 1.785 0 0 1-.004-1.784L7.196 9.5"/><path d="M11 19h8.2a1.8 1.8 0 0 0 1.5-1 1.8 1.8 0 0 0 0-2l-4.7-8.2"/><path d="M11 5h2"/><path d="m7 19-3-3 3-3"/><path d="m11 19 3 3 3-3"/><path d="m13 5 3 3-3 3"/></svg>
                    <strong>قيمة.</strong>
                </a>
                <p>نربط الموارد بالفرص، ونحوّل الاستدامة<br>إلى قيمة مشتركة للأعمال والبيئة.</p>
                <span class="small-label">المملكة العربية السعودية</span>
            </div>
            <div>
                <h3>اكتشف قيمة</h3>
                <a href="<?= BASE_URL ?>/market.php">سوق الموارد</a>
                <a href="<?= BASE_URL ?>/services.php">خدمات المنصة</a>
                <a href="<?= BASE_URL ?>/about.php">عن قيمة</a>
            </div>
            <div>
                <h3>المعرفة والأثر</h3>
                <a href="<?= BASE_URL ?>/insights.php">مؤشرات السوق</a>
                <a href="<?= BASE_URL ?>/services.php?slug=esg">تقارير الأثر وESG</a>
                <a href="<?= BASE_URL ?>/privacy.php">الخصوصية</a>
                <a href="<?= BASE_URL ?>/terms.php">الشروط والأحكام</a>
            </div>
            <div>
                <h3>لنصنع قيمة معًا</h3>
                <p>مورد، مصنع أو جهة تبحث عن فرصة؟</p>
                <a class="footer-contact" href="<?= BASE_URL ?>/contact.php">
                    تواصل مع فريق قيمة
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 7 10 10"/><path d="M17 7v10H7"/></svg>
                </a>
            </div>
        </div>
        <div class="footer-bottom rail">
            <span>© <?= date('Y') ?> قيمة. جميع الحقوق محفوظة.</span>
            <span>كل مورد... له قيمة</span>
            <span>كفاءة الموارد تبدأ بالاتصال الصحيح.</span>
        </div>
    </footer>

    <script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
