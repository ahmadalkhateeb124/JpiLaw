<?php
// ─── Pull blog data from CMS (Arabic public site) ────────────────────────────
$L = '_ar';

$page    = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 9;
$catId   = (int) ($_GET['cat'] ?? 0);
$q       = trim((string) ($_GET['q'] ?? ''));

$where = ["p.status = 'published'"];
$params = [];
if ($catId)    { $where[] = 'p.category_id = ?'; $params[] = $catId; }
if ($q !== '') { $where[] = "(p.title$L LIKE ? OR p.excerpt$L LIKE ?)"; $params[] = "%$q%"; $params[] = "%$q%"; }
$whereSql = 'WHERE ' . implode(' AND ', $where);

$total = 0;
$posts = [];
$cats  = [];
$recent = [];

if (isset($pdo) && $pdo instanceof PDO) {
    $totStmt = $pdo->prepare("SELECT COUNT(*) FROM blog_posts p $whereSql");
    $totStmt->execute($params);
    $total = (int) $totStmt->fetchColumn();

    $totalPages = max(1, (int) ceil($total / $perPage));
    $page       = min($page, $totalPages);
    $offset     = ($page - 1) * $perPage;

    $stmt = $pdo->prepare(
        "SELECT p.id, p.slug, p.title$L AS title, p.excerpt$L AS excerpt, p.featured_image,
                p.published_at, p.created_at, c.name$L AS cat_name, c.slug AS cat_slug
         FROM blog_posts p
         LEFT JOIN blog_categories c ON c.id = p.category_id
         $whereSql
         ORDER BY COALESCE(p.published_at, p.created_at) DESC
         LIMIT $perPage OFFSET $offset"
    );
    $stmt->execute($params);
    $posts = $stmt->fetchAll();

    $cats = $pdo->query(
        "SELECT c.id, c.slug, c.name$L AS name,
                (SELECT COUNT(*) FROM blog_posts p WHERE p.category_id = c.id AND p.status = 'published') AS posts_count
         FROM blog_categories c WHERE c.is_active = 1 ORDER BY sort_order, name$L"
    )->fetchAll();

    $recent = $pdo->query(
        "SELECT slug, title$L AS title, featured_image, COALESCE(published_at, created_at) AS dt
         FROM blog_posts WHERE status = 'published' ORDER BY dt DESC LIMIT 4"
    )->fetchAll();
} else {
    $totalPages = 1;
}

