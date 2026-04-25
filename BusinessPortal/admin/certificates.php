<?php
require_once __DIR__ . '/../config/config.php';
require_once BP_INCLUDES . '/auth.php';
require_once BP_INCLUDES . '/crud.php';
requireLogin();

$pageTitle = __('certificates');
$current = 'certificates';

crud_run($pdo, [
    'table'        => 'certificates',
    'page_title'   => __('certificates'),
    'current'      => 'certificates',
    'has_slug'     => false,
    'has_active'   => true,
    'has_order'    => true,
    'order_by'     => 'sort_order, id',
    'upload_subdir'=> 'certificates',
    'list_image'   => 'image',
    'title_column' => 'title',
    'list_columns' => [
        ['field' => 'issued_at', 'label' => 'تاريخ الإصدار'],
    ],
    'fields' => [
        ['name' => 'title',       'type' => 'text',     'bilingual' => true, 'required' => true, 'label_ar' => 'العنوان',   'label_en' => 'Title'],
        ['name' => 'description', 'type' => 'textarea', 'bilingual' => true, 'rows' => 3,        'label_ar' => 'الوصف',     'label_en' => 'Description'],
        ['name' => 'issued_by',   'type' => 'text',     'bilingual' => true,                     'label_ar' => 'جهة الإصدار', 'label_en' => 'Issued by'],
        ['name' => 'image',       'type' => 'image',    'label' => __('image')],
        ['name' => 'issued_at',   'type' => 'date',     'label' => 'تاريخ الإصدار'],
    ],
]);
