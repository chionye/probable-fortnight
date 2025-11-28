<?php
$page_title = 'All Stories - ' . SITE_NAME;
require_once 'includes/header.php';

// Get filter parameters
$category_id = $_GET['category'] ?? null;
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = POSTS_PER_PAGE;
$offset = ($page - 1) * $per_page;

// Get total stories for pagination
$total_stories = getTotalStories('published', $category_id);
$total_pages = ceil($total_stories / $per_page);

// Get stories
$stories = getAllStories($per_page, $offset, 'published', $category_id);

// Get current category name
$current_category = null;
if ($category_id) {
    $current_category = getCategoryById($category_id);
}

$categories = getAllCategories();
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Page Header -->
    <div class="border-b-2 border-black pb-6 mb-8">
        <h1 class="article-title mb-2">
            <?php echo $current_category ? htmlspecialchars($current_category['name']) : 'All Stories'; ?>
        </h1>
        <p class="article-meta">
            <?php echo number_format($total_stories); ?> <?php echo $total_stories === 1 ? 'story' : 'stories'; ?>
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
        <!-- Main Content -->
        <div class="lg:col-span-3">
            <?php if (empty($stories)): ?>
                <div class="text-center py-16">
                    <h3 class="article-card-title mb-4">No stories found</h3>
                    <p class="article-card-excerpt mb-6">
                        <?php echo $current_category ? 'There are no stories in this category yet.' : 'No stories have been published yet.'; ?>
                    </p>
                    <?php if ($current_category): ?>
                        <a href="blog.php" class="nyt-button">View All Stories</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="space-y-0">
                    <?php foreach ($stories as $story): ?>
                    <article class="article-card py-8">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Image -->
                            <div class="md:col-span-1">
                                <?php if ($story['image']): ?>
                                    <a href="<?php echo SITE_URL; ?>/post.php?slug=<?php echo $story['slug']; ?>">
                                        <img src="<?php echo UPLOAD_URL . $story['image']; ?>"
                                             alt="<?php echo htmlspecialchars($story['title']); ?>"
                                             class="w-full h-48 object-cover">
                                    </a>
                                <?php endif; ?>
                            </div>

                            <!-- Content -->
                            <div class="md:col-span-2">
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
                                    <?php echo htmlspecialchars($story['excerpt'] ?? truncate(strip_tags($story['content']), 180)); ?>
                                </p>

                                <div class="article-meta">
                                    By <?php echo htmlspecialchars($story['author_name']); ?> |
                                    <?php echo formatDate($story['created_at']); ?> |
                                    <?php echo number_format($story['views']); ?> views
                                </div>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav class="border-t border-gray-300 pt-8 mt-8">
                        <div class="flex justify-center items-center space-x-2">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?>"
                                   class="px-3 py-2 border border-gray-300 hover:bg-gray-100 text-sm">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            <?php endif; ?>

                            <div class="hidden md:flex space-x-2">
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <?php if ($i === $page): ?>
                                        <span class="px-4 py-2 bg-black text-white text-sm font-bold">
                                            <?php echo $i; ?>
                                        </span>
                                    <?php elseif ($i === 1 || $i === $total_pages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                                        <a href="?page=<?php echo $i; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?>"
                                           class="px-4 py-2 border border-gray-300 hover:bg-gray-100 text-sm">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php elseif ($i === $page - 3 || $i === $page + 3): ?>
                                        <span class="px-4 py-2 text-sm">...</span>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>

                            <div class="md:hidden">
                                <span class="text-sm px-4 py-2">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
                            </div>

                            <?php if ($page < $total_pages): ?>
                                <a href="?page=<?php echo $page + 1; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?>"
                                   class="px-3 py-2 border border-gray-300 hover:bg-gray-100 text-sm">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <aside class="lg:col-span-1">
            <!-- Categories -->
            <div class="mb-12">
                <h3 class="sidebar-title">Categories</h3>
                <ul class="space-y-3">
                    <li>
                        <a href="blog.php"
                           class="block text-sm <?php echo !$category_id ? 'font-bold' : 'hover:underline'; ?>">
                            All Categories
                        </a>
                    </li>
                    <?php foreach ($categories as $cat): ?>
                        <li>
                            <a href="?category=<?php echo $cat['id']; ?>"
                               class="block text-sm <?php echo $category_id == $cat['id'] ? 'font-bold' : 'hover:underline'; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Popular Stories -->
            <?php
            $popular_stories = getAllStories(5, 0, 'published');
            if (!empty($popular_stories)):
            ?>
            <div class="border-t border-gray-300 pt-6">
                <h3 class="sidebar-title">Popular Stories</h3>
                <div class="space-y-6">
                    <?php foreach ($popular_stories as $index => $popular): ?>
                    <article class="<?php echo $index > 0 ? 'border-t border-gray-200 pt-6' : ''; ?>">
                        <h4 class="article-card-title text-sm mb-2 leading-tight">
                            <a href="<?php echo SITE_URL; ?>/post.php?slug=<?php echo $popular['slug']; ?>">
                                <?php echo htmlspecialchars($popular['title']); ?>
                            </a>
                        </h4>
                        <p class="article-meta">
                            <?php echo number_format($popular['views']); ?> views
                        </p>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </aside>
    </div>

</main>

<?php require_once 'includes/footer.php'; ?>
