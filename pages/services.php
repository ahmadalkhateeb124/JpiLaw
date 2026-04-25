<?php
// Pull services from CMS
$_services = [];
if (isset($pdo) && $pdo instanceof PDO) {
    $_stmt = $pdo->query("SELECT slug, title_ar, short_desc_ar, content_ar, icon, image FROM services WHERE is_active = 1 ORDER BY sort_order, id");
    $_services = $_stmt ? $_stmt->fetchAll() : [];
}
?>

<!-- Page Title -->
<div class="page-title-area page-title-area-three title-img-one">
    <div class="d-table">
        <div class="d-table-cell">
            <div class="page-title-text">
                <h1 class="text-light">
                    خدمات قانونية متخصّصة في الأردن وفلسطين
                </h1>
                <ul>
                    <li><a href="<?= htmlspecialchars($base_url) ?>">الصفحة الرئيسية</a></li>
                    <li><i class="icofont-simple-left"></i></li>
                    <li>الخدمات القانونية</li>
                </ul>
                <div class="page-title-btn">
                    <a href="<?= htmlspecialchars($base_url) ?>appointment" aria-label="احجز استشارة قانونية">احجز استشارة
                        <i class="icofont-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Page Title -->

<style>
.jpi-services-intro { padding: 60px 0 30px; background: #fff; }
.jpi-services-intro .lead { max-width: 920px; margin: 0 auto; text-align: center; color: #444; line-height: 2; font-size: 16px; }
.jpi-services-intro .lead strong { color: #1a1a1a; }
.jpi-services-grid { padding: 50px 0 80px; background: #fafafa; }
.jpi-services-grid .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(290px, 1fr)); gap: 22px; }
.jpi-svc-card { background: #fff; border: 1px solid #e8e8e8; border-radius: 12px; padding: 28px 24px; transition: transform .25s, box-shadow .25s, border-color .25s; }
.jpi-svc-card:hover { transform: translateY(-4px); border-color: #66573e; box-shadow: 0 12px 28px rgba(0,0,0,0.08); }
.jpi-svc-card .icon { width: 52px; height: 52px; border-radius: 12px; background: #1a1a1a; color: #f4f1ec; display: inline-flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 18px; }
.jpi-svc-card h2 { font-size: 19px; font-weight: 700; color: #1a1a1a; margin-bottom: 10px; line-height: 1.4; }
.jpi-svc-card p { color: #555; line-height: 1.8; font-size: 14.5px; margin-bottom: 14px; }
.jpi-svc-card .more { color: #66573e; font-weight: 700; font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
.jpi-svc-card .more:hover { color: #1a1a1a; }
.jpi-services-cta { background: linear-gradient(135deg, #1a1a1a, #000); color: #fff; padding: 50px 40px; border-radius: 16px; text-align: center; max-width: 1000px; margin: 50px auto 0; }
.jpi-services-cta h2 { color: #f4f1ec; font-size: 28px; margin-bottom: 12px; font-weight: 700; }
.jpi-services-cta p { color: rgba(255,255,255,0.75); max-width: 640px; margin: 0 auto 24px; line-height: 1.8; }
.jpi-services-cta .btns { display: inline-flex; gap: 12px; flex-wrap: wrap; justify-content: center; }
.jpi-services-cta .btns a { padding: 13px 28px; border-radius: 8px; font-weight: 700; text-decoration: none; transition: all .2s; }
.jpi-services-cta .btns .primary { background: #66573e; color: #fff; }
.jpi-services-cta .btns .primary:hover { background: #fff; color: #1a1a1a; transform: translateY(-2px); }
.jpi-services-cta .btns .ghost { background: transparent; color: #fff; border: 1px solid rgba(255,255,255,0.3); }
.jpi-services-cta .btns .ghost:hover { background: rgba(255,255,255,0.08); }
@media (max-width: 768px){.jpi-services-cta{padding: 36px 24px;}.jpi-services-cta h2{font-size:22px;}}
</style>

<section class="jpi-services-intro">
    <div class="container">
        <p class="lead">
            في <strong>مكتب JPI للمحاماة والاستشارات القانونية</strong>، نقدّم منظومة متكاملة من الخدمات القانونيّة للأفراد والشركات في
            <strong>الأردن وفلسطين</strong>.
            فريقنا من <strong>المحامين المعتمدين</strong> يجمع بين الخبرة المحلّية والفهم العميق للقانون التجاري الدولي،
            ليقدّم لك حلولاً قانونيّة دقيقة ومناسبة لطبيعة قضيّتك.
            سواء كنت بحاجة إلى <a href="<?= htmlspecialchars($base_url) ?>practice">استشارة في مجال محدّد</a> أو
            <a href="<?= htmlspecialchars($base_url) ?>appointment">حجز موعد مع محامٍ متخصّص</a>،
            نحن جاهزون لمرافقتك في كلّ خطوة.
        </p>
    </div>
</section>

<section class="jpi-services-grid">
    <div class="container">
        <div class="grid">
            <?php foreach ($_services as $s): ?>
                <article class="jpi-svc-card">
                    <?php if (!empty($s['icon'])): ?>
                        <div class="icon"><i class="<?= htmlspecialchars($s['icon']) ?>" aria-hidden="true"></i></div>
                    <?php else: ?>
                        <div class="icon"><i class="fa-solid fa-scale-balanced" aria-hidden="true"></i></div>
                    <?php endif; ?>
                    <h2><?= htmlspecialchars($s['title_ar']) ?></h2>
                    <?php if (!empty($s['short_desc_ar'])): ?>
                        <p><?= htmlspecialchars($s['short_desc_ar']) ?></p>
                    <?php endif; ?>
                    <a href="<?= htmlspecialchars($base_url) ?>contact?service=<?= urlencode($s['slug']) ?>" class="more">
                        احجز استشارة <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="jpi-services-cta">
            <h2>هل تحتاج خدمة قانونية مخصّصة؟</h2>
            <p>
                لا تتردّد في التواصل معنا. أوّل استشارة لتقييم قضيّتك مجّانية، وردّنا خلال 24 ساعة.
                نخدم العملاء في عمّان، الزرقاء، إربد، رام الله، نابلس، الخليل، وبقيّة محافظات الأردن وفلسطين.
            </p>
            <div class="btns">
                <a href="<?= htmlspecialchars($base_url) ?>appointment" class="primary">احجز موعد</a>
                <a href="<?= htmlspecialchars($base_url) ?>contact" class="ghost">تواصل معنا</a>
            </div>
        </div>
    </div>
</section>
