<?php
require_once 'includes/functions.php';

$slug = $_GET['slug'] ?? null;
if (!$slug) {
    header('Location: blog.php');
    exit;
}

$story = getStoryBySlug($slug);
if (!$story) {
    header('Location: blog.php');
    exit;
}

// Increment view count
incrementViews($story['id']);

$page_title = htmlspecialchars($story['title']) . ' - ' . SITE_NAME;
$page_description = truncate($story['excerpt'] ?? strip_tags($story['content']), 160);

require_once 'includes/header.php';

// Get related stories from same category
$related_stories = [];
if ($story['category_id']) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM stories
                          WHERE category_id = :category_id
                          AND id != :id
                          AND status = 'published'
                          ORDER BY RAND()
                          LIMIT 3");
    $stmt->execute([
        'category_id' => $story['category_id'],
        'id' => $story['id']
    ]);
    $related_stories = $stmt->fetchAll();
}
?>

<article class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="text-sm text-gray-600 mb-6">
            <a href="<?php echo SITE_URL; ?>" class="hover:text-blue-600">Home</a>
            <i class="fas fa-chevron-right mx-2 text-xs"></i>
            <a href="<?php echo SITE_URL; ?>/blog.php" class="hover:text-blue-600">Blog</a>
            <?php if ($story['category_name']): ?>
                <i class="fas fa-chevron-right mx-2 text-xs"></i>
                <a href="<?php echo SITE_URL; ?>/blog.php?category=<?php echo $story['category_id']; ?>" class="hover:text-blue-600">
                    <?php echo htmlspecialchars($story['category_name']); ?>
                </a>
            <?php endif; ?>
        </nav>

        <!-- Article Header -->
        <header class="mb-8">
            <?php if ($story['category_name']): ?>
                <a href="<?php echo SITE_URL; ?>/blog.php?category=<?php echo $story['category_id']; ?>"
                   class="inline-block bg-blue-600 text-white text-sm px-4 py-1 rounded-full mb-4 hover:bg-blue-700">
                    <?php echo htmlspecialchars($story['category_name']); ?>
                </a>
            <?php endif; ?>
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                <?php echo htmlspecialchars($story['title']); ?>
            </h1>
            <?php if ($story['excerpt']): ?>
                <p class="text-xl text-gray-600 mb-6">
                    <?php echo htmlspecialchars($story['excerpt']); ?>
                </p>
            <?php endif; ?>
            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                <div class="flex items-center">
                    <i class="far fa-user mr-2"></i>
                    <span>By <?php echo htmlspecialchars($story['author_name']); ?></span>
                </div>
                <div class="flex items-center">
                    <i class="far fa-calendar mr-2"></i>
                    <span><?php echo formatDate($story['published_at'] ?? $story['created_at']); ?></span>
                </div>
                <div class="flex items-center">
                    <i class="far fa-clock mr-2"></i>
                    <span><?php echo ceil(str_word_count(strip_tags($story['content'])) / 200); ?> min read</span>
                </div>
                <div class="flex items-center">
                    <i class="far fa-eye mr-2"></i>
                    <span><?php echo number_format($story['views']); ?> views</span>
                </div>
            </div>
        </header>

        <!-- Featured Image -->
        <?php if ($story['image']): ?>
            <div class="mb-8">
                <img src="<?php echo UPLOAD_URL . $story['image']; ?>"
                     alt="<?php echo htmlspecialchars($story['title']); ?>"
                     class="w-full rounded-lg shadow-lg">
            </div>
        <?php endif; ?>

        <!-- Article Content -->
        <div class="prose prose-lg max-w-none mb-12">
            <div class="bg-white rounded-lg shadow-md p-8">
                <?php echo nl2br(htmlspecialchars($story['content'])); ?>
            </div>
        </div>

        <!-- Share Buttons -->
        <div class="bg-gray-50 rounded-lg p-6 mb-12">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Share this story</h3>
            <div class="flex flex-wrap gap-3">
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . '/post.php?slug=' . $story['slug']); ?>"
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fab fa-facebook mr-2"></i> Facebook
                </a>
                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(SITE_URL . '/post.php?slug=' . $story['slug']); ?>&text=<?php echo urlencode($story['title']); ?>"
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600">
                    <i class="fab fa-twitter mr-2"></i> Twitter
                </a>
                <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(SITE_URL . '/post.php?slug=' . $story['slug']); ?>"
                   target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800">
                    <i class="fab fa-linkedin mr-2"></i> LinkedIn
                </a>
                <button onclick="copyToClipboard()"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    <i class="fas fa-link mr-2"></i> Copy Link
                </button>
            </div>
        </div>

        <!-- Related Stories -->
        <?php if (!empty($related_stories)): ?>
            <section class="mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Related Stories</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <?php foreach ($related_stories as $related): ?>
                    <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                        <?php if ($related['image']): ?>
                            <a href="<?php echo SITE_URL; ?>/post.php?slug=<?php echo $related['slug']; ?>">
                                <img src="<?php echo UPLOAD_URL . $related['image']; ?>"
                                     alt="<?php echo htmlspecialchars($related['title']); ?>"
                                     class="w-full h-40 object-cover">
                            </a>
                        <?php else: ?>
                            <div class="w-full h-40 bg-gradient-to-r from-blue-400 to-purple-400"></div>
                        <?php endif; ?>
                        <div class="p-4">
                            <h3 class="text-lg font-bold text-gray-900 mb-2">
                                <a href="<?php echo SITE_URL; ?>/post.php?slug=<?php echo $related['slug']; ?>" class="hover:text-blue-600">
                                    <?php echo htmlspecialchars($related['title']); ?>
                                </a>
                            </h3>
                            <p class="text-sm text-gray-600 mb-3">
                                <?php echo truncate($related['excerpt'] ?? strip_tags($related['content']), 80); ?>
                            </p>
                            <a href="<?php echo SITE_URL; ?>/post.php?slug=<?php echo $related['slug']; ?>"
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Read More <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
</article>

<script>
function copyToClipboard() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(function() {
        alert('Link copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}
</script>

<?php require_once 'includes/footer.php'; ?>
