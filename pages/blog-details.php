<?php
// ─── Pull blog post from CMS (Arabic public site) ────────────────────────────
$L = '_ar';

$slug = isset($_GET['puid']) && $_GET['puid'] !== ''
    ? trim((string) $_GET['puid'])
    : (isset($_GET['slug']) ? trim((string) $_GET['slug']) : '');

// $post is usually pre-fetched in index.php; fall back to fetching here.
if (!isset($post) || !$post) {
    $post = null;
    if (isset($pdo) && $pdo instanceof PDO && $slug !== '') {
        $stmt = $pdo->prepare(
            "SELECT p.*, c.name$L AS cat_name, c.slug AS cat_slug, a.full_name AS author_name, a.avatar AS author_avatar
             FROM blog_posts p
             LEFT JOIN blog_categories c ON c.id = p.category_id
             LEFT JOIN admins a ON a.id = p.author_id
             WHERE p.slug = ? AND p.status = 'published'
             LIMIT 1"
        );
        $stmt->execute([$slug]);
        $post = $stmt->fetch();
    }
}

$recent = [];
$cats   = [];
$related = [];

if ($post) {
    $rel = $pdo->prepare(
        "SELECT slug, title$L AS title, featured_image, COALESCE(published_at, created_at) AS dt
         FROM blog_posts WHERE status = 'published' AND id <> ?
         AND (category_id = ? OR ? IS NULL) ORDER BY dt DESC LIMIT 3"
    );
    $rel->execute([$post['id'], $post['category_id'], $post['category_id']]);
    $related = $rel->fetchAll();

    $recent = $pdo->query(
        "SELECT slug, title$L AS title, featured_image, COALESCE(published_at, created_at) AS dt
         FROM blog_posts WHERE status = 'published' ORDER BY dt DESC LIMIT 4"
    )->fetchAll();

    $cats = $pdo->query(
        "SELECT c.id, c.slug, c.name$L AS name,
                (SELECT COUNT(*) FROM blog_posts p WHERE p.category_id = c.id AND p.status = 'published') AS posts_count
         FROM blog_categories c WHERE c.is_active = 1 ORDER BY sort_order, name$L"
    )->fetchAll();
}

