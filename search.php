<?php
require_once 'includes/header.php';

$search_query = $_GET['q'] ?? '';
$search_query = trim($search_query);

$page_title = 'Search Results' . ($search_query ? ' for "' . htmlspecialchars($search_query) . '"' : '') . ' - ' . SITE_NAME;

$stories = [];
$total_results = 0;

if (!empty($search_query)) {
    // Pagination
    $page = max(1, (int)($_GET['page'] ?? 1));
    $per_page = POSTS_PER_PAGE;
    $offset = ($page - 1) * $per_page;

    // Get search results
    $stories = getAllStories($per_page, $offset, 'published', null, $search_query);
    $total_results = getTotalStories('published', null, $search_query);
    $total_pages = ceil($total_results / $per_page);
}
?>

<div class="bg-white py-6 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">Search Results</h1>
        <form action="search.php" method="GET" class="max-w-2xl">
            <div class="flex">
                <input type="text" name="q" placeholder="Search for stories..."
                       class="flex-1 border border-gray-300 rounded-l-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-r-lg hover:bg-blue-700">
                    <i class="fas fa-search mr-2"></i> Search
                </button>
            </div>
        </form>
        <?php if (!empty($search_query)): ?>
            <p class="text-gray-600 mt-4">
                Found <strong><?php echo $total_results; ?></strong> <?php echo $total_results === 1 ? 'result' : 'results'; ?>
                for "<strong><?php echo htmlspecialchars($search_query); ?></strong>"
            </p>
        <?php endif; ?>
    </div>
</div>

<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (empty($search_query)): ?>
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Enter a search term</h3>
                <p class="text-gray-600">
                    Use the search box above to find stories by title, content, or excerpt.
                </p>
            </div>
        <?php elseif (empty($stories)): ?>
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">No results found</h3>
                <p class="text-gray-600 mb-6">
                    We couldn't find any stories matching "<?php echo htmlspecialchars($search_query); ?>".
                    Try using different keywords or browse our <a href="blog.php" class="text-blue-600 hover:text-blue-800">latest stories</a>.
                </p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <?php foreach ($stories as $story): ?>
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

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="flex justify-center">
                    <nav class="inline-flex rounded-md shadow-sm -space-x-px">
                        <?php if ($page > 1): ?>
                            <a href="?q=<?php echo urlencode($search_query); ?>&page=<?php echo $page - 1; ?>"
                               class="relative inline-flex items-center px-3 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <?php if ($i === $page): ?>
                                <span class="relative inline-flex items-center px-4 py-2 border border-blue-600 bg-blue-600 text-sm font-medium text-white">
                                    <?php echo $i; ?>
                                </span>
                            <?php elseif ($i === 1 || $i === $total_pages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                                <a href="?q=<?php echo urlencode($search_query); ?>&page=<?php echo $i; ?>"
                                   class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                    <?php echo $i; ?>
                                </a>
                            <?php elseif ($i === $page - 3 || $i === $page + 3): ?>
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                    ...
                                </span>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?q=<?php echo urlencode($search_query); ?>&page=<?php echo $page + 1; ?>"
                               class="relative inline-flex items-center px-3 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
