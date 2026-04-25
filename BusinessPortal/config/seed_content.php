<?php
/**
 * Seed all existing site content into the CMS database.
 * Idempotent — safe to re-run; uses INSERT IGNORE / WHERE NOT EXISTS.
 *
 * Run:  php BusinessPortal/config/seed_content.php
 */

require_once __DIR__ . '/config.php';

$inserted = ['services'=>0,'practice'=>0,'attorneys'=>0,'faqs'=>0,'certificates'=>0,'testimonials'=>0,'settings'=>0];

// ─── 1. Services (10 items) ──────────────────────────────────────────────────
$services = [
    ['tasees-sharikat',         'تأسيس الشركات',                 'Company Formation',         'تسجيل وتأسيس الشركات حسب القوانين المحلية والدولية بكل احترافية وشفافية.', 'Registration and formation of companies under local and international laws with full professionalism and transparency.', 'fa-solid fa-building'],
    ['mutalabat-maliya',        'المطالبات المالية',             'Financial Claims',          'استرداد الحقوق المالية بالطرق القانونية سواء من أفراد أو مؤسسات.', 'Recovering financial rights legally — from individuals or institutions.', 'fa-solid fa-money-bill-transfer'],
    ['muhami-sharikat',         'محامي شركات',                   'Corporate Lawyer',          'تقديم الاستشارات القانونية للشركات، صياغة العقود التجارية، وحماية مصالح الشركات من النزاعات القانونية.', 'Legal advisory for companies, drafting commercial contracts, and protecting corporate interests from disputes.', 'fa-solid fa-briefcase'],
    ['qadaya-jinaiya',          'القضايا الجنائية',              'Criminal Cases',            'تمثيل قانوني في جميع مراحل الدعوى الجزائية من التحقيق وحتى صدور الحكم.', 'Legal representation through every stage of criminal proceedings — from investigation to verdict.', 'fa-solid fa-gavel'],
    ['itiradat-aradi',          'الاعتراضات على الأراضي',        'Land Objections',           'نقدّم خدمات الطعن القانوني في قرارات الأراضي ونزاعات التملك.', 'Legal challenges to land decisions and ownership disputes.', 'fa-solid fa-map-location-dot'],
    ['uqud-istasharat',         'العقود والاستشارات القانونية',  'Contracts & Advisory',      'صياغة، مراجعة، وتدقيق العقود القانونية بمهنية عالية، إلى جانب استشارات قانونية دقيقة.', 'Drafting, reviewing and auditing legal contracts with high professionalism, plus precise legal counsel.', 'fa-solid fa-file-contract'],
    ['istasharat-aqariya',      'الاستشارات العقارية والمالية',  'Real Estate & Finance',     'خدمات قانونية في البيع، الشراء، الرهن العقاري، والتمويل العقاري.', 'Legal services for sale, purchase, mortgage and real-estate financing.', 'fa-solid fa-house-chimney'],
    ['khadamat-dawliya',        'الخدمات القانونية الدولية',     'International Legal Services','تمثيل قانوني عبر الحدود يشمل العقود الدولية وتسوية النزاعات خارج البلاد.', 'Cross-border legal representation including international contracts and dispute resolution abroad.', 'fa-solid fa-globe'],
    ['khadamat-daribiya',       'الخدمات القانونية للضرائب',     'Tax Legal Services',        'استشارات وتمثيل في القضايا الضريبية وضمان الالتزام بالقوانين المالية.', 'Advisory and representation in tax cases, ensuring compliance with financial regulations.', 'fa-solid fa-receipt'],
    ['istasharat-amma',         'الاستشارات القانونية العامة',   'General Legal Advisory',    'نقدّم استشارات قانونية شاملة تغطي مختلف المجالات للأفراد والمؤسسات.', 'Comprehensive legal advisory covering various fields for individuals and institutions.', 'fa-solid fa-comments'],
];

