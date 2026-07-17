<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../includes/helpers.php';

$db = Database::getConnection();
$slug = $_GET['slug'] ?? '';

if ($slug) {
    $stmt = $db->prepare("SELECT id, title_ne, title_en, slug, content_ne, content_en, excerpt_ne, excerpt_en, cover_image, tags, published_at FROM articles WHERE slug = :slug AND is_published = 1 AND published_at IS NOT NULL LIMIT 1");
    $stmt->execute([':slug' => $slug]);
    $article = $stmt->fetch();
    if (!$article) jsonError('Article not found', 404);
    if ($article['tags']) $article['tags'] = json_decode($article['tags'], true);
    jsonSuccess($article);
}

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = min(50, max(1, (int)($_GET['limit'] ?? 20)));
$offset = ($page - 1) * $limit;

$stmt = $db->prepare("SELECT id, title_ne, title_en, slug, excerpt_ne, excerpt_en, cover_image, published_at FROM articles WHERE is_published = 1 AND published_at IS NOT NULL ORDER BY published_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll();

$count = (int)$db->query("SELECT COUNT(*) FROM articles WHERE is_published = 1 AND published_at IS NOT NULL")->fetchColumn();

jsonSuccess([
    'articles' => $articles,
    'page' => $page,
    'limit' => $limit,
    'total' => $count,
    'total_pages' => ceil($count / $limit),
]);
