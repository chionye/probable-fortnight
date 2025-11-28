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

<article class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Article Header -->
        <header class="mb-8">
            <?php if ($story['category_name']): ?>
                <a href="<?php echo SITE_URL; ?>/blog.php?category=<?php echo $story['category_id']; ?>"
                   class="category-label inline-block mb-4">
                    <?php echo htmlspecialchars($story['category_name']); ?>
                </a>
            <?php endif; ?>

            <h1 class="article-title mb-6">
                <?php echo htmlspecialchars($story['title']); ?>
            </h1>

            <?php if ($story['excerpt']): ?>
                <p class="text-xl leading-relaxed text-gray-700 mb-6" style="font-family: var(--font-serif);">
                    <?php echo htmlspecialchars($story['excerpt']); ?>
                </p>
            <?php endif; ?>

            <div class="byline">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <p class="byline-author">By <?php echo htmlspecialchars($story['author_name']); ?></p>
                        <p class="article-date mt-1">
                            <?php echo strtoupper(formatDate($story['published_at'] ?? $story['created_at'])); ?>
                        </p>
                    </div>
                    <div class="flex items-center space-x-4 article-meta">
                        <span><i class="far fa-clock mr-1"></i> <?php echo ceil(str_word_count(strip_tags($story['content'])) / 200); ?> min read</span>
                        <span><i class="far fa-eye mr-1"></i> <?php echo number_format($story['views']); ?> views</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Featured Image -->
        <?php if ($story['image']): ?>
            <figure class="mb-8">
                <img src="<?php echo UPLOAD_URL . $story['image']; ?>"
                     alt="<?php echo htmlspecialchars($story['title']); ?>"
                     class="w-full h-auto">
                <figcaption class="image-caption mt-2">
                    <?php echo htmlspecialchars($story['title']); ?>
                </figcaption>
            </figure>
        <?php endif; ?>

        <!-- Article Content -->
        <div class="article-content mb-12">
            <div class="dropcap">
                <?php
                $paragraphs = explode("\n\n", $story['content']);
                foreach ($paragraphs as $index => $paragraph) {
                    if (!empty(trim($paragraph))) {
                        echo '<p>' . nl2br(htmlspecialchars($paragraph)) . '</p>';
                    }
                }
                ?>
            </div>
        </div>

        <!-- Share Section -->
        <div class="border-t border-gray-300 pt-6 mb-12">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="article-meta">Share this story:</div>
                <div class="flex flex-wrap gap-3">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . '/post.php?slug=' . $story['slug']); ?>"
                       target="_blank"
                       class="px-4 py-2 border border-gray-300 hover:bg-gray-100 text-sm">
                        <i class="fab fa-facebook mr-2"></i>Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(SITE_URL . '/post.php?slug=' . $story['slug']); ?>&text=<?php echo urlencode($story['title']); ?>"
                       target="_blank"
                       class="px-4 py-2 border border-gray-300 hover:bg-gray-100 text-sm">
                        <i class="fab fa-twitter mr-2"></i>Twitter
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(SITE_URL . '/post.php?slug=' . $story['slug']); ?>"
                       target="_blank"
                       class="px-4 py-2 border border-gray-300 hover:bg-gray-100 text-sm">
                        <i class="fab fa-linkedin mr-2"></i>LinkedIn
                    </a>
                    <button onclick="copyToClipboard()"
                            class="px-4 py-2 border border-gray-300 hover:bg-gray-100 text-sm">
                        <i class="fas fa-link mr-2"></i>Copy Link
                    </button>
                </div>
            </div>
        </div>

    </div>

    <!-- Related Stories -->
    <?php if (!empty($related_stories)): ?>
        <section class="border-t-2 border-black pt-12 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="section-header mb-8">Related Stories</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <?php foreach ($related_stories as $related): ?>
                    <article class="article-card">
                        <?php if ($related['image']): ?>
                            <a href="<?php echo SITE_URL; ?>/post.php?slug=<?php echo $related['slug']; ?>">
                                <img src="<?php echo UPLOAD_URL . $related['image']; ?>"
                                     alt="<?php echo htmlspecialchars($related['title']); ?>"
                                     class="w-full h-48 object-cover mb-4">
                            </a>
                        <?php endif; ?>

                        <h3 class="article-card-title text-lg mb-3">
                            <a href="<?php echo SITE_URL; ?>/post.php?slug=<?php echo $related['slug']; ?>">
                                <?php echo htmlspecialchars($related['title']); ?>
                            </a>
                        </h3>

                        <p class="article-card-excerpt mb-3">
                            <?php echo htmlspecialchars($related['excerpt'] ?? truncate(strip_tags($related['content']), 100)); ?>
                        </p>

                        <div class="article-meta">
                            <?php echo formatDate($related['created_at']); ?>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
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