$ins = $pdo->prepare(
    'INSERT IGNORE INTO services (slug, title_ar, title_en, short_desc_ar, short_desc_en, icon, sort_order, is_active)
     VALUES (?, ?, ?, ?, ?, ?, ?, 1)'
);
foreach ($services as $i => $s) {
    if ($ins->execute([$s[0], $s[1], $s[2], $s[3], $s[4], $s[5], $i + 1]) && $ins->rowCount() > 0) {
        $inserted['services']++;
    }
}

// ─── 2. Practice Areas (7 items) ─────────────────────────────────────────────
$practice = [
    ['muhami-sharikat-area',  'محامي شركات',          'Corporate Law',     'تقديم استشارات قانونية للشركات، صياغة العقود التجارية، وحماية مصالح الشركات من النزاعات القانونية والتجارية.', 'Legal advisory for corporations, drafting commercial contracts, and protecting corporate interests from legal and commercial disputes.', 'fa-solid fa-building-columns'],
    ['qanun-madani',          'القانون المدني',       'Civil Law',         'استشارات وتمثيل قانوني في القضايا المدنية مثل التعويضات، العقود، والمسؤولية المدنية.', 'Advisory and representation in civil matters such as compensation, contracts, and civil liability.', 'fa-solid fa-balance-scale'],
    ['qanun-usra',            'قانون الأسرة',         'Family Law',        'حلول قانونية في قضايا الأحوال الشخصية مثل الطلاق، الحضانة، والنفقة بكل خصوصية واحترافية.', 'Legal solutions for personal status matters — divorce, custody, and alimony — handled with full discretion.', 'fa-solid fa-people-roof'],
    ['qanun-tijari',          'القانون التجاري',      'Commercial Law',    'خدمات قانونية للشركات في العقود التجارية، التأسيس، وحماية الملكية التجارية.', 'Legal services for businesses — commercial contracts, formation, and trade-mark protection.', 'fa-solid fa-handshake'],
    ['qanun-taleem',          'قانون التعليم',        'Education Law',     'تمثيل قانوني في قضايا التعليم للطلاب والمعلمين في المدارس والجامعات.', 'Legal representation in education matters for students and teachers across schools and universities.', 'fa-solid fa-graduation-cap'],
    ['qanun-jinai',           'القانون الجنائي',      'Criminal Law',      'دفاع شامل في القضايا الجنائية وضمان العدالة ضمن إطار القانون الأردني.', 'Comprehensive defence in criminal cases, ensuring justice within Jordanian legal framework.', 'fa-solid fa-gavel'],
    ['qanun-iliktroni',       'القانون الإلكتروني',   'Cyber Law',         'استشارات في قضايا الجرائم الإلكترونية والخصوصية الرقمية وحماية البيانات.', 'Advisory on cybercrime, digital privacy and data protection matters.', 'fa-solid fa-shield-halved'],
];

$ins = $pdo->prepare(
    'INSERT IGNORE INTO practice_areas (slug, title_ar, title_en, short_desc_ar, short_desc_en, icon, sort_order, is_active)
     VALUES (?, ?, ?, ?, ?, ?, ?, 1)'
);
foreach ($practice as $i => $p) {
    if ($ins->execute([$p[0], $p[1], $p[2], $p[3], $p[4], $p[5], $i + 1]) && $ins->rowCount() > 0) {
        $inserted['practice']++;
    }
}

