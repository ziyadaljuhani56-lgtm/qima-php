// ملف التفاعلات العامة للواجهة
document.addEventListener('DOMContentLoaded', () => {
    // تبديل القائمة في الشاشات الصغيرة
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNav = document.querySelector('.main-nav');
    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', () => {
            mainNav.classList.toggle('open');
        });
    }

    // إظهار وإخفاء نافذة استعادة كلمة المرور
    const forgotBtn = document.getElementById('open-forgot-modal');
    const forgotModal = document.getElementById('forgot-modal');
    const closeForgotBtn = document.getElementById('close-forgot-modal');

    if (forgotBtn && forgotModal) {
        forgotBtn.addEventListener('click', (e) => {
            e.preventDefault();
            forgotModal.classList.add('open');
        });
    }

    if (closeForgotBtn && forgotModal) {
        closeForgotBtn.addEventListener('click', () => {
            forgotModal.classList.remove('open');
        });
    }

    if (forgotModal) {
        forgotModal.addEventListener('click', (e) => {
            if (e.target === forgotModal) {
                forgotModal.classList.remove('open');
            }
        });
    }

    // إظهار/إخفاء كلمة المرور
    document.querySelectorAll('.pw-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.parentElement.querySelector('input');
            if (input) {
                input.type = input.type === 'password' ? 'text' : 'password';
            }
        });
    });
});
