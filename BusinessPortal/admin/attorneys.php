<?php
require_once __DIR__ . '/../config/config.php';
require_once BP_INCLUDES . '/auth.php';
require_once BP_INCLUDES . '/crud.php';
requireLogin();

$pageTitle = __('attorneys');
$current = 'attorneys';

crud_run($pdo, [
    'table'        => 'attorneys',
    'page_title'   => __('attorneys'),
    'current'      => 'attorneys',
    'has_slug'     => true,
    'has_active'   => true,
    'has_order'    => true,
    'order_by'     => 'sort_order, id',
    'upload_subdir'=> 'attorneys',
    'list_image'   => 'image',
    'title_column' => 'name',
    'list_columns' => [
        ['field' => 'email',      'label' => __('email')],
        ['field' => 'phone',      'label' => __('phone')],
    ],
    'fields' => [
        ['name' => 'name',  'type' => 'text',     'bilingual' => true, 'required' => true, 'label_ar' => 'الاسم',    'label_en' => 'Name'],
        ['name' => 'title', 'type' => 'text',     'bilingual' => true,                     'label_ar' => 'المسمى',   'label_en' => 'Position'],
        ['name' => 'bio',   'type' => 'rich',     'bilingual' => true, 'rows' => 8,        'label_ar' => 'السيرة',   'label_en' => 'Biography'],
        ['name' => 'image', 'type' => 'image', 'label' => __('image')],
        ['name' => 'email', 'type' => 'email', 'label' => __('email')],
        ['name' => 'phone', 'type' => 'phone', 'label' => __('phone')],
        ['name' => 'linkedin',  'type' => 'url',  'label' => 'LinkedIn'],
        ['name' => 'facebook',  'type' => 'url',  'label' => 'Facebook'],
        ['name' => 'twitter',   'type' => 'url',  'label' => 'Twitter / X'],
        ['name' => 'instagram', 'type' => 'url',  'label' => 'Instagram'],
    ],
]);