// ─── 3. Attorneys (2 founding partners) ──────────────────────────────────────
$attorneys = [
    [
        'aseel-abu-sara',
        'المحامية أسيل أبو ساره',
        'Aseel Abu Sara, Esq.',
        'محامية قانون عام، شريك مؤسس',
        'Public Law Attorney, Founding Partner',
        '<p>المحامية <strong>أسيل أبو ساره</strong> — محامية قانون عام حاصلة على درجة الماجستير، وأحد الشريكين المؤسسين لمكتب JPI للمحاماة.</p><p>تتمتع بخبرة واسعة في القانون المدني، التجاري، والجنائي، وتقدّم استشارات قانونية متخصصة للأفراد والشركات في الأردن وفلسطين.</p>',
        '<p><strong>Aseel Abu Sara</strong>, Esq. — Public Law Attorney with a Master\'s degree, and a Founding Partner of JPI Law Firm.</p><p>She brings extensive experience in civil, commercial and criminal law, providing specialised legal advisory to individuals and corporations across Jordan and Palestine.</p>',
        'assets/img/home-one/asel.png',
        'Aseel@jpilawfirm.com',
        '00962796872442',
        '',
        'https://x.com/AseelAbusara?t=lAiwGilBtfeGY8OpwvUGRg&s=09',
    ],
    [
        'majd-al-ahmad',
        'المحامي مجد الأحمد',
        'Majd Al-Ahmad, Esq.',
        'محامي قانون عام، شريك مؤسس',
        'Public Law Attorney, Founding Partner',
        '<p>المحامي <strong>مجد الأحمد</strong> — محامي قانون عام حاصل على درجة الماجستير، وأحد الشريكين المؤسسين لمكتب JPI للمحاماة.</p><p>متخصص في قضايا الشركات، العقود، والقانون التجاري الدولي، ويتولى تمثيل العملاء أمام المحاكم في الأردن وفلسطين.</p>',
        '<p><strong>Majd Al-Ahmad</strong>, Esq. — Public Law Attorney with a Master\'s degree, and a Founding Partner of JPI Law Firm.</p><p>Specialises in corporate matters, contracts and international commercial law, representing clients before courts in Jordan and Palestine.</p>',
        'assets/img/home-one/img1.png',
        'info@jpilawfirm.com',
        '00970592900242',
        '',
        'https://x.com/majdalahmad83?t=LG_lD7itc2D1CzBpFfJlSQ&s=09',
    ],
];

$ins = $pdo->prepare(
    'INSERT IGNORE INTO attorneys
     (slug, name_ar, name_en, title_ar, title_en, bio_ar, bio_en, image, email, phone, linkedin, twitter, sort_order, is_featured, is_active)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 1)'
);
foreach ($attorneys as $i => $a) {
    $img = SITE_URL . $a[7];
    if ($ins->execute([$a[0], $a[1], $a[2], $a[3], $a[4], $a[5], $a[6], $img, $a[8], $a[9], $a[10], $a[11], $i + 1]) && $ins->rowCount() > 0) {
        $inserted['attorneys']++;
    }
}

// ─── 4. FAQs (5 items) ───────────────────────────────────────────────────────
$faqs = [
    [
        'ما هي أنواع القضايا التي تتخصص فيها شركة JPI Law Firm؟',
        'What types of cases does JPI Law Firm specialise in?',
        'نحن متخصصون في مجموعة واسعة من المجالات القانونية بما في ذلك تأسيس الشركات، المطالبات المالية، القضايا الجزائية، الاعتراضات على الأراضي، القضايا العقارية، قضايا الضرائب، وتنظيم العقود.',
        'We specialise in a wide range of legal areas including company formation, financial claims, criminal cases, land objections, real-estate matters, tax cases, and contract drafting.',
        'عام', 'General',
    ],
    [
        'هل يمكنني اختيار المحامي الذي سيتولى قضيتي؟',
        'Can I choose the attorney who will handle my case?',
        'نعم، يمكنك اختيار المحامي الذي ترغب في التعامل معه بناءً على تخصصه واحتياجاتك القانونية. كما نقدم مرونة في اختيار محامٍ آخر إذا لزم الأمر.',
        'Yes — you can choose the attorney based on their specialty and your needs, with flexibility to switch attorneys if required.',
        'الإجراءات', 'Procedure',
    ],
    [
        'ما هي المدة المتوقعة للحصول على استشارة قانونية؟',
        'How long does it take to get a legal consultation?',
        'عادةً ما نقوم بالرد على طلبات الاستشارات في غضون 24 ساعة، وتحديد موعد للاستشارة في أقرب وقت ممكن بناءً على توافر المحامي واختيارك للموعد.',
        'We typically respond to consultation requests within 24 hours and schedule the consultation as soon as the attorney\'s availability and your preferred slot align.',
        'الإجراءات', 'Procedure',
    ],
    [
        'هل تقدمون استشارات قانونية عبر الإنترنت؟',
        'Do you offer online legal consultations?',
        'نعم، نحن نوفر استشارات قانونية عبر الإنترنت من خلال مكالمات الفيديو أو الهاتف. يمكنك اختيار هذه الخدمة أثناء حجز الاستشارة.',
        'Yes — we offer online consultations via video or phone calls. You can choose this option when booking.',
        'الخدمات', 'Services',
    ],
    [
        'ما هي تكاليف الاستشارة القانونية؟',
        'What are the legal consultation fees?',
        'تختلف تكلفة الاستشارة بناءً على نوع القضية وتعقيدها. نوصي بالاتصال بنا أو إرسال استفسار عبر النموذج الإلكتروني للحصول على تقدير للتكلفة.',
        'Fees vary depending on the case type and complexity. We recommend contacting us or submitting an inquiry through our online form for a fee estimate.',
        'التكلفة', 'Pricing',
    ],
];

