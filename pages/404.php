<!-- 404 -->
<section class="error-area">
    <div class="error-item">
        <div class="d-table">
            <div class="d-table-cell">
                <div class="error-text">
                    <h1>404!</h1>
                    <p>عذراً! الصفحة غير موجودة</p>
                    <span>أوه! الصفحة التي تبحث عنها غير موجودة. قد تكون تم نقلها أو حذفها.</span>
                    <a href="<?= $base_url ?>Home" class="nav-link <?= $current_page == 'Home' || $current_page == '' ? 'active' : '' ?>">العودة إلى الصفحة الرئيسية</a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- نهاية 404 -->