<?php
/**
 * Seed default admin + initial site settings.
 * Run once from CLI or browser:  php seed.php
 *
 *   Email:    admin@jpilawfirm.com
 *   Password: JpiLaw@2026
 */

require_once __DIR__ . '/config.php';

$adminEmail    = 'admin@jpilawfirm.com';
$adminPassword = 'JpiLaw@2026';

// ── 1. Default super-admin ───────────────────────────────────────────────────
$stmt = $pdo->prepare('SELECT id FROM admins WHERE email = ?');
$stmt->execute([$adminEmail]);
if (!$stmt->fetch()) {
    $hash = password_hash($adminPassword, PASSWORD_DEFAULT);
    $pdo->prepare(
        'INSERT INTO admins (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)'
    )->execute(['Super Admin', $adminEmail, $hash, 'superadmin']);
    echo "✔ Created default admin: $adminEmail / $adminPassword\n";
} else {
    echo "✓ Admin already exists: $adminEmail\n";
}

// ── 2. Default site settings ─────────────────────────────────────────────────
$settings = [
    // General
    ['key' => 'site_name',         'group' => 'general', 'type' => 'text',  'label_ar' => 'اسم الموقع',         'label_en' => 'Site name',         'value_ar' => 'JPI - مكتب المحاماة',                'value_en' => 'JPI Law Firm'],
    ['key' => 'site_tagline',      'group' => 'general', 'type' => 'text',  'label_ar' => 'الشعار الفرعي',      'label_en' => 'Tagline',           'value_ar' => 'استشارات قانونية في الأردن وفلسطين', 'value_en' => 'Legal advisory in Jordan & Palestine'],
    ['key' => 'site_description',  'group' => 'general', 'type' => 'textarea','label_ar' => 'وصف الموقع',        'label_en' => 'Site description',  'value_ar' => 'مكتب JPI يقدّم خدمات قانونية متخصّصة في القضايا التجارية، العقارية، والجنائية في الأردن وفلسطين.', 'value_en' => 'JPI Law Firm provides specialised legal services in commercial, real-estate and criminal cases across Jordan and Palestine.'],
    ['key' => 'logo',              'group' => 'general', 'type' => 'image', 'label_ar' => 'الشعار',             'label_en' => 'Logo',              'value_ar' => 'assets/img/logo.png', 'value_en' => 'assets/img/logo.png'],

    // Contact
    ['key' => 'contact_email',     'group' => 'contact', 'type' => 'email', 'label_ar' => 'البريد الإلكتروني',   'label_en' => 'Email',             'value_ar' => 'info@jpilawfirm.com', 'value_en' => 'info@jpilawfirm.com'],
    ['key' => 'contact_phone',     'group' => 'contact', 'type' => 'phone', 'label_ar' => 'رقم الهاتف',          'label_en' => 'Phone',             'value_ar' => '+962 6 000 0000',     'value_en' => '+962 6 000 0000'],
    ['key' => 'contact_whatsapp',  'group' => 'contact', 'type' => 'phone', 'label_ar' => 'واتساب',              'label_en' => 'WhatsApp',          'value_ar' => '+962 7 0000 0000',    'value_en' => '+962 7 0000 0000'],
    ['key' => 'address',           'group' => 'contact', 'type' => 'textarea','label_ar' => 'العنوان',           'label_en' => 'Address',           'value_ar' => 'عمّان، الأردن',       'value_en' => 'Amman, Jordan'],
    ['key' => 'address_palestine', 'group' => 'contact', 'type' => 'textarea','label_ar' => 'عنوان فلسطين',      'label_en' => 'Address (Palestine)', 'value_ar' => 'رام الله، فلسطين',  'value_en' => 'Ramallah, Palestine'],
    ['key' => 'working_hours',     'group' => 'contact', 'type' => 'text',  'label_ar' => 'ساعات العمل',         'label_en' => 'Working hours',     'value_ar' => 'الأحد - الخميس: 9:00 - 17:00', 'value_en' => 'Sun – Thu: 9:00 – 17:00'],

    // Social
    ['key' => 'social_facebook',   'group' => 'social', 'type' => 'url', 'label_ar' => 'فيسبوك',     'label_en' => 'Facebook',  'value_ar' => '', 'value_en' => ''],
    ['key' => 'social_twitter',    'group' => 'social', 'type' => 'url', 'label_ar' => 'تويتر / X',  'label_en' => 'Twitter / X','value_ar' => '', 'value_en' => ''],
    ['key' => 'social_instagram',  'group' => 'social', 'type' => 'url', 'label_ar' => 'إنستغرام',   'label_en' => 'Instagram', 'value_ar' => '', 'value_en' => ''],
    ['key' => 'social_linkedin',   'group' => 'social', 'type' => 'url', 'label_ar' => 'لينكدإن',    'label_en' => 'LinkedIn',  'value_ar' => '', 'value_en' => ''],
    ['key' => 'social_youtube',    'group' => 'social', 'type' => 'url', 'label_ar' => 'يوتيوب',     'label_en' => 'YouTube',   'value_ar' => '', 'value_en' => ''],

    // SEO
    ['key' => 'seo_keywords',      'group' => 'seo', 'type' => 'textarea','label_ar' => 'كلمات SEO',  'label_en' => 'SEO keywords','value_ar' => 'محامي، الأردن، فلسطين، استشارات قانونية', 'value_en' => 'lawyer, Jordan, Palestine, legal advisory'],
    ['key' => 'google_analytics',  'group' => 'seo', 'type' => 'text',    'label_ar' => 'Google Analytics ID', 'label_en' => 'Google Analytics ID','value_ar' => '', 'value_en' => ''],
];

$insSetting = $pdo->prepare(
    'INSERT IGNORE INTO site_settings (`key`, value_ar, value_en, `group`, type, label_ar, label_en, sort_order)
     VALUES (:key, :value_ar, :value_en, :group, :type, :label_ar, :label_en, :sort_order)'
);

$i = 0;
foreach ($settings as $s) {
    $insSetting->execute([
        ':key'        => $s['key'],
        ':value_ar'   => $s['value_ar'],
        ':value_en'   => $s['value_en'],
        ':group'      => $s['group'],
        ':type'       => $s['type'],
        ':label_ar'   => $s['label_ar'],
        ':label_en'   => $s['label_en'],
        ':sort_order' => $i++,
    ]);
}
echo "✔ Seeded " . count($settings) . " site settings\n";

// ── 3. Default blog categories ───────────────────────────────────────────────
$cats = [
    ['justice-and-law',   'العدالة والقانون',     'Justice & Law'],
    ['commercial-law',    'القانون التجاري',      'Commercial Law'],
    ['real-estate',       'العقارات',             'Real Estate'],
    ['family-law',        'قانون الأسرة',         'Family Law'],
    ['criminal-law',      'القانون الجنائي',      'Criminal Law'],
    ['legal-advice',      'استشارات قانونية',     'Legal Advice'],
];
$insCat = $pdo->prepare(
    'INSERT IGNORE INTO blog_categories (slug, name_ar, name_en, sort_order, is_active) VALUES (?, ?, ?, ?, 1)'
);
foreach ($cats as $i => [$slug, $ar, $en]) {
    $insCat->execute([$slug, $ar, $en, $i]);
}
echo "✔ Seeded " . count($cats) . " blog categories\n";

echo "\n✅ Seed complete.\n";
echo "Login at:  " . BP_URL . "auth/login.php\n";
echo "Email:     $adminEmail\n";
echo "Password:  $adminPassword\n";