$ins = $pdo->prepare(
    'INSERT IGNORE INTO faqs (question_ar, question_en, answer_ar, answer_en, category_ar, category_en, sort_order, is_active)
     VALUES (?, ?, ?, ?, ?, ?, ?, 1)'
);
foreach ($faqs as $i => $f) {
    if ($ins->execute([$f[0], $f[1], $f[2], $f[3], $f[4], $f[5], $i + 1]) && $ins->rowCount() > 0) {
        $inserted['faqs']++;
    }
}

// ─── 5. Certificates (8 items) ───────────────────────────────────────────────
$certificates = [
    ['شهادة المحامية أسيل أبو ساره — رقم 1', 'Aseel Abu Sara — Certificate 1', 'assets/img/3.png'],
    ['شهادة المحامية أسيل أبو ساره — رقم 2', 'Aseel Abu Sara — Certificate 2', 'assets/img/4.png'],
    ['شهادة المحامية أسيل أبو ساره — رقم 3', 'Aseel Abu Sara — Certificate 3', 'assets/img/5.png'],
    ['شهادة المحامية أسيل أبو ساره — رقم 4', 'Aseel Abu Sara — Certificate 4', 'assets/img/6.png'],
    ['شهادة المحامية أسيل أبو ساره — رقم 5', 'Aseel Abu Sara — Certificate 5', 'assets/img/asell.png'],
    ['شهادة المحامية أسيل أبو ساره — رقم 6', 'Aseel Abu Sara — Certificate 6', 'assets/img/asell (1).png'],
    ['شهادة المحامية أسيل أبو ساره — رقم 7', 'Aseel Abu Sara — Certificate 7', 'assets/img/asell (2).png'],
    ['شهادة المحامية أسيل أبو ساره — رقم 8', 'Aseel Abu Sara — Certificate 8', 'assets/img/asell (3).png'],
];

$ins = $pdo->prepare(
    'INSERT IGNORE INTO certificates (title_ar, title_en, image, issued_by_ar, issued_by_en, sort_order, is_active)
     VALUES (?, ?, ?, ?, ?, ?, 1)'
);
foreach ($certificates as $i => $c) {
    $img = SITE_URL . $c[2];
    if ($ins->execute([$c[0], $c[1], $img, 'مكتب JPI للمحاماة', 'JPI Law Firm', $i + 1]) && $ins->rowCount() > 0) {
        $inserted['certificates']++;
    }
}

