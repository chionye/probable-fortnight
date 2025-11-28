<?php
$page_title = 'Blog - ' . SITE_NAME;
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

<div class="bg-white py-6 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-bold text-gray-900">
            <?php echo $current_category ? 'Category: ' . htmlspecialchars($current_category['name']) : 'All Stories'; ?>
        </h1>
        <p class="text-gray-600 mt-2">
            <?php echo $total_stories; ?> <?php echo $total_stories === 1 ? 'story' : 'stories'; ?> found
        </p>
    </div>
</div>

<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Main Content -->
            <div class="lg:w-3/4">
                <?php if (empty($stories)): ?>
                    <div class="bg-white rounded-lg shadow-md p-12 text-center">
                        <i class="fas fa-newspaper text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">No stories found</h3>
                        <p class="text-gray-600 mb-6">
                            <?php echo $current_category ? 'There are no stories in this category yet.' : 'No stories have been published yet.'; ?>
                        </p>
                        <?php if ($current_category): ?>
                            <a href="blog.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">
                                View All Stories
                            </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <?php foreach ($stories as $story): ?>
                        <article class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                            <?php if ($story['image']): ?>
                                <a href="<?php echo SITE_URL; ?>/post.php?slug=<?php echo $story['slug']; ?>">
                                    <img src="<?php echo UPLOAD_URL . $story['image']; ?>"
                                         alt="<?php echo htmlspecialchars($story['title']); ?>"
                                         class="w-full h-56 object-cover">
                                </a>
                            <?php else: ?>
                                <div class="w-full h-56 bg-gradient-to-r from-blue-400 to-purple-400"></div>
                            <?php endif; ?>
                            <div class="p-6">
                                <?php if ($story['category_name']): ?>
                                    <a href="<?php echo SITE_URL; ?>/blog.php?category=<?php echo $story['category_id']; ?>"
                                       class="inline-block bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded-full mb-3 hover:bg-blue-200">
                                        <?php echo htmlspecialchars($story['category_name']); ?>
                                    </a>
                                <?php endif; ?>
                                <h2 class="text-2xl font-bold text-gray-900 mb-3">
                                    <a href="<?php echo SITE_URL; ?>/post.php?slug=<?php echo $story['slug']; ?>" class="hover:text-blue-600">
                                        <?php echo htmlspecialchars($story['title']); ?>
                                    </a>
                                </h2>
                                <p class="text-gray-600 mb-4">
                                    <?php echo truncate($story['excerpt'] ?? strip_tags($story['content']), 150); ?>
                                </p>
                                <div class="flex justify-between items-center text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <i class="far fa-user mr-2"></i>
                                        <span><?php echo htmlspecialchars($story['author_name']); ?></span>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span><i class="far fa-calendar mr-1"></i> <?php echo timeAgo($story['created_at']); ?></span>
                                        <span><i class="far fa-eye mr-1"></i> <?php echo number_format($story['views']); ?></span>
                                    </div>
                                </div>
                                <a href="<?php echo SITE_URL; ?>/post.php?slug=<?php echo $story['slug']; ?>"
                                   class="inline-block mt-4 text-blue-600 hover:text-blue-800 font-medium">
                                    Read More <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <div class="flex justify-center">
                            <nav class="inline-flex rounded-md shadow-sm -space-x-px">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?php echo $page - 1; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?>"
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
                                        <a href="?page=<?php echo $i; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?>"
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
                                    <a href="?page=<?php echo $page + 1; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?>"
                                       class="relative inline-flex items-center px-3 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                <?php endif; ?>
                            </nav>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <aside class="lg:w-1/4">
                <!-- Categories -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Categories</h3>
                    <ul class="space-y-2">
                        <li>
                            <a href="blog.php"
                               class="block px-3 py-2 rounded <?php echo !$category_id ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                                All Categories
                            </a>
                        </li>
                        <?php foreach ($categories as $cat): ?>
                            <li>
                                <a href="?category=<?php echo $cat['id']; ?>"
                                   class="block px-3 py-2 rounded <?php echo $category_id == $cat['id'] ? 'bg-blue-600 text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
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
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Popular Stories</h3>
                    <div class="space-y-4">
                        <?php foreach ($popular_stories as $popular): ?>
                        <div class="flex gap-3">
                            <?php if ($popular['image']): ?>
                                <img src="<?php echo UPLOAD_URL . $popular['image']; ?>"
                                     alt="<?php echo htmlspecialchars($popular['title']); ?>"
                                     class="w-16 h-16 object-cover rounded">
                            <?php else: ?>
                                <div class="w-16 h-16 bg-gradient-to-r from-blue-400 to-purple-400 rounded"></div>
                            <?php endif; ?>
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900 mb-1">
                                    <a href="<?php echo SITE_URL; ?>/post.php?slug=<?php echo $popular['slug']; ?>" class="hover:text-blue-600">
                                        <?php echo truncate($popular['title'], 50); ?>
                                    </a>
                                </h4>
                                <p class="text-xs text-gray-500">
                                    <i class="far fa-eye mr-1"></i> <?php echo number_format($popular['views']); ?> views
                                </p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
