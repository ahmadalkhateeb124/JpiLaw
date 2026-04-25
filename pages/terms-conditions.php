<?php
// Pull content from CMS (pages_content), fall back to default if empty.
$_pageData = null;
if (isset($pdo) && $pdo instanceof PDO) {
    $_stmt = $pdo->prepare("SELECT title_ar, content_ar FROM pages_content WHERE page_slug = 'terms-conditions' AND section_key = 'body' LIMIT 1");
    $_stmt->execute();
    $_pageData = $_stmt->fetch();
}

$_title   = $_pageData['title_ar']   ?? 'الشروط والأحكام';
$_content = $_pageData['content_ar'] ?? '';
?>
<!-- Page Title -->
<div class="page-title-area title-img-one">
    <div class="d-table">
        <div class="d-table-cell">
            <div class="page-title-text">
                <h2><?= htmlspecialchars($_title) ?></h2>
                <ul>
                    <li><a href="<?= htmlspecialchars($base_url) ?>">الصفحة الرئيسية</a></li>
                    <li><i class="icofont-simple-left"></i></li>
                    <li><?= htmlspecialchars($_title) ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- End Page Title -->

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
                <h2>1. ما هي الشروط والأحكام؟</h2>
                <p>الشروط والأحكام هي مجموعة من القواعد والسياسات التي تضعها الشركة أو الموقع الإلكتروني لتنظيم العلاقة بينه وبين المستخدمين. هذه الشروط تشمل قواعد الاستخدام، حقوق الملكية الفكرية، المسؤوليات القانونية، وقواعد الخصوصية التي تنظم كيفية استخدام خدمات الموقع.</p>

                <h2>2. كيف يتم تطبيق الشروط والأحكام؟</h2>
                <p>تعتبر هذه الشروط ملزمة لكل من المستخدمين الذين يتفاعلون مع الموقع أو الخدمة. عند استخدامك للموقع أو خدماتنا، فإنك توافق على الالتزام بهذه الشروط والأحكام. إذا لم توافق على أي من هذه الشروط، يجب عليك التوقف عن استخدام الموقع.</p>
                <p>تهدف هذه الشروط إلى تحديد الحقوق والمسؤوليات بين الشركة والمستخدمين وضمان تجربة آمنة ومنظمة لكافة الأطراف.</p>

                <h2>3. ما هي البيانات الشخصية التي يمكننا جمعها؟</h2>
                <ul>
                    <li>الاسم، عنوان البريد الإلكتروني، والصورة الشخصية عند زيارة المعارض أو استخدام التقنيات التي نقدمها لك للحصول على هدايا مميزة.</li>
                    <li>البيانات الشخصية التي يتم جمعها عبر موقعنا الإلكتروني، النشرات الإخبارية، ورسائل البريد الإلكتروني التجارية.</li>
                    <li>عنوان الـ IP.</li>
                    <li>سلوكك في تصفح الموقع، مثل معلومات عن زيارتك الأولى.</li>
                    <li>ما إذا كنت تفتح النشرة الإخبارية أو البريد الإلكتروني وأي الأقسام التي تختارها.</li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</section>
