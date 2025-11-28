<?php
$page_title = 'Manage Stories';
require_once '../includes/admin-header.php';

$db = getDB();

// Handle status filter
$status_filter = $_GET['status'] ?? 'all';
$category_filter = $_GET['category'] ?? '';

$sql = "SELECT s.*, c.name as category_name, a.username as author_name
        FROM stories s
        LEFT JOIN categories c ON s.category_id = c.id
        LEFT JOIN admin_users a ON s.author_id = a.id
        WHERE 1=1";

$params = [];

if ($status_filter !== 'all') {
    $sql .= " AND s.status = :status";
    $params['status'] = $status_filter;
}

if (!empty($category_filter)) {
    $sql .= " AND s.category_id = :category";
    $params['category'] = $category_filter;
}

$sql .= " ORDER BY s.created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$stories = $stmt->fetchAll();

$categories = getAllCategories();
?>

<div class="px-4 py-6 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Manage Stories</h1>
        <a href="add-story.php" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-plus mr-2"></i> Add New Story
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <form method="GET" action="" class="flex flex-wrap gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="border border-gray-300 rounded-md px-3 py-2" onchange="this.form.submit()">
                    <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All</option>
                    <option value="published" <?php echo $status_filter === 'published' ? 'selected' : ''; ?>>Published</option>
                    <option value="draft" <?php echo $status_filter === 'draft' ? 'selected' : ''; ?>>Draft</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category" class="border border-gray-300 rounded-md px-3 py-2" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $category_filter == $cat['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($status_filter !== 'all' || !empty($category_filter)): ?>
                <div class="flex items-end">
                    <a href="stories.php" class="text-blue-600 hover:text-blue-900 text-sm">Clear Filters</a>
                </div>
            <?php endif; ?>
        </form>
    </div>

    <!-- Stories Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Featured</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($stories)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            No stories found. <a href="add-story.php" class="text-blue-600 hover:text-blue-900">Create one now</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($stories as $story): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($story['image']): ?>
                                <img src="<?php echo UPLOAD_URL . $story['image']; ?>" alt="" class="h-10 w-16 object-cover rounded">
                            <?php else: ?>
                                <div class="h-10 w-16 bg-gray-200 rounded flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($story['title']); ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-500"><?php echo htmlspecialchars($story['category_name'] ?? 'N/A'); ?></span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($story['status'] === 'published'): ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Published
                                </span>
                            <?php else: ?>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Draft
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <?php if ($story['is_featured']): ?>
                                <i class="fas fa-star text-yellow-500"></i>
                                <span class="text-xs text-gray-500 ml-1">(<?php echo $story['featured_order']; ?>)</span>
                            <?php else: ?>
                                <i class="far fa-star text-gray-300"></i>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo number_format($story['views']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo timeAgo($story['created_at']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="edit-story.php?id=<?php echo $story['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="delete-story.php?id=<?php echo $story['id']; ?>"
                               class="text-red-600 hover:text-red-900"
                               onclick="return confirm('Are you sure you want to delete this story?')">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/admin-footer.php'; ?>
