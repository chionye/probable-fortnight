<?php
$page_title = 'Home - ' . SITE_NAME;
require_once 'includes/header.php';

$featured_stories = getFeaturedStories(5);
$latest_stories = getAllStories(6, 0, 'published');
?>

<!-- Featured Stories Slider -->
<?php if (!empty($featured_stories)): ?>
<section class="bg-gray-900 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-white mb-6">Featured Stories</h2>
        <div class="swiper featured-slider">
            <div class="swiper-wrapper">
                <?php foreach ($featured_stories as $story): ?>
                <div class="swiper-slide">
                    <div class="relative h-96 rounded-lg overflow-hidden">
                        <?php if ($story['image']): ?>
                            <img src="<?php echo UPLOAD_URL . $story['image']; ?>"
                                 alt="<?php echo htmlspecialchars($story['title']); ?>"
                                 class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full bg-gradient-to-r from-blue-600 to-purple-600"></div>
                        <?php endif; ?>
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                            <?php if ($story['category_name']): ?>
                                <span class="inline-block bg-blue-600 text-white text-xs px-3 py-1 rounded-full mb-2">
                                    <?php echo htmlspecialchars($story['category_name']); ?>
                                </span>
                            <?php endif; ?>
                            <h3 class="text-2xl font-bold mb-2">
                                <a href="<?php echo SITE_URL; ?>/post.php?slug=<?php echo $story['slug']; ?>" class="hover:text-blue-300">
                                    <?php echo htmlspecialchars($story['title']); ?>
                                </a>
                            </h3>
                            <p class="text-gray-200 mb-3">
                                <?php echo truncate($story['excerpt'] ?? strip_tags($story['content']), 120); ?>
                            </p>
                            <a href="<?php echo SITE_URL; ?>/post.php?slug=<?php echo $story['slug']; ?>"
                               class="inline-block bg-white text-gray-900 px-4 py-2 rounded-lg hover:bg-gray-200 transition">
                                Read More <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Latest Stories -->
<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-900">Latest Stories</h2>
            <a href="<?php echo SITE_URL; ?>/blog.php" class="text-blue-600 hover:text-blue-800 font-medium">
                View All <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($latest_stories as $story): ?>
            <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                <?php if ($story['image']): ?>
                    <a href="<?php echo SITE_URL; ?>/post.php?slug=<?php echo $story['slug']; ?>">
                        <img src="<?php echo UPLOAD_URL . $story['image']; ?>"
                             alt="<?php echo htmlspecialchars($story['title']); ?>"
                             class="w-full h-48 object-cover">
                    </a>
                <?php else: ?>
                    <div class="w-full h-48 bg-gradient-to-r from-blue-400 to-purple-400"></div>
                <?php endif; ?>
                <div class="p-5">
                    <?php if ($story['category_name']): ?>
                        <a href="<?php echo SITE_URL; ?>/blog.php?category=<?php echo $story['category_id']; ?>"
                           class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mb-2">
                            <?php echo htmlspecialchars($story['category_name']); ?>
                        </a>
                    <?php endif; ?>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">
                        <a href="<?php echo SITE_URL; ?>/post.php?slug=<?php echo $story['slug']; ?>" class="hover:text-blue-600">
                            <?php echo htmlspecialchars($story['title']); ?>
                        </a>
                    </h3>
                    <p class="text-gray-600 text-sm mb-3">
                        <?php echo truncate($story['excerpt'] ?? strip_tags($story['content']), 100); ?>
                    </p>
                    <div class="flex justify-between items-center text-sm text-gray-500">
                        <span><i class="far fa-calendar mr-1"></i> <?php echo timeAgo($story['created_at']); ?></span>
                        <span><i class="far fa-eye mr-1"></i> <?php echo number_format($story['views']); ?> views</span>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- News Section -->
<section class="py-12 bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-6">Latest News</h2>
        <div id="news-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="col-span-full text-center py-8">
                <i class="fas fa-spinner fa-spin text-4xl text-blue-600"></i>
                <p class="text-gray-600 mt-4">Loading news...</p>
            </div>
        </div>
    </div>
</section>

<script>
// Initialize Swiper for featured slider
const swiper = new Swiper('.featured-slider', {
    loop: true,
    autoplay: {
        delay: 5000,
        disableOnInteraction: false,
    },
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
});

// Fetch news from NewsAPI
$(document).ready(function() {
    $.ajax({
        url: '<?php echo SITE_URL; ?>/api/news.php',
        method: 'GET',
        success: function(response) {
            if (response.status === 'ok' && response.articles) {
                let newsHtml = '';
                response.articles.slice(0, 8).forEach(function(article) {
                    newsHtml += `
                        <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                            ${article.urlToImage ? `
                                <img src="${article.urlToImage}" alt="${article.title}" class="w-full h-40 object-cover">
                            ` : `
                                <div class="w-full h-40 bg-gradient-to-r from-green-400 to-blue-400"></div>
                            `}
                            <div class="p-4">
                                <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">
                                    <a href="${article.url}" target="_blank" class="hover:text-blue-600">
                                        ${article.title}
                                    </a>
                                </h3>
                                <p class="text-gray-600 text-sm mb-3 line-clamp-3">
                                    ${article.description || 'No description available'}
                                </p>
                                <div class="flex justify-between items-center text-xs text-gray-500">
                                    <span>${article.source.name}</span>
                                    <a href="${article.url}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        Read more <i class="fas fa-external-link-alt ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    `;
                });
                $('#news-container').html(newsHtml);
            } else {
                $('#news-container').html(`
                    <div class="col-span-full text-center py-8">
                        <i class="fas fa-exclamation-circle text-4xl text-red-600"></i>
                        <p class="text-gray-600 mt-4">Unable to load news at this time.</p>
                    </div>
                `);
            }
        },
        error: function() {
            $('#news-container').html(`
                <div class="col-span-full text-center py-8">
                    <i class="fas fa-exclamation-circle text-4xl text-red-600"></i>
                    <p class="text-gray-600 mt-4">Unable to load news at this time.</p>
                </div>
            `);
        }
    });
});
</script>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

<?php require_once 'includes/footer.php'; ?>
