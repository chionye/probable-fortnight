<?php
$page_title = SITE_NAME . ' - Latest News and Stories';
require_once 'includes/header.php';

$featured_stories = getFeaturedStories(1);
$top_story = !empty($featured_stories) ? $featured_stories[0] : null;

$latest_stories = getAllStories(12, 0, 'published');
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Top Featured Story -->
    <?php if ($top_story): ?>
    <article class="featured-story mb-8">
        <div class="nyt-grid">
            <div class="grid-span-8">
                <?php if ($top_story['category_name']): ?>
                    <a href="<?php echo SITE_URL; ?>/blog.php?category=<?php echo $top_story['category_id']; ?>"
                       class="category-label">
                        <?php echo htmlspecialchars($top_story['category_name']); ?>
                    </a>
                <?php endif; ?>
                <h1 class="article-title mb-4">
                    <a href="<?php echo SITE_URL; ?>/post.php?slug=<?php echo $top_story['slug']; ?>"
                       class="hover:text-gray-700">
                        <?php echo htmlspecialchars($top_story['title']); ?>
                    </a>
                </h1>
                <p class="article-excerpt text-lg mb-4">
                    <?php echo htmlspecialchars($top_story['excerpt'] ?? truncate(strip_tags($top_story['content']), 200)); ?>
                </p>
                <div class="article-meta">
                    By <?php echo htmlspecialchars($top_story['author_name'] ?? 'Staff'); ?> |
                    <?php echo formatDate($top_story['published_at'] ?? $top_story['created_at']); ?>
                </div>
            </div>
            <div class="grid-span-4">
                <?php if ($top_story['image']): ?>
                    <a href="<?php echo SITE_URL; ?>/post.php?slug=<?php echo $top_story['slug']; ?>">
                        <img src="<?php echo UPLOAD_URL . $top_story['image']; ?>"
                             alt="<?php echo htmlspecialchars($top_story['title']); ?>"
                             class="w-full h-auto">
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </article>
    <?php endif; ?>

    <!-- Latest Stories Grid -->
    <div class="border-t-2 border-black pt-6 mb-12">
        <div class="nyt-grid">
            <?php
            $grid_stories = array_slice($latest_stories, 0, 6);
            foreach ($grid_stories as $index => $story):
                $span_class = ($index === 0) ? 'grid-span-8' : (($index === 1 || $index === 2) ? 'grid-span-4' : 'grid-span-4');
            ?>
            <article class="<?php echo $span_class; ?> article-card">
                <?php if ($story['image']): ?>
                    <a href="<?php echo SITE_URL; ?>/post.php?slug=<?php echo $story['slug']; ?>">
                        <img src="<?php echo UPLOAD_URL . $story['image']; ?>"
                             alt="<?php echo htmlspecialchars($story['title']); ?>"
                             class="w-full h-auto mb-3">
                    </a>
                <?php endif; ?>

                <?php if ($story['category_name']): ?>
                    <a href="<?php echo SITE_URL; ?>/blog.php?category=<?php echo $story['category_id']; ?>"
                       class="category-label">
                        <?php echo htmlspecialchars($story['category_name']); ?>
                    </a>
                <?php endif; ?>

                <h2 class="article-card-title">
                    <a href="<?php echo SITE_URL; ?>/post.php?slug=<?php echo $story['slug']; ?>">
                        <?php echo htmlspecialchars($story['title']); ?>
                    </a>
                </h2>

                <p class="article-card-excerpt">
                    <?php echo htmlspecialchars($story['excerpt'] ?? truncate(strip_tags($story['content']), 120)); ?>
                </p>

                <div class="article-meta">
                    <?php echo formatDate($story['created_at']); ?> |
                    <?php echo number_format($story['views']); ?> views
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- News Section -->
    <section class="border-t-2 border-black pt-6 mb-12">
        <h2 class="section-header">Latest News</h2>
        <div id="news-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="col-span-full text-center py-12">
                <div class="loading-skeleton inline-block w-12 h-12 rounded-full"></div>
                <p class="article-meta mt-4">Loading news...</p>
            </div>
        </div>
    </section>

    <!-- More Stories -->
    <section class="border-t-2 border-black pt-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="section-header mb-0 border-0">More Stories</h2>
            <a href="<?php echo SITE_URL; ?>/blog.php" class="nyt-nav-link">
                View All <i class="fas fa-arrow-right ml-2 text-xs"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-8">
            <?php
            $more_stories = array_slice($latest_stories, 6, 9);
            foreach ($more_stories as $story):
            ?>
            <article class="article-card">
                <?php if ($story['category_name']): ?>
                    <a href="<?php echo SITE_URL; ?>/blog.php?category=<?php echo $story['category_id']; ?>"
                       class="category-label">
                        <?php echo htmlspecialchars($story['category_name']); ?>
                    </a>
                <?php endif; ?>

                <h3 class="article-card-title text-lg">
                    <a href="<?php echo SITE_URL; ?>/post.php?slug=<?php echo $story['slug']; ?>">
                        <?php echo htmlspecialchars($story['title']); ?>
                    </a>
                </h3>

                <p class="article-card-excerpt">
                    <?php echo htmlspecialchars($story['excerpt'] ?? truncate(strip_tags($story['content']), 100)); ?>
                </p>

                <div class="article-meta">
                    <?php echo formatDate($story['created_at']); ?>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </section>

</main>

<script>
// Fetch news from API
$(document).ready(function() {
    $.ajax({
        url: '<?php echo SITE_URL; ?>/api/news.php',
        method: 'GET',
        success: function(response) {
            if (response.status === 'ok' && response.articles) {
                let newsHtml = '';
                response.articles.slice(0, 8).forEach(function(article) {
                    newsHtml += `
                        <article class="news-card">
                            ${article.urlToImage ? `
                                <img src="${article.urlToImage}" alt="${article.title}" class="w-full h-32 object-cover mb-2">
                            ` : ''}
                            <p class="news-source mb-2">${article.source.name}</p>
                            <h3 class="article-card-title text-base mb-2">
                                <a href="${article.url}" target="_blank" rel="noopener">
                                    ${article.title}
                                </a>
                            </h3>
                            <p class="article-card-excerpt text-sm">
                                ${article.description || ''}
                            </p>
                        </article>
                    `;
                });
                $('#news-container').html(newsHtml);
            } else {
                $('#news-container').html(`
                    <div class="col-span-full text-center py-8">
                        <p class="article-meta">Unable to load news at this time.</p>
                    </div>
                `);
            }
        },
        error: function() {
            $('#news-container').html(`
                <div class="col-span-full text-center py-8">
                    <p class="article-meta">Unable to load news at this time.</p>
                </div>
            `);
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
