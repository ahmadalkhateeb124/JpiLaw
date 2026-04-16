<?php
require_once("header-ini.php");
// الحصول على الصفحة الحالية من URL
$current_page = basename($_SERVER['REQUEST_URI'], ".php");  // هذا يقوم بإزالة امتداد ".php" من اسم الصفحة


?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>

    <meta name="google-site-verification" content="UL6xxRmXMXrrvvNtvdbYgpnMPILoV48c5ID2cIIOckM" />

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $PageTitle; ?></title>

    <!-- الوصف -->
    <meta name="description" content="<?php echo $escapedDescription; ?>">

    <!-- الكلمات المفتاحية -->
    <meta name="keywords" content="<?php echo $KeyWords; ?>">

    <!-- تحسين محركات البحث (SEO) -->
    <meta name="robots" content="index, follow">


    <meta property="og:title" content="<?php echo $PageTitle; ?>">
    <meta property="og:description" content="<?php echo $escapedDescription; ?>">
    <meta property="og:type" content="website">
    <meta property="og:image" content="https://jpilawfirm.com/assets/favicon.ico">
    <meta property="og:url" content="https://jpilawfirm.com">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="https://jpilawfirm.com/assets/favicon.ico">
    <link rel="shortcut icon" href="https://jpilawfirm.com/assets/favicon.ico" type="image/x-icon">



    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo $PageTitle; ?>">
    <meta name="twitter:description" content="<?php echo $escapedDescription; ?>">
    <meta name="twitter:image" content="https://jpilawfirm.com/assets/favicon.ico">



    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= $base_url ?>assets/css/bootstrap.rtl.min.css">
    <!-- Meanmenu CSS -->
    <link rel="stylesheet" href="<?= $base_url ?>assets/css/meanmenu.css">
    <!-- Icofont CSS -->
    <link rel="stylesheet" href="<?= $base_url ?>assets/css/icofont.min.css">
    <!-- Nice Select CSS -->
    <link rel="stylesheet" href="<?= $base_url ?>assets/css/nice-select.min.css">
    <!-- Owl Carousel CSS -->
    <link rel="stylesheet" href="<?= $base_url ?>assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="<?= $base_url ?>assets/css/owl.theme.default.min.css">
    <!-- Magnific Popup CSS -->
    <link rel="stylesheet" href="<?= $base_url ?>assets/css/magnific-popup.min.css">
    <!-- Flaticon CSS -->
    <link rel="stylesheet" href="<?= $base_url ?>assets/fonts/flaticon.css">
    <!-- Animate CSS -->
    <link rel="stylesheet" href="<?= $base_url ?>assets/css/animate.min.css">
    <!-- Odometer CSS -->
    <link rel="stylesheet" href="<?= $base_url ?>assets/css/odometer.min.css">
    <!-- Style CSS -->
    <link rel="stylesheet" href="<?= $base_url ?>assets/css/style.css">
    <!-- Responsive CSS -->
    <link rel="stylesheet" href="<?= $base_url ?>assets/css/responsive.css">
    <!-- Theme Dark CSS -->
    <link rel="stylesheet" href="<?= $base_url ?>assets/css/theme-dark.css">
    <!-- RTL CSS -->
    <link rel="stylesheet" href="<?= $base_url ?>assets/css/rtl.css">

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

</head>

<style>
    /* تنسيق الأزرار */
    .contact-buttons {
        position: fixed;
        right: 20px;
        bottom: 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        z-index: 12;
    }

    .contact-btnn {
        background-color: #007bff;
        color: white;
        padding: 15px;
        border-radius: 50%;
        /* جعل الأزرار دائرية */
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-size: 20px;
        /* حجم الأيقونات */
        width: 50px;
        /* عرض الدائرة */
        height: 50px;
        /* ارتفاع الدائرة */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        transition: background-color 0.3s;
    }

    .contact-btnn:hover {
        color: #fff;
    }

    .contact-btnn:focus {
        color: #fff;
    }

    .contact-btnn i {
        font-size: 24px;
        /* حجم الأيقونات */
    }

    /* تخصيص الألوان للأيقونات */
    .whatsapp-btn {
        background-color: #86714d;
        /* لون الواتساب */
    }

    .phone-btn {
        background-color: #998056;
        /* لون الهاتف */
    }

    .email-btn {
        background-color: #66573e;
        /* لون الإيميل */
    }
</style>

