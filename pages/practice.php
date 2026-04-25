<?php
// Pull practice areas from CMS
$_areas = [];
if (isset($pdo) && $pdo instanceof PDO) {
    $_stmt = $pdo->query("SELECT slug, title_ar, short_desc_ar, content_ar, icon, image FROM practice_areas WHERE is_active = 1 ORDER BY sort_order, id");
    $_areas = $_stmt ? $_stmt->fetchAll() : [];
}
?>

<!-- Page Title -->
<div class="page-title-area page-title-area-three title-img-one">
    <div class="d-table">
        <div class="d-table-cell">
            <div class="page-title-text">
                <h1 class="text-light">
                    مجالات الممارسة القانونية في الأردن وفلسطين
                </h1>
                <ul>
                    <li><a href="<?= htmlspecialchars($base_url) ?>">الصفحة الرئيسية</a></li>
                    <li><i class="icofont-simple-left"></i></li>
                    <li>مجالات الممارسة</li>
                </ul>
                <div class="page-title-btn">
                    <a href="<?= htmlspecialchars($base_url) ?>appointment" aria-label="احجز استشارة قانونية">احجز موعداً
                        <i class="icofont-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Page Title -->

<style>
.jpi-practice-intro { padding: 60px 0 30px; background: #fff; }
.jpi-practice-intro .lead { max-width: 920px; margin: 0 auto; text-align: center; color: #444; line-height: 2; font-size: 16px; }
.jpi-practice-intro .lead strong { color: #1a1a1a; }
.jpi-practice-grid { padding: 50px 0 80px; background: #fafafa; }
.jpi-practice-grid .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 22px; }
.jpi-area-card { background: #fff; border: 1px solid #e8e8e8; border-radius: 12px; padding: 28px 24px; transition: all .25s; position: relative; overflow: hidden; }
.jpi-area-card::before { content: ''; position: absolute; top: 0; inset-inline-end: 0; width: 80px; height: 80px; background: radial-gradient(circle, rgba(102,87,62,0.10), transparent 70%); pointer-events: none; }
.jpi-area-card:hover { transform: translateY(-4px); border-color: #66573e; box-shadow: 0 12px 28px rgba(0,0,0,0.08); }
.jpi-area-card .icon { width: 56px; height: 56px; border-radius: 14px; background: linear-gradient(135deg, #1a1a1a, #2a2a2a); color: #f4f1ec; display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 18px; position: relative; }
.jpi-area-card h2 { font-size: 20px; font-weight: 700; color: #1a1a1a; margin-bottom: 12px; line-height: 1.4; }
.jpi-area-card p { color: #555; line-height: 1.85; font-size: 14.5px; }
.jpi-area-card .more { color: #66573e; font-weight: 700; font-size: 13px; text-decoration: none; margin-top: 14px; display: inline-flex; align-items: center; gap: 6px; }
.jpi-area-card .more:hover { color: #1a1a1a; }

.jpi-practice-stats { background: #1a1a1a; color: #fff; padding: 50px 0; margin-top: 30px; }
.jpi-practice-stats .row { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px; text-align: center; }
.jpi-practice-stats .num { font-size: 42px; font-weight: 800; color: #f4f1ec; line-height: 1; }
.jpi-practice-stats .lbl { color: rgba(255,255,255,0.7); margin-top: 6px; font-size: 14px; }
</style>

<section class="jpi-practice-intro">
    <div class="container">
        <p class="lead">
            يمتلك <strong>مكتب JPI للمحاماة</strong> فريقاً متخصّصاً يغطّي أهمّ مجالات القانون في
            <strong>الأردن وفلسطين</strong>.
            خبرتنا تشمل قانون الشركات، القانون التجاري، القانون المدني والجنائي،
            <a href="<?= htmlspecialchars($base_url) ?>blog/qadaya-talaq-hadana-jordan">قضايا الأحوال الشخصيّة</a>،
            وقضايا
            <a href="<?= htmlspecialchars($base_url) ?>blog/jaraim-iliktroniya-jordan-2026">الجرائم الإلكترونيّة</a>.
            نختار لكل قضيّة المحامي الأنسب من فريقنا، ونعمل بمنهج جماعي لضمان أفضل نتيجة لعميلنا.
        </p>
    </div>
</section>

<section class="jpi-practice-grid">
    <div class="container">
        <div class="grid">
            <?php foreach ($_areas as $a): ?>
                <article class="jpi-area-card">
                    <div class="icon">
                        <?php if (!empty($a['icon'])): ?>
                            <i class="<?= htmlspecialchars($a['icon']) ?>" aria-hidden="true"></i>
                        <?php else: ?>
                            <i class="fa-solid fa-scale-balanced" aria-hidden="true"></i>
                        <?php endif; ?>
                    </div>
                    <h2><?= htmlspecialchars($a['title_ar']) ?></h2>
                    <?php if (!empty($a['short_desc_ar'])): ?>
                        <p><?= htmlspecialchars($a['short_desc_ar']) ?></p>
                    <?php endif; ?>
                    <a href="<?= htmlspecialchars($base_url) ?>appointment" class="more">
                        احجز موعد استشارة <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="jpi-practice-stats">
    <div class="container">
        <div class="row">
            <div>
                <div class="num">+5</div>
                <div class="lbl">سنوات خبرة في الأردن وفلسطين</div>
            </div>
            <div>
                <div class="num">+200</div>
                <div class="lbl">قضيّة مكتملة بنجاح</div>
            </div>
            <div>
                <div class="num">24/7</div>
                <div class="lbl">دعم استشاري للعملاء</div>
            </div>
            <div>
                <div class="num">100%</div>
                <div class="lbl">سرّيّة تامّة في كل قضيّة</div>
            </div>
        </div>
    </div>
</section>
