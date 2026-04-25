<?php
// Pull content from CMS (pages_content), fall back to default if empty.
$_pageData = null;
if (isset($pdo) && $pdo instanceof PDO) {
    $_stmt = $pdo->prepare("SELECT title_ar, content_ar FROM pages_content WHERE page_slug = 'privacy-policy' AND section_key = 'body' LIMIT 1");
    $_stmt->execute();
    $_pageData = $_stmt->fetch();
}

$_title   = $_pageData['title_ar']   ?? 'سياسة الخصوصية';
$_content = $_pageData['content_ar'] ?? '';
?>
<!-- عنوان الصفحة -->
<div class="page-title-area title-img-one">
    <div class="d-table">
        <div class="d-table-cell">
            <div class="page-title-text">
                <h2><?= htmlspecialchars($_title) ?></h2>
                <ul>
                    <li><a href="<?= htmlspecialchars($base_url) ?>">الرئيسية</a></li>
                    <li><i class="icofont-simple-left"></i></li>
                    <li><?= htmlspecialchars($_title) ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- نهاية عنوان الصفحة -->

<style>
.privacy-area .privacy-item h2 { font-size: 22px; color: #1a1a1a; margin-bottom: 12px; font-weight: 700; }
.privacy-area .privacy-item p, .privacy-area .privacy-item li { font-size: 15px; line-height: 1.9; color: #333; }
.privacy-area .privacy-item ul { padding-inline-start: 20px; }
.privacy-area .privacy-item ul li { margin-bottom: 8px; }
.privacy-area .privacy-item { margin-bottom: 32px; }
</style>

<section class="privacy-area pt-100 pb-100">
    <div class="container">
        <div class="privacy-item">
            <?php if ($_content !== ''): ?>
                <?= $_content /* trusted HTML from admin */ ?>
            <?php else: ?>
                <h2>1. ما هي سياسة الخصوصية؟</h2>
                <p>سياسة الخصوصية توضح كيفية جمع المعلومات الشخصية واستخدامها وحمايتها عند زيارتك لموقعنا أو استخدامك لخدماتنا القانونية. نحن ملتزمون بالحفاظ على سرية وخصوصية جميع البيانات التي يتم جمعها من عملائنا وزوارنا، وفقًا لأفضل المعايير والممارسات القانونية.</p>

                <h2>2. من تُطبق عليه هذه السياسة؟</h2>
                <p>تُطبق سياسة الخصوصية هذه على جميع عملائنا، وزوار موقعنا الإلكتروني، وأي شخص يستخدم خدماتنا القانونية سواء من خلال الإنترنت أو من خلال التواصل المباشر مع شركة JPI للمحاماة والاستشارات القانونية.</p>
                <p>من خلال استخدامك لموقعنا أو التواصل معنا، فإنك توافق على شروط هذه السياسة وتفوضنا بمعالجة بياناتك الشخصية بما يتوافق مع بنودها.</p>

                <h2>3. ما البيانات الشخصية التي نقوم بمعالجتها؟</h2>
                <ul>
                    <li>الاسم والبريد الإلكتروني (في حال مشاركتك في فعالياتنا أو استخدام خدمات معينة).</li>
                    <li>البيانات التي يتم جمعها من خلال موقعنا أو النشرات البريدية أو المراسلات الإلكترونية.</li>
                    <li>عنوان IP الخاص بك.</li>
                    <li>سلوكك أثناء تصفح الموقع، مثل الصفحات التي تزورها ومدة الزيارة.</li>
                    <li>تفاعلك مع الرسائل الإلكترونية مثل فتح الرسائل واختيار أقسام معينة داخلها.</li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</section>
