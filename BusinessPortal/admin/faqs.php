<?php
require_once __DIR__ . '/../config/config.php';
require_once BP_INCLUDES . '/auth.php';
require_once BP_INCLUDES . '/crud.php';
requireLogin();

$pageTitle = __('faqs');
$current = 'faqs';

crud_run($pdo, [
    'table'        => 'faqs',
    'page_title'   => __('faqs'),
    'current'      => 'faqs',
    'has_slug'     => false,
    'has_active'   => true,
    'has_order'    => true,
    'order_by'     => 'sort_order, id',
    'title_column' => 'question',
    'list_columns' => [
        ['field' => 'sort_order', 'label' => __('sort_order')],
    ],
    'fields' => [
        ['name' => 'question', 'type' => 'text',     'bilingual' => true, 'required' => true, 'label_ar' => 'السؤال',  'label_en' => 'Question'],
        ['name' => 'answer',   'type' => 'rich',     'bilingual' => true, 'rows' => 6,        'label_ar' => 'الإجابة',  'label_en' => 'Answer'],
        ['name' => 'category', 'type' => 'text',     'bilingual' => true,                     'label_ar' => 'الفئة',    'label_en' => 'Category'],
    ],
]);