$formatArDate = function ($dt) {
    if (!$dt) return '';
    $months_ar = ['', 'يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];
    $ts = strtotime((string) $dt);
    return (int) date('j', $ts) . ' ' . $months_ar[(int) date('n', $ts)] . ' ' . date('Y', $ts);
};

$shareUrl = (isset($currentURL) ? $currentURL : ($base_url . 'blog-details/' . ($slug ?: '')));
?>
<style>
.jpi-detail-hero { background: linear-gradient(135deg, #26282b 0%, #1a1c1f 100%); color: #fff; padding: 70px 0 50px; position: relative; overflow: hidden; }
.jpi-detail-hero::before { content: ''; position: absolute; inset: 0; background: radial-gradient(circle at 80% 20%, rgba(159,128,84,0.25), transparent 50%); pointer-events: none; }
.jpi-detail-hero .crumb { color: #ebcfa7; font-size: 13px; letter-spacing: .14em; text-transform: uppercase; margin-bottom: 14px; }
.jpi-detail-hero .crumb a { color: #ebcfa7; text-decoration: none; }
.jpi-detail-hero h1 { color: #fff; font-size: 38px; line-height: 1.3; max-width: 800px; margin: 0 auto 20px; font-weight: 700; }
.jpi-detail-hero .post-meta { display: flex; flex-wrap: wrap; justify-content: center; gap: 22px; color: rgba(255,255,255,0.75); font-size: 14px; }
.jpi-detail-hero .post-meta i { color: #c4a476; margin-inline-end: 6px; }
.jpi-detail-hero .cat-pill { display: inline-block; background: rgba(159,128,84,0.25); color: #ebcfa7; padding: 4px 14px; border-radius: 999px; font-size: 12px; font-weight: 700; letter-spacing: .04em; margin-bottom: 18px; }
.jpi-article-section { padding: 60px 0; background: #faf7f2; }
.jpi-article { background: #fff; border: 1px solid #e8e2d6; border-radius: 14px; padding: 36px; box-shadow: 0 8px 22px rgba(38,40,43,0.04); }
.jpi-article .featured-image { width: 100%; aspect-ratio: 16/9; object-fit: cover; border-radius: 10px; margin-bottom: 28px; }
.jpi-article-content { color: #2a2c30; font-size: 16px; line-height: 1.85; }
.jpi-article-content p { margin-bottom: 18px; }
.jpi-article-content h2 { font-size: 26px; color: #26282b; margin: 32px 0 14px; font-weight: 700; }
.jpi-article-content h3 { font-size: 21px; color: #26282b; margin: 26px 0 12px; font-weight: 700; }
.jpi-article-content blockquote { border-inline-start: 4px solid #9f8054; background: #faf7f2; padding: 16px 22px; margin: 24px 0; font-style: italic; color: #4a4d52; border-radius: 0 8px 8px 0; }
[dir="rtl"] .jpi-article-content blockquote { border-radius: 8px 0 0 8px; }
.jpi-article-content ul, .jpi-article-content ol { padding-inline-start: 22px; margin-bottom: 18px; }
.jpi-article-content ul li, .jpi-article-content ol li { margin-bottom: 8px; }
.jpi-article-content img { max-width: 100%; height: auto; border-radius: 8px; margin: 16px 0; }
.jpi-article-content a { color: #9f8054; text-decoration: underline; }
.jpi-article-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 28px; margin-top: 32px; border-top: 1px solid #e8e2d6; flex-wrap: wrap; gap: 18px; }
.jpi-share { display: flex; align-items: center; gap: 8px; }
.jpi-share span { font-weight: 700; color: #26282b; font-size: 13px; }
.jpi-share a { width: 36px; height: 36px; border-radius: 50%; background: #faf7f2; color: #26282b; display: inline-flex; align-items: center; justify-content: center; transition: all .2s; }
.jpi-share a:hover { background: #9f8054; color: #fff; }
.jpi-author-card { display: flex; align-items: center; gap: 14px; background: #faf7f2; padding: 18px; border-radius: 10px; margin-top: 28px; }
.jpi-author-card .avatar { width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, #9f8054, #c4a476); color: #fff; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 22px; }
.jpi-author-card strong { display: block; color: #26282b; font-size: 15px; }
.jpi-author-card small { color: #6c707a; font-size: 12px; }

.jpi-related { margin-top: 50px; }
.jpi-related h3 { color: #26282b; font-size: 22px; margin-bottom: 20px; font-weight: 700; padding-bottom: 10px; border-bottom: 2px solid #ebcfa7; }
.jpi-related-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 18px; }
.jpi-rel-card { background: #fff; border: 1px solid #e8e2d6; border-radius: 10px; overflow: hidden; transition: transform .2s; }
.jpi-rel-card:hover { transform: translateY(-3px); box-shadow: 0 10px 22px rgba(38,40,43,0.08); }
.jpi-rel-card .img { aspect-ratio: 16/10; overflow: hidden; }
.jpi-rel-card .img img { width: 100%; height: 100%; object-fit: cover; }
.jpi-rel-card .ph { aspect-ratio: 16/10; background: linear-gradient(135deg, #ebcfa7, #c4a476); display: flex; align-items: center; justify-content: center; color: #26282b; font-size: 32px; }
.jpi-rel-card .body { padding: 16px; }
.jpi-rel-card h4 { font-size: 15px; line-height: 1.5; margin: 0 0 6px; }
.jpi-rel-card h4 a { color: #26282b; text-decoration: none; font-weight: 700; }
.jpi-rel-card h4 a:hover { color: #9f8054; }
.jpi-rel-card time { font-size: 11px; color: #6c707a; }

.jpi-sidebar { position: sticky; top: 100px; }
.jpi-widget { background: #fff; border: 1px solid #e8e2d6; border-radius: 12px; padding: 22px; margin-bottom: 22px; }
.jpi-widget h4 { font-size: 16px; color: #26282b; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 2px solid #ebcfa7; font-weight: 700; }
.jpi-widget .cat-list { list-style: none; padding: 0; margin: 0; }
.jpi-widget .cat-list li { padding: 7px 0; border-bottom: 1px dashed #f0eada; }
.jpi-widget .cat-list li:last-child { border-bottom: 0; }
.jpi-widget .cat-list a { color: #26282b; font-size: 13px; text-decoration: none; display: flex; justify-content: space-between; }
.jpi-widget .cat-list a:hover { color: #9f8054; }
.jpi-widget .cat-list .count { background: #faf7f2; color: #6c707a; padding: 1px 9px; border-radius: 999px; font-size: 11px; }
.jpi-widget .recent-item { display: flex; gap: 12px; padding: 9px 0; border-bottom: 1px dashed #f0eada; }
.jpi-widget .recent-item:last-child { border-bottom: 0; }
.jpi-widget .recent-item img { width: 60px; height: 56px; object-fit: cover; border-radius: 6px; flex-shrink: 0; }
.jpi-widget .recent-item .ph { width: 60px; height: 56px; background: linear-gradient(135deg, #ebcfa7, #c4a476); border-radius: 6px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: #26282b; }
.jpi-widget .recent-item h5 { font-size: 13px; line-height: 1.5; margin: 0; font-weight: 600; }
.jpi-widget .recent-item h5 a { color: #26282b; text-decoration: none; }
.jpi-widget .recent-item h5 a:hover { color: #9f8054; }
.jpi-widget .recent-item time { font-size: 11px; color: #6c707a; }

@media (max-width: 768px){ .jpi-detail-hero h1 { font-size: 26px; } .jpi-article { padding: 22px; } }
</style>

<?php if (!$post): ?>
    <section class="jpi-article-section">
        <div class="container text-center" style="padding: 60px 20px;">
            <i class="fa-regular fa-newspaper" style="font-size: 64px; color: #c4a476; margin-bottom: 16px;"></i>
            <h2><?= 'المقال غير موجود' ?></h2>
            <p style="color: #6c707a;"><?= 'قد يكون قد تم حذف هذا المقال أو نقله.' ?></p>
            <a href="<?= htmlspecialchars($base_url . 'blog') ?>" class="btn" style="background: #26282b; color: #ebcfa7; padding: 10px 24px; border-radius: 6px; text-decoration: none; display: inline-block; margin-top: 14px;">
                <?= 'العودة للمدونة' ?>
            </a>
        </div>
    </section>
<?php else:
    $title  = $post['title' . $L];
    $body   = $post['content' . $L];
    $excerpt = $post['excerpt' . $L];
    // Override page meta for SEO
    $PageTitle         = $post['meta_title' . $L] ?: $title;
    $escapedDescription = htmlspecialchars(mb_substr(strip_tags($post['meta_description' . $L] ?: $excerpt ?: $title), 0, 160), ENT_QUOTES, 'UTF-8');
    $KeyWords          = htmlspecialchars($post['meta_keywords' . $L] ?: '', ENT_QUOTES, 'UTF-8');
?>

<section class="jpi-detail-hero">
    <div class="container text-center">
        <div class="crumb">
            <a href="<?= htmlspecialchars($base_url) ?>"><?= 'الرئيسية' ?></a>
            &nbsp; / &nbsp;
            <a href="<?= htmlspecialchars($base_url . 'blog') ?>"><?= 'المدونة' ?></a>
            &nbsp; / &nbsp; <?= htmlspecialchars(mb_strimwidth($title, 0, 50, '…')) ?>
        </div>
        <?php if (!empty($post['cat_name'])): ?>
            <span class="cat-pill"><?= htmlspecialchars($post['cat_name']) ?></span>
        <?php endif; ?>
        <h1><?= htmlspecialchars($title) ?></h1>
        <div class="post-meta">
            <span><i class="fa-regular fa-calendar"></i><?= htmlspecialchars($formatArDate($post['published_at'] ?: $post['created_at'])) ?></span>
            <?php if (!empty($post['author_name'])): ?>
                <span><i class="fa-regular fa-user"></i><?= htmlspecialchars($post['author_name']) ?></span>
            <?php endif; ?>
            <span><i class="fa-regular fa-eye"></i><?= (int) $post['views'] ?> <?= 'مشاهدة' ?></span>
        </div>
    </div>
</section>

<section class="jpi-article-section">
    <div class="container">
        <div class="row g-4">

            <div class="col-lg-8">
                <article class="jpi-article">
                    <?php if (!empty($post['featured_image'])): ?>
                        <img src="<?= htmlspecialchars($post['featured_image']) ?>" alt="<?= htmlspecialchars($title) ?>" class="featured-image">
                    <?php endif; ?>

                    <div class="jpi-article-content">
                        <?= $body /* trusted HTML from admin */ ?>
                    </div>

                    <div class="jpi-article-footer">
                        <div class="jpi-share">
                            <span><?= 'شارك:' ?></span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($shareUrl) ?>" target="_blank" rel="noopener" title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="https://twitter.com/intent/tweet?url=<?= urlencode($shareUrl) ?>&text=<?= urlencode($title) ?>" target="_blank" rel="noopener" title="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($shareUrl) ?>" target="_blank" rel="noopener" title="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                            <a href="https://wa.me/?text=<?= urlencode($title . ' — ' . $shareUrl) ?>" target="_blank" rel="noopener" title="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                            <a href="mailto:?subject=<?= urlencode($title) ?>&body=<?= urlencode($shareUrl) ?>" title="Email"><i class="fa-regular fa-envelope"></i></a>
                        </div>
                    </div>

                    <?php if (!empty($post['author_name'])): ?>
                        <div class="jpi-author-card">
                            <div class="avatar"><?= htmlspecialchars(mb_strtoupper(mb_substr((string) $post['author_name'], 0, 1))) ?></div>
                            <div>
                                <strong><?= htmlspecialchars($post['author_name']) ?></strong>
                                <small><?= 'محرر في JPI' ?></small>
                            </div>
                        </div>
                    <?php endif; ?>
                </article>

                <?php if (!empty($related)): ?>
                    <div class="jpi-related">
                        <h3><?= 'مقالات ذات صلة' ?></h3>
                        <div class="jpi-related-grid">
                            <?php foreach ($related as $r): ?>
                                <article class="jpi-rel-card">
                                    <a href="<?= htmlspecialchars($base_url . 'blog-details/' . $r['slug']) ?>">
                                        <?php if (!empty($r['featured_image'])): ?>
                                            <div class="img"><img src="<?= htmlspecialchars($r['featured_image']) ?>" alt=""></div>
                                        <?php else: ?>
                                            <div class="ph"><i class="fa-solid fa-scale-balanced"></i></div>
                                        <?php endif; ?>
                                    </a>
                                    <div class="body">
                                        <h4><a href="<?= htmlspecialchars($base_url . 'blog-details/' . $r['slug']) ?>"><?= htmlspecialchars(mb_strimwidth((string) $r['title'], 0, 70, '…')) ?></a></h4>
                                        <time><?= htmlspecialchars($formatArDate($r['dt'])) ?></time>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <aside class="col-lg-4">
                <div class="jpi-sidebar">
                    <?php if (!empty($cats)): ?>
                        <div class="jpi-widget">
                            <h4><i class="fa-solid fa-folder-tree"></i> &nbsp; <?= 'التصنيفات' ?></h4>
                            <ul class="cat-list">
                                <?php foreach ($cats as $c): ?>
                                    <li>
                                        <a href="<?= htmlspecialchars($base_url . 'blog?cat=' . (int) $c['id']) ?>">
                                            <?= htmlspecialchars($c['name']) ?>
                                            <span class="count"><?= (int) $c['posts_count'] ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($recent)): ?>
                        <div class="jpi-widget">
                            <h4><i class="fa-solid fa-clock"></i> &nbsp; <?= 'مقالات حديثة' ?></h4>
                            <?php foreach ($recent as $r): ?>
                                <div class="recent-item">
                                    <?php if (!empty($r['featured_image'])): ?>
                                        <img src="<?= htmlspecialchars($r['featured_image']) ?>" alt="">
                                    <?php else: ?>
                                        <div class="ph"><i class="fa-solid fa-scale-balanced"></i></div>
                                    <?php endif; ?>
                                    <div>
                                        <h5><a href="<?= htmlspecialchars($base_url . 'blog-details/' . $r['slug']) ?>"><?= htmlspecialchars(mb_strimwidth((string) $r['title'], 0, 60, '…')) ?></a></h5>
                                        <time><?= htmlspecialchars($formatArDate($r['dt'])) ?></time>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </aside>

        </div>
    </div>
</section>

<?php endif; ?>
