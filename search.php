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

<div class="border-b-2 border-black py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="article-title mb-6">Search</h1>
        <form action="search.php" method="GET" class="max-w-2xl">
            <div class="flex">
                <input type="text" name="q" placeholder="Search for stories..."
                       class="flex-1 nyt-search px-4 py-3 text-base"
                       value="<?php echo htmlspecialchars($search_query); ?>">
                <button type="submit" class="nyt-button px-6 py-3">
                    <i class="fas fa-search mr-2"></i> Search
                </button>
            </div>
        </form>
        <?php if (!empty($search_query)): ?>
            <p class="article-meta mt-4">
                Found <?php echo number_format($total_results); ?> <?php echo $total_results === 1 ? 'result' : 'results'; ?>
                for "<?php echo htmlspecialchars($search_query); ?>"
            </p>
        <?php endif; ?>
    </div>
</div>

<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (empty($search_query)): ?>
            <div class="text-center py-16">
                <h3 class="article-card-title mb-4">Enter a search term</h3>
                <p class="article-card-excerpt max-w-lg mx-auto">
                    Use the search box above to find stories by title, content, or excerpt.
                </p>
            </div>
        <?php elseif (empty($stories)): ?>
            <div class="text-center py-16">
                <h3 class="article-card-title mb-4">No results found</h3>
                <p class="article-card-excerpt max-w-lg mx-auto mb-6">
                    We couldn't find any stories matching "<?php echo htmlspecialchars($search_query); ?>".
                    Try using different keywords or browse our <a href="blog.php" class="hover:underline font-bold">latest stories</a>.
                </p>
            </div>
        <?php else: ?>
            <div class="space-y-0 divide-y divide-gray-300">
                <?php foreach ($stories as $story): ?>
                <article class="article-card py-8">
                    <?php if ($story['category_name']): ?>
                        <a href="<?php echo SITE_URL; ?>/blog.php?category=<?php echo $story['category_id']; ?>"
                           class="category-label">
                            <?php echo htmlspecialchars($story['category_name']); ?>
                        </a>
                    <?php endif; ?>

                    <h2 class="article-card-title text-2xl mb-3">
                        <a href="<?php echo SITE_URL; ?>/post.php?slug=<?php echo $story['slug']; ?>">
                            <?php echo htmlspecialchars($story['title']); ?>
                        </a>
                    </h2>

                    <p class="article-card-excerpt mb-4">
                        <?php echo htmlspecialchars($story['excerpt'] ?? truncate(strip_tags($story['content']), 150)); ?>
                    </p>

                    <div class="article-meta">
                        <?php echo formatDate($story['created_at']); ?> |
                        <?php echo number_format($story['views']); ?> views
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