<body>

    <div class="contact-buttons">
        <a href="tel:+962796872442" class="contact-btnn  email-btn">
            <i class='bx bx-phone'></i>
        </a>
        <a href="https://wa.me/962796872442" class="contact-btnn  whatsapp-btn" target="_blank">
            <i class='bx bxl-whatsapp'></i>
        </a>

        <a href="mailto:info@jpilawfirm.com" class="contact-btnn  email-btn">
            <i class='bx bx-envelope'></i> </a>

        <a href="https://maps.app.goo.gl/98v9sMXWHRgZiNd59" class="contact-btnn  whatsapp-btn" target="_blank">
            <i class='bx bx-map'></i>
        </a>

    </div>

    <!-- Preloader -->
    <div class="loader">
        <div class="d-table">
            <div class="d-table-cell">
                <div class="sk-folding-cube">
                    <div class="sk-cube1 sk-cube"></div>
                    <div class="sk-cube2 sk-cube"></div>
                    <div class="sk-cube4 sk-cube"></div>
                    <div class="sk-cube3 sk-cube"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Preloader -->

    <!-- Navbar -->
    <div class="navbar-area fixed-top">
        <!-- Menu For Mobile Device -->
        <div class="mobile-nav">
            <a href="Home" class="logo">
                <img src="assets/img/logo.png" alt="Logo">
            </a>
        </div>
        <!-- Menu For Mobile Device -->
        <div class="main-nav">
            <div class="container">
                <nav class="navbar navbar-expand-md navbar-light">
                    <a class="navbar-brand" href="<?= $base_url ?>Home">
                        <img src="assets/img/logo.png" alt="Logo">
                    </a>
                    <div class="collapse navbar-collapse mean-menu" id="navbarSupportedContent">

                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a href="<?= $base_url ?>Home" class="nav-link <?= $current_page == 'Home' || $current_page == '' ? 'active' : '' ?>">الصفحة الرئيسية</a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= $base_url ?>about" class="nav-link <?= $current_page == 'about' ? 'active' : '' ?>">المزيد عنا</a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= $base_url ?>services" class="nav-link <?= $current_page == 'services' ? 'active' : '' ?>">الخدمات</a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= $base_url ?>practice" class="nav-link <?= $current_page == 'practice' ? 'active' : '' ?>">مجالات الممارسة</a>
                            </li>
                            <li class="nav-item dropdown <?= in_array($current_page, ['practice-details', 'appointment', 'testimonial', 'faq', 'privacy-policy', 'terms-conditions']) ? 'active' : '' ?>">
                                <a href="#" class="nav-link dropdown-toggle">الصفحات</a>
                                <ul class="dropdown-menu">

                                    <li class="nav-item">
                                        <a href="<?= $base_url ?>appointment" class="nav-link <?= $current_page == 'appointment' ? 'active' : '' ?>">حجز موعد</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= $base_url ?>certificates" class="nav-link <?= $current_page == 'certificates' ? 'active' : '' ?>">المؤهلات الأكاديمية للمحامية أسيل أبو ساره</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= $base_url ?>certificatess" class="nav-link <?= $current_page == 'certificatess' ? 'active' : '' ?>">المؤهلات الأكاديمية للمحامي مجد الاحمد</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= $base_url ?>faq" class="nav-link <?= $current_page == 'faq' ? 'active' : '' ?>">الأسئلة الشائعة</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= $base_url ?>privacy-policy" class="nav-link <?= $current_page == 'privacy-policy' ? 'active' : '' ?>">سياسة الخصوصية</a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="<?= $base_url ?>terms-conditions" class="nav-link <?= $current_page == 'terms-conditions' ? 'active' : '' ?>">الشروط والأحكام</a>
                                    </li>

                                </ul>
                            </li>
                            <li class="nav-item">
                                <a href="<?= $base_url ?>blog" class="nav-link <?= $current_page == 'blog' ? 'active' : '' ?>">المدونة</a>
                            </li>
                            <li class="nav-item">
                                <a href="<?= $base_url ?>contact" class="nav-link <?= $current_page == 'contact' ? 'active' : '' ?>">التواصل</a>
                            </li>
                        </ul>


                        <div class="side-nav">
                            <a href="<?= $base_url ?>contact">احجز موعدًا</a>
                        </div>

                    </div>
                </nav>
            </div>
        </div>
    </div>
    <!-- End Navbar -->


    <!-- Menu For Desktop Device -->
    <div class="main-nav">
        <div class="container">
            <nav class="navbar navbar-expand-md navbar-light">
                <a class="navbar-brand" href="index.html">
                    <img src="assets/img/logo.png" alt="Logo">
                </a>
                <div class="collapse navbar-collapse mean-menu" id="navbarSupportedContent">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="<?= $base_url ?>Home" class="nav-link <?= $current_page == 'Home' || $current_page == '' ? 'active' : '' ?>">الصفحة الرئيسية</a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>about" class="nav-link <?= $current_page == 'about' ? 'active' : '' ?>">المزيد عنا</a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>services" class="nav-link <?= $current_page == 'services' ? 'active' : '' ?>">الخدمات</a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>practice" class="nav-link <?= $current_page == 'practice' ? 'active' : '' ?>">مجالات الممارسة</a>
                        </li>
                        <li class="nav-item dropdown <?= in_array($current_page, ['practice-details', 'appointment', 'testimonial', 'faq', 'privacy-policy', 'terms-conditions']) ? 'active' : '' ?>">
                            <a href="#" class="nav-link dropdown-toggle">الصفحات</a>
                            <ul class="dropdown-menu">
                                <li class="nav-item">
                                    <a href="<?= $base_url ?>practice-details" class="nav-link <?= $current_page == 'practice-details' ? 'active' : '' ?>">تفاصيل المجال</a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= $base_url ?>appointment" class="nav-link <?= $current_page == 'appointment' ? 'active' : '' ?>">حجز موعد</a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= $base_url ?>testimonial" class="nav-link <?= $current_page == 'testimonial' ? 'active' : '' ?>">آراء العملاء</a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= $base_url ?>faq" class="nav-link <?= $current_page == 'faq' ? 'active' : '' ?>">الأسئلة الشائعة</a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= $base_url ?>privacy-policy" class="nav-link <?= $current_page == 'privacy-policy' ? 'active' : '' ?>">سياسة الخصوصية</a>
                                </li>
                                <li class="nav-item">
                                    <a href="<?= $base_url ?>terms-conditions" class="nav-link <?= $current_page == 'terms-conditions' ? 'active' : '' ?>">الشروط والأحكام</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>blog" class="nav-link <?= $current_page == 'blog' ? 'active' : '' ?>">المدونة</a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= $base_url ?>contact" class="nav-link <?= $current_page == 'contact' ? 'active' : '' ?>">التواصل</a>
                        </li>
                    </ul>

                    <div class="side-nav">
                        <a href="<?= $base_url ?>contact">احجز موعدًا</a>
                    </div>

                </div>
            </nav>
        </div>
    </div>
    </div>
    <!-- End Navbar -->