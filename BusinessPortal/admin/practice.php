<?php
require_once __DIR__ . '/../config/config.php';
require_once BP_INCLUDES . '/auth.php';
require_once BP_INCLUDES . '/crud.php';
requireLogin();

$pageTitle = __('practice_areas');
$current = 'practice';

crud_run($pdo, [
    'table'        => 'practice_areas',
    'page_title'   => __('practice_areas'),
    'current'      => 'practice',
    'has_slug'     => true,
    'has_active'   => true,
    'has_order'    => true,
    'order_by'     => 'sort_order, id',
    'upload_subdir'=> 'practice',
    'list_image'   => 'image',
    'title_column' => 'title',
    'list_columns' => [
        ['field' => 'icon',       'label' => __('icon')],
        ['field' => 'sort_order', 'label' => __('sort_order')],
    ],
    'fields' => [
        ['name' => 'title',      'type' => 'text',     'bilingual' => true, 'required' => true, 'label_ar' => 'العنوان',    'label_en' => 'Title'],
        ['name' => 'short_desc', 'type' => 'textarea', 'bilingual' => true, 'rows' => 2,        'label_ar' => 'وصف مختصر', 'label_en' => 'Short description'],
        ['name' => 'content',    'type' => 'rich',     'bilingual' => true, 'rows' => 10,       'label_ar' => 'المحتوى',    'label_en' => 'Content'],
        ['name' => 'icon',       'type' => 'text',     'label' => __('icon') . ' (FontAwesome)'],
        ['name' => 'image',      'type' => 'image',    'label' => __('image')],
    ],
]);
