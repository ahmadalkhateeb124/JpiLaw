<?php
/** Sample blog posts so the public blog isn't empty on first view. */

require_once __DIR__ . '/config.php';

$samples = [
    [
        'slug'     => 'tasees-sharika-jordan',
        'cat_slug' => 'commercial-law',
        'title_ar' => 'كل ما تحتاج معرفته عن تأسيس الشركات في الأردن',
        'title_en' => 'Everything you need to know about company formation in Jordan',
        'excerpt_ar' => 'دليل عملي لأصحاب المشاريع لتأسيس شركة في الأردن: الإجراءات، التكاليف، والفروقات بين الأشكال القانونية.',
        'excerpt_en' => 'A practical guide for entrepreneurs covering procedures, costs, and the differences between legal forms.',
        'content_ar' => "<p>يتساءل كثير من رواد الأعمال عن أفضل شكل قانوني لتأسيس مشروعهم في الأردن، وعن الإجراءات الرسمية المطلوبة لإنجاز ذلك بسرعة وكفاءة. في هذا المقال نستعرض الخطوات الأساسية لتأسيس الشركات بمختلف أنواعها.</p><h2>أنواع الشركات في الأردن</h2><p>ينظّم قانون الشركات الأردني عدة أشكال قانونية يمكن لأصحاب المشاريع الاختيار من بينها، أهمها:</p><ul><li><strong>الشركة ذات المسؤولية المحدودة:</strong> الأكثر شيوعاً للمشاريع الصغيرة والمتوسطة.</li><li><strong>الشركة المساهمة العامة:</strong> مناسبة للمشاريع الكبيرة وطرح الأسهم.</li><li><strong>الشركة التضامنية:</strong> يتحمل فيها الشركاء مسؤولية شخصية.</li></ul><h2>الخطوات الرسمية</h2><p>تشمل عملية التأسيس عدة خطوات أساسية تبدأ من حجز الاسم التجاري وتنتهي بالحصول على الرقم الضريبي ورقم المؤسسة العامة للضمان الاجتماعي.</p><blockquote>يُنصح بالاستعانة بمستشار قانوني متخصص لضمان اختيار الشكل القانوني الأنسب لطبيعة المشروع.</blockquote><p>فريق <strong>JPI</strong> القانوني جاهز لمرافقتك في كل خطوة من خطوات التأسيس.</p>",
        'content_en' => "<p>Many entrepreneurs ask about the best legal form for their project in Jordan and the official procedures involved.</p><h2>Types of companies in Jordan</h2><p>Jordanian company law allows several legal forms, the most common being:</p><ul><li><strong>Limited Liability Company (LLC):</strong> the most common for SMEs.</li><li><strong>Public Shareholding Company:</strong> suitable for large enterprises.</li><li><strong>Partnership:</strong> partners bear personal liability.</li></ul>",
    ],
    [
        'slug'     => 'mukhalafat-asasiya-aqari',
        'cat_slug' => 'real-estate',
        'title_ar' => 'أهم الأخطاء القانونية في عقود البيع العقاري',
        'title_en' => 'Top legal mistakes in real estate sale contracts',
        'excerpt_ar' => 'كيف تحمي حقوقك عند شراء أو بيع عقار في الأردن وفلسطين؟ نصائح من خبرتنا في القضايا العقارية.',
        'excerpt_en' => 'How to protect your rights when buying or selling property in Jordan & Palestine.',
        'content_ar' => "<p>إن إبرام عقد بيع عقاري دون مراجعة قانونية دقيقة قد يكلّف الأطراف خسائر كبيرة لاحقاً. نستعرض أبرز الأخطاء التي يقع فيها كثيرون.</p><h2>1. عدم التحقق من السجل العيني</h2><p>قبل أي توقيع، يجب التأكد من أن البائع هو المالك الفعلي وأن العقار خالٍ من الرهون أو الحجوزات.</p><h2>2. غياب شروط الفسخ والتعويض</h2><p>يجب أن يحدد العقد بوضوح ما يحدث في حال عدم الالتزام من أحد الطرفين.</p>",
        'content_en' => "<p>Signing a real estate contract without proper legal review can cost both parties heavily.</p>",
    ],
    [
        'slug'     => 'huquq-al-aamil-amal',
        'cat_slug' => 'legal-advice',
        'title_ar' => 'حقوقك كموظف في القانون الأردني',
        'title_en' => 'Your rights as an employee under Jordanian law',
        'excerpt_ar' => 'الإجازات السنوية، نهاية الخدمة، الفصل التعسّفي — تعرّف على أهم حقوقك المنصوص عليها في قانون العمل.',
        'excerpt_en' => 'Annual leave, end-of-service, wrongful termination — know your key rights under Jordan\'s Labor Law.',
        'content_ar' => "<p>كثير من الموظفين لا يعرفون حقوقهم الأساسية في قانون العمل الأردني، مما يجعلهم عرضة للظلم في حالات الفصل أو إنهاء الخدمة.</p><h2>الإجازات السنوية</h2><p>يحق لكل موظف الحصول على 14 يوم إجازة سنوية مدفوعة الأجر بعد إكمال سنة من العمل.</p><h2>مكافأة نهاية الخدمة</h2><p>تُحتسب على أساس راتب شهر عن كل سنة عمل.</p>",
        'content_en' => "<p>Many employees are unaware of their basic rights under Jordan's Labor Law.</p>",
    ],
];

// Get the default super admin id
$adminId = (int) $pdo->query('SELECT id FROM admins ORDER BY id LIMIT 1')->fetchColumn();

$ins = $pdo->prepare(
    'INSERT IGNORE INTO blog_posts
     (slug, category_id, author_id, title_ar, title_en, excerpt_ar, excerpt_en, content_ar, content_en, status, published_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, "published", NOW())'
);

$catStmt = $pdo->prepare('SELECT id FROM blog_categories WHERE slug = ?');

$count = 0;
foreach ($samples as $s) {
    $catStmt->execute([$s['cat_slug']]);
    $catId = (int) $catStmt->fetchColumn() ?: null;

    $ok = $ins->execute([
        $s['slug'], $catId, $adminId,
        $s['title_ar'], $s['title_en'],
        $s['excerpt_ar'], $s['excerpt_en'],
        $s['content_ar'], $s['content_en'],
    ]);
    if ($ok && $ins->rowCount() > 0) $count++;
}

echo "✔ Inserted $count sample posts\n";
echo "View blog at: " . SITE_URL . "blog\n";
