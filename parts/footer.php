        <!-- Footer -->
        <footer dir="rtl" style="text-align: right; ">
            <!-- النشرة البريدية -->


            <div class="container ">
                <div class="row justify-content-center ">
                    <div class="col-sm-6 col-lg-4 mt-5">
                        <div class="footer-item">
                            <div class="footer-logo">
                                <a href="Home">
                                    <img src="assets/img/logo.png" alt="Logo">
                                </a>
                                <p>فريقنا من المحامين دائمًا جاهز لتقديم أفضل الخدمات بكفاءة عالية. إذا كنت ترغب في معرفة المزيد عن شركتنا، لا تتردد في التواصل معنا.</p>
                                <ul>

                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3 mt-5">
                        <div class="footer-item">
                            <div class="footer-service">
                                <h3>خدماتنا</h3>
                                <ul>
                                    <li><a href="#"><i class="icofont-simple-left"></i> القانون الأسري</a></li>
                                    <li><a href="#"><i class="icofont-simple-left"></i> قانون التعليم</a></li>
                                    <li><a href="#"><i class="icofont-simple-left"></i> القانون المدني</a></li>
                                    <li><a href="#"><i class="icofont-simple-left"></i> القانون الجنائي</a></li>
                                    <li><a href="#"><i class="icofont-simple-left"></i> قانون الأعمال</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-2 mt-5">
                        <div class="footer-item">
                            <div class="footer-service">
                                <h3>روابط سريعة</h3>
                                <ul>
                                    <li><a href="<?= $base_url ?>Home" class="nav-link <?= $current_page == 'Home' || $current_page == '' ? 'active' : '' ?>"><i class="icofont-simple-left"></i> الرئيسية</a></li>
                                    <li><a href="<?= $base_url ?>about" class="nav-link <?= $current_page == 'about' ? 'active' : '' ?>"><i class="icofont-simple-left"></i> من نحن</a></li>
                                    <li><a href="<?= $base_url ?>blog" class="nav-link <?= $current_page == 'blog' ? 'active' : '' ?>"><i class="icofont-simple-left"></i> المدونة</a></li>
                                    <li><a href="<?= $base_url ?>contact" class="nav-link <?= $current_page == 'contact' ? 'active' : '' ?>"><i class="icofont-simple-left"></i> التواصل</a></li>
                                    <li><a href="<?= $base_url ?>appointment" class="nav-link <?= $current_page == 'appointment' ? 'active' : '' ?>"><i class="icofont-simple-left"></i>حجز موعد </a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <?php
                        $_fAddrJo  = site_setting('address',           'الأردن - عمّان - شارع المدينة المنورة');
                        $_fAddrPs  = site_setting('address_palestine', 'فلسطين - رام الله - ميدان المنارة');
                        $_fPhone1  = site_setting('contact_phone',     '+962 79 628 6204');
                        $_fPhone2  = site_setting('contact_whatsapp',  '+962 79 687 2442');
                        $_fEmail   = site_setting('contact_email',     'info@jpilawfirm.com');
                        $_socials  = [
                            'facebook'  => site_setting('social_facebook'),
                            'twitter'   => site_setting('social_twitter'),
                            'instagram' => site_setting('social_instagram'),
                            'linkedin'  => site_setting('social_linkedin'),
                            'youtube'   => site_setting('social_youtube'),
                        ];
                    ?>
                    <div class="col-sm-6 col-lg-3 mt-5">
                        <div class="footer-item">
                            <div class="footer-find">
                                <h3>تواصل معنا</h3>
                                <ul>
                                    <li><i class="icofont-location-pin"></i> <?= htmlspecialchars($_fAddrPs) ?></li>
                                    <li><i class="icofont-location-pin"></i> <?= htmlspecialchars($_fAddrJo) ?></li>
                                    <li dir="ltr"><i class="icofont-ui-call"></i> <a href="<?= htmlspecialchars(tel_link($_fPhone1)) ?>"><?= htmlspecialchars($_fPhone1) ?></a></li>
                                    <li dir="ltr"><i class="icofont-ui-call"></i> <a href="<?= htmlspecialchars(tel_link($_fPhone2)) ?>"><?= htmlspecialchars($_fPhone2) ?></a></li>
                                    <li><i class="icofont-at"></i> <a href="mailto:<?= htmlspecialchars($_fEmail) ?>"><?= htmlspecialchars($_fEmail) ?></a></li>
                                </ul>
                                <?php if (array_filter($_socials)): ?>
                                    <ul style="display: flex; gap: 8px; margin-top: 14px; padding: 0; list-style: none;">
                                        <?php foreach ($_socials as $name => $url): if (!$url) continue; ?>
                                            <li>
                                                <a href="<?= htmlspecialchars($url) ?>" target="_blank" rel="noopener" aria-label="<?= htmlspecialchars($name) ?>"
                                                   style="width: 34px; height: 34px; border-radius: 50%; background: rgba(255,255,255,0.08); color: #fff; display: flex; align-items: center; justify-content: center; transition: background .2s;">
                                                    <i class="fa-brands fa-<?= $name === 'twitter' ? 'x-twitter' : htmlspecialchars($name) ?>"></i>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="copyright-area mt-5">
                    <div class="row justify-content-center">
                        <div class="col-sm-7 col-lg-6">
                            <div class="copyright-item">
                                <p>جميع الحقوق محفوظة لمكتب المحاماة © jpilawfirm</p> <small>صُمّم بواسطة
                                    <a href="https://Webkoit.com/" target="_blank">Webkoit</a>
                                </small>
                            </div>
                        </div>
                        <div class="col-sm-5 col-lg-6">
                            <div class="copyright-item copyright-right">
                                <a href="terms-conditions" target="_blank">الشروط والأحكام</a> <span>-</span>
                                <a href="privacy-policy" target="_blank">سياسة الخصوصية</a>
                                <br>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </footer>

        <!-- End Footer -->


        <!-- Essential JS -->
        <script src="<?= $base_url ?>assets/js/jquery.min.js"></script>
        <script src="<?= $base_url ?>assets/js/bootstrap.bundle.min.js"></script>
        <!-- Meanmenu JS -->
        <script src="<?= $base_url ?>assets/js/jquery.meanmenu.js"></script>
        <!-- Nice Select JS -->
        <script src="<?= $base_url ?>assets/js/jquery.nice-select.min.js"></script>
        <!-- Form Ajaxchimp JS -->
        <script src="<?= $base_url ?>assets/js/jquery.ajaxchimp.min.js"></script>
        <!-- Form Validator JS -->
        <script src="<?= $base_url ?>assets/js/form-validator.min.js"></script>
        <!-- Contact JS -->
        <script src="<?= $base_url ?>assets/js/contact-form-script.js"></script>
        <!-- Owl Carousel JS -->
        <script src="<?= $base_url ?>assets/js/owl.carousel.min.js"></script>
        <!-- Odometer JS -->
        <script src="<?= $base_url ?>assets/js/odometer.min.js"></script>
        <script src="<?= $base_url ?>assets/js/jquery.appear.min.js"></script>
        <!-- Magnific Popup JS -->
        <script src="<?= $base_url ?>assets/js/jquery.magnific-popup.min.js"></script>
        <!-- animate__animated animate__JS -->
        <script src="<?= $base_url ?>assets/js/wow.min.js"></script>
        <!-- Custom JS -->
        <script src="<?= $base_url ?>assets/js/custom.js"></script>

        </body>

        </html>