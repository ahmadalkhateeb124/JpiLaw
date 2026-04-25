<?php
require_once __DIR__ . '/../config/config.php';
require_once BP_INCLUDES . '/auth.php';
require_once BP_INCLUDES . '/crud.php';
requireLogin();

$pageTitle = __('testimonials');
$current = 'testimonials';

crud_run($pdo, [
    'table'        => 'testimonials',
    'page_title'   => __('testimonials'),
    'current'      => 'testimonials',
    'has_slug'     => false,
    'has_active'   => true,
    'has_order'    => true,
    'order_by'     => 'sort_order, id',
    'upload_subdir'=> 'testimonials',
    'list_image'   => 'image',
    'title_column' => 'name',
    'list_columns' => [
        ['field' => 'rating',     'label' => __('rating')],
    ],
    'fields' => [
        ['name' => 'name',    'type' => 'text',     'bilingual' => true, 'required' => true, 'label_ar' => 'الاسم',    'label_en' => 'Name'],
        ['name' => 'role',    'type' => 'text',     'bilingual' => true,                     'label_ar' => 'الوظيفة',  'label_en' => 'Role'],
        ['name' => 'content', 'type' => 'textarea', 'bilingual' => true, 'rows' => 4,        'label_ar' => 'الرأي',    'label_en' => 'Testimonial'],
        ['name' => 'image',   'type' => 'image', 'label' => __('image')],
        ['name' => 'rating',  'type' => 'select', 'label' => __('rating'), 'options' => [5=>'★★★★★',4=>'★★★★☆',3=>'★★★☆☆',2=>'★★☆☆☆',1=>'★☆☆☆☆']],
    ],
]);