$formatArDate = function ($dt) {
    if (!$dt) return '';
    $months_ar = ['', 'يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];
    $ts = strtotime((string) $dt);
    return (int) date('j', $ts) . ' ' . $months_ar[(int) date('n', $ts)] . ' ' . date('Y', $ts);
};
?>
<style>
.blog-hero { background: linear-gradient(135deg, #26282b 0%, #1a1c1f 100%); color: #fff; padding: 90px 0 60px; position: relative; overflow: hidden; }
.blog-hero::before { content: ''; position: absolute; inset: 0; background: radial-gradient(circle at 80% 20%, rgba(159,128,84,0.25), transparent 50%); pointer-events: none; }
.blog-hero h1 { color: #fff; font-size: 48px; margin-bottom: 12px; font-weight: 700; }
.blog-hero p { color: rgba(255,255,255,0.7); font-size: 16px; max-width: 620px; margin: 0 auto; }
.blog-hero .crumb { color: #ebcfa7; font-size: 13px; letter-spacing: .14em; text-transform: uppercase; margin-bottom: 16px; }
.jpi-blog-section { padding: 70px 0; background: #faf7f2; }
.jpi-blog-card { background: #fff; border: 1px solid #e8e2d6; border-radius: 12px; overflow: hidden; transition: transform .25s, box-shadow .25s; height: 100%; display: flex; flex-direction: column; }
.jpi-blog-card:hover { transform: translateY(-4px); box-shadow: 0 14px 28px rgba(38,40,43,0.10); }
.jpi-blog-card .img-wrap { position: relative; aspect-ratio: 16/10; overflow: hidden; }
.jpi-blog-card .img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform .35s; }
.jpi-blog-card:hover .img-wrap img { transform: scale(1.06); }
.jpi-blog-card .img-wrap .placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #ebcfa7, #c4a476); color: #26282b; font-size: 42px; }
.jpi-blog-card .body { padding: 22px; flex: 1; display: flex; flex-direction: column; }
.jpi-blog-card .cat { display: inline-block; background: rgba(159,128,84,0.12); color: #84693f; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 999px; margin-bottom: 10px; letter-spacing: .04em; }
.jpi-blog-card h3 { font-size: 19px; font-weight: 700; color: #26282b; margin-bottom: 8px; line-height: 1.45; }
.jpi-blog-card h3 a { color: inherit; text-decoration: none; }
.jpi-blog-card h3 a:hover { color: #9f8054; }
.jpi-blog-card .excerpt { color: #6c707a; font-size: 14px; line-height: 1.65; margin-bottom: 16px; flex: 1; }
.jpi-blog-card .meta { font-size: 12px; color: #6c707a; display: flex; align-items: center; gap: 12px; padding-top: 12px; border-top: 1px solid #f0eada; }
.jpi-blog-card .meta i { color: #9f8054; }
.jpi-blog-card .read-more { display: inline-flex; align-items: center; gap: 6px; color: #9f8054; font-weight: 700; font-size: 13px; margin-top: 10px; text-decoration: none; }
.jpi-blog-card .read-more:hover { color: #26282b; }
.jpi-sidebar { position: sticky; top: 100px; }
.jpi-widget { background: #fff; border: 1px solid #e8e2d6; border-radius: 12px; padding: 24px; margin-bottom: 24px; }
.jpi-widget h4 { font-size: 16px; color: #26282b; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 2px solid #ebcfa7; font-weight: 700; }
.jpi-widget .search-box { display: flex; gap: 0; }
.jpi-widget .search-box input { flex: 1; padding: 10px 14px; border: 1px solid #e8e2d6; border-radius: 6px 0 0 6px; font-family: inherit; }
.jpi-widget .search-box button { padding: 10px 16px; background: #26282b; color: #ebcfa7; border: 0; border-radius: 0 6px 6px 0; cursor: pointer; }
[dir="rtl"] .jpi-widget .search-box input { border-radius: 0 6px 6px 0; }
[dir="rtl"] .jpi-widget .search-box button { border-radius: 6px 0 0 6px; }
.jpi-widget .cat-list { list-style: none; padding: 0; margin: 0; }
.jpi-widget .cat-list li { padding: 8px 0; border-bottom: 1px dashed #f0eada; }
.jpi-widget .cat-list li:last-child { border-bottom: 0; }
.jpi-widget .cat-list a { color: #26282b; font-size: 14px; text-decoration: none; display: flex; justify-content: space-between; }
.jpi-widget .cat-list a:hover { color: #9f8054; }
.jpi-widget .cat-list .count { background: #faf7f2; color: #6c707a; padding: 1px 9px; border-radius: 999px; font-size: 11px; }
.jpi-widget .recent-item { display: flex; gap: 12px; padding: 10px 0; border-bottom: 1px dashed #f0eada; }
.jpi-widget .recent-item:last-child { border-bottom: 0; }
.jpi-widget .recent-item img { width: 64px; height: 60px; object-fit: cover; border-radius: 6px; flex-shrink: 0; }
.jpi-widget .recent-item .placeholder { width: 64px; height: 60px; background: linear-gradient(135deg, #ebcfa7, #c4a476); border-radius: 6px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; color: #26282b; }
.jpi-widget .recent-item h5 { font-size: 13px; line-height: 1.5; margin: 0; font-weight: 600; }
.jpi-widget .recent-item h5 a { color: #26282b; text-decoration: none; }
.jpi-widget .recent-item h5 a:hover { color: #9f8054; }
.jpi-widget .recent-item time { font-size: 11px; color: #6c707a; }
.jpi-pagination { display: flex; justify-content: center; gap: 6px; margin-top: 40px; }
.jpi-pagination a, .jpi-pagination span { padding: 9px 14px; border: 1px solid #e8e2d6; border-radius: 6px; color: #26282b; text-decoration: none; font-size: 14px; font-weight: 600; }
.jpi-pagination .current { background: #26282b; color: #ebcfa7; border-color: #26282b; }
.jpi-pagination a:hover { background: #faf7f2; color: #9f8054; }
.jpi-empty { text-align: center; padding: 60px 20px; color: #6c707a; }
.jpi-empty i { font-size: 56px; color: #c4a476; margin-bottom: 12px; }
@media (max-width: 768px){ .blog-hero h1 { font-size: 32px; } }
</style>

<section class="blog-hero">
    <div class="container text-center">
        <div class="crumb">
            <a href="<?= htmlspecialchars($base_url) ?>" style="color: #ebcfa7; text-decoration: none;"><?= 'الرئيسية' ?></a>
            &nbsp; / &nbsp; <?= 'المدونة' ?>
        </div>
        <h1><?= 'مدوّنتنا القانونية' ?></h1>
        <p><?= 'مقالات ونصائح وتحليلات قانونية محدّثة من فريق محامي JPI في الأردن وفلسطين.' ?></p>
    </div>
</section>

<section class="jpi-blog-section">
    <div class="container">
        <div class="row g-4">

            <div class="col-lg-8">
                <?php if (empty($posts)): ?>
                    <div class="jpi-empty">
                        <i class="fa-regular fa-newspaper" style="display:block;"></i>
                        <h3><?= 'لا توجد مقالات بعد' ?></h3>
                        <p><?= 'تابعنا — سنشارك مقالاتنا قريباً.' ?></p>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($posts as $p): ?>
                            <div class="col-sm-6">
                                <article class="jpi-blog-card">
                                    <a href="<?= htmlspecialchars($base_url . 'blog-details/' . $p['slug']) ?>" class="img-wrap">
                                        <?php if (!empty($p['featured_image'])): ?>
                                            <img src="<?= htmlspecialchars($p['featured_image']) ?>" alt="<?= htmlspecialchars($p['title']) ?>">
                                        <?php else: ?>
                                            <div class="placeholder"><i class="fa-solid fa-scale-balanced"></i></div>
                                        <?php endif; ?>
                                    </a>
                                    <div class="body">
                                        <?php if (!empty($p['cat_name'])): ?>
                                            <span class="cat"><?= htmlspecialchars($p['cat_name']) ?></span>
                                        <?php endif; ?>
                                        <h3><a href="<?= htmlspecialchars($base_url . 'blog-details/' . $p['slug']) ?>"><?= htmlspecialchars($p['title']) ?></a></h3>
                                        <?php if (!empty($p['excerpt'])): ?>
                                            <p class="excerpt"><?= htmlspecialchars(mb_strimwidth((string) $p['excerpt'], 0, 130, '…')) ?></p>
                                        <?php endif; ?>
                                        <div class="meta">
                                            <span><i class="fa-regular fa-calendar"></i> <?= htmlspecialchars($formatArDate($p['published_at'] ?? $p['created_at'])) ?></span>
                                        </div>
                                        <a href="<?= htmlspecialchars($base_url . 'blog-details/' . $p['slug']) ?>" class="read-more">
                                            <?= 'اقرأ المزيد' ?>
                                            <i class="fa-solid <?= 'fa-arrow-left' ?>"></i>
                                        </a>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ($totalPages > 1): ?>
                        <nav class="jpi-pagination">
                            <?php for ($i = 1; $i <= $totalPages; $i++):
                                $qs = http_build_query(array_merge($_GET, ['page' => $i])); ?>
                                <a href="?<?= htmlspecialchars($qs) ?>" class="<?= $i === $page ? 'current' : '' ?>"><?= $i ?></a>
                            <?php endfor; ?>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <aside class="col-lg-4">
                <div class="jpi-sidebar">
                    <div class="jpi-widget">
                        <h4><i class="fa-solid fa-magnifying-glass"></i> &nbsp; <?= 'بحث' ?></h4>
                        <form class="search-box" method="get" action="">
                            <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="<?= 'ابحث في المدونة...' ?>">
                            <button type="submit"><i class="fa-solid fa-search"></i></button>
                        </form>
                    </div>

                    <?php if (!empty($cats)): ?>
                        <div class="jpi-widget">
                            <h4><i class="fa-solid fa-folder-tree"></i> &nbsp; <?= 'التصنيفات' ?></h4>
                            <ul class="cat-list">
                                <li><a href="?"><?= 'كل المقالات' ?> <span class="count"><?= (int) $total ?></span></a></li>
                                <?php foreach ($cats as $c): ?>
                                    <li>
                                        <a href="?cat=<?= (int) $c['id'] ?>">
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
                                        <div class="placeholder"><i class="fa-solid fa-scale-balanced"></i></div>
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
