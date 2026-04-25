<?php
// Pull FAQs from CMS
$_faqs = [];
if (isset($pdo) && $pdo instanceof PDO) {
    $_stmt = $pdo->query("SELECT question_ar, answer_ar FROM faqs WHERE is_active = 1 ORDER BY sort_order, id");
    $_faqs = $_stmt ? $_stmt->fetchAll() : [];
}

// FAQPage JSON-LD — gives rich snippets in Google search results
if (!empty($_faqs)) {
    $_faqLd = [
        '@context' => 'https://schema.org',
        '@type'    => 'FAQPage',
        'mainEntity' => array_map(fn($f) => [
            '@type' => 'Question',
            'name'  => $f['question_ar'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => trim(strip_tags($f['answer_ar'])),
            ],
        ], $_faqs),
    ];
    echo "<script type=\"application/ld+json\">" . json_encode($_faqLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "</script>";
}
?>

<!-- Page Title -->
<div class="page-title-area page-title-area-three title-img-one">
    <div class="d-table">
        <div class="d-table-cell">
            <div class="page-title-text">
                <h1>الأسئلة الشائعة عن خدمات JPI القانونية</h1>
                <ul>
                    <li><a href="<?= htmlspecialchars($base_url) ?>">الصفحة الرئيسية</a></li>
                    <li><i class="icofont-simple-left"></i></li>
                    <li>الأسئلة الشائعة</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- End Page Title -->

<style>
.jpi-faq-section { padding: 70px 0; background: #fafafa; }
.jpi-faq-section .lead { max-width: 780px; margin: 0 auto 40px; text-align: center; color: #444; line-height: 1.9; font-size: 16px; }
.jpi-faq-list { max-width: 860px; margin: 0 auto; display: flex; flex-direction: column; gap: 14px; }
.jpi-faq-item { background: #fff; border: 1px solid #e8e8e8; border-radius: 10px; overflow: hidden; transition: border-color .2s, box-shadow .2s; }
.jpi-faq-item:hover { border-color: #66573e; box-shadow: 0 4px 14px rgba(0,0,0,0.04); }
.jpi-faq-item summary { padding: 18px 24px; font-weight: 700; font-size: 16px; color: #1a1a1a; cursor: pointer; list-style: none; display: flex; justify-content: space-between; align-items: center; gap: 14px; }
.jpi-faq-item summary::-webkit-details-marker { display: none; }
.jpi-faq-item summary::after { content: '+'; font-size: 22px; color: #66573e; transition: transform .25s; flex-shrink: 0; }
.jpi-faq-item[open] summary::after { transform: rotate(45deg); }
.jpi-faq-item[open] summary { background: #fafafa; border-bottom: 1px solid rgba(102,87,62,0.20); }
.jpi-faq-answer { padding: 18px 24px 22px; color: #444; line-height: 1.9; font-size: 15px; }
.jpi-faq-answer p { margin: 0 0 12px; }
.jpi-faq-answer p:last-child { margin-bottom: 0; }
.jpi-faq-cta { max-width: 860px; margin: 50px auto 0; padding: 32px; background: #1a1a1a; color: #fff; border-radius: 14px; text-align: center; }
.jpi-faq-cta h3 { color: #f4f1ec; font-size: 22px; margin-bottom: 10px; font-weight: 700; }
.jpi-faq-cta p { color: rgba(255,255,255,0.75); margin-bottom: 20px; }
.jpi-faq-cta a { display: inline-block; padding: 12px 28px; background: #66573e; color: #fff; border-radius: 8px; font-weight: 700; text-decoration: none; transition: all .2s; }
.jpi-faq-cta a:hover { background: #fff; color: #1a1a1a; transform: translateY(-2px); }
.jpi-faq-empty { text-align: center; padding: 60px 20px; color: #6c707a; }
</style>

<section class="jpi-faq-section">
    <div class="container">
        <p class="lead">
            في هذه الصفحة جمعنا أكثر الأسئلة التي يطرحها عملاؤنا في الأردن وفلسطين حول خدماتنا القانونية، طرق الاستشارة، التكاليف، ومدة الإجراءات. لو لم تجد إجابتك،
            <a href="<?= htmlspecialchars($base_url) ?>contact" style="color: #66573e; font-weight: bold;">تواصل معنا</a>
            وسنردّ خلال 24 ساعة.
        </p>

        <?php if (empty($_faqs)): ?>
            <div class="jpi-faq-empty">
                <i class="fa-regular fa-circle-question" style="font-size: 56px; color: #66573e; margin-bottom: 14px;"></i>
                <p>لا توجد أسئلة منشورة حالياً.</p>
            </div>
        <?php else: ?>
            <div class="jpi-faq-list">
                <?php foreach ($_faqs as $i => $f): ?>
                    <details class="jpi-faq-item" <?= $i === 0 ? 'open' : '' ?>>
                        <summary><?= htmlspecialchars($f['question_ar']) ?></summary>
                        <div class="jpi-faq-answer">
                            <?= $f['answer_ar'] /* trusted HTML from admin */ ?>
                        </div>
                    </details>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="jpi-faq-cta">
            <h3>لم تجد إجابتك؟</h3>
            <p>فريقنا القانوني جاهز للردّ على استفسارك خلال 24 ساعة.</p>
            <a href="<?= htmlspecialchars($base_url) ?>contact">تواصل معنا الآن</a>
        </div>
    </div>
</section>