// ─── 6. Testimonials (sample placeholders since none in current site) ────────
$testimonials = [
    [
        'أحمد محمود', 'Ahmad Mahmoud',
        'رجل أعمال', 'Business Owner',
        'تعامل احترافي وفريق قانوني خبير. ساعدوني في تأسيس شركتي بسرعة وبدون أي تعقيدات. أنصح بهم بشدة لكل من يحتاج خدمة قانونية موثوقة.',
        'Highly professional team. They helped me incorporate my company quickly and smoothly. Strongly recommended for anyone needing reliable legal services.',
        5,
    ],
    [
        'سامية الحسن', 'Samia Al-Hasan',
        'موكّلة', 'Client',
        'استشارة دقيقة ومتابعة ممتازة لقضيتي. الفريق متفانٍ ويفهم تفاصيل القانون الأردني والفلسطيني بعمق.',
        'Precise consultation and excellent follow-up on my case. The team is dedicated and deeply understands both Jordanian and Palestinian law.',
        5,
    ],
    [
        'خالد العمري', 'Khaled Al-Omari',
        'مدير شركة', 'Company Director',
        'ربطنا معهم عقد استشارة شركات منذ سنتين وما ندمنا. يردّون بسرعة وحلولهم القانونية عملية وواقعية.',
        'We have had a corporate advisory contract with them for two years — never regretted it. Quick response and practical legal solutions.',
        5,
    ],
];

$ins = $pdo->prepare(
    'INSERT IGNORE INTO testimonials (name_ar, name_en, role_ar, role_en, content_ar, content_en, rating, sort_order, is_active)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)'
);
$existing = (int) $pdo->query('SELECT COUNT(*) FROM testimonials')->fetchColumn();
if ($existing === 0) {
    foreach ($testimonials as $i => $t) {
        if ($ins->execute([$t[0], $t[1], $t[2], $t[3], $t[4], $t[5], $t[6], $i + 1])) {
            $inserted['testimonials']++;
        }
    }
}

// ─── 7. Update site_settings with real contact data ──────────────────────────
$updates = [
    'site_name'        => ['JPI - مكتب المحاماة',                                  'JPI Law Firm'],
    'site_tagline'     => ['مكتب جي بي آي للمحاماة والاستشارات القانونية',       'JPI Law & Legal Consultancy'],
    'site_description' => [
        'نقدم في مكتب جي بي آي خدمات قانونية متكاملة ومتخصصة في جميع فروع القانون مثل القانون المدني، التجاري، الجنائي، وقانون العمل. فريقنا من المحامين ذوي الخبرة يضمن لك التمثيل القانوني الأمثل وفق أعلى معايير الجودة والسرية التامة.',
        'JPI Law Firm provides comprehensive legal services across all branches of law — civil, commercial, criminal, and labour law. Our experienced attorneys deliver the highest standards of representation with full confidentiality.',
    ],
    'contact_email'    => ['info@jpilawfirm.com',           'info@jpilawfirm.com'],
    'contact_phone'    => ['00962 79 628 6204',             '00962 79 628 6204'],
    'contact_whatsapp' => ['00962 79 687 2442',             '00962 79 687 2442'],
    'address'          => ['شارع المدينة المنورة، عمّان، الأردن', 'Al Madina Al Monawara Street, Amman, Jordan'],
    'address_palestine'=> ['ميدان المنارة، رام الله، فلسطين',       'Al-Manara Square, Ramallah, Palestine'],
    'working_hours'    => ['الأحد - الخميس: 9:00 - 17:00',          'Sun – Thu: 9:00 – 17:00'],
    'social_twitter'   => ['https://x.com/AseelAbusara?t=lAiwGilBtfeGY8OpwvUGRg&s=09', 'https://x.com/AseelAbusara?t=lAiwGilBtfeGY8OpwvUGRg&s=09'],
];

$upd = $pdo->prepare('UPDATE site_settings SET value_ar = ?, value_en = ? WHERE `key` = ?');
foreach ($updates as $key => [$ar, $en]) {
    if ($upd->execute([$ar, $en, $key])) {
        $inserted['settings'] += $upd->rowCount();
    }
}

// ─── Done ────────────────────────────────────────────────────────────────────
echo "\n✅ Content seed complete:\n";
foreach ($inserted as $k => $n) printf("   %-15s %d\n", $k . ':', $n);
echo "\nView at: " . BP_URL . "admin/\n";
