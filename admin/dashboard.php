<?php
$page_title = 'Dashboard';
require_once '../includes/admin-header.php';

$db = getDB();

// Get statistics
$total_stories = $db->query("SELECT COUNT(*) FROM stories")->fetchColumn();
$published_stories = $db->query("SELECT COUNT(*) FROM stories WHERE status = 'published'")->fetchColumn();
$draft_stories = $db->query("SELECT COUNT(*) FROM stories WHERE status = 'draft'")->fetchColumn();
$featured_stories = $db->query("SELECT COUNT(*) FROM stories WHERE is_featured = 1")->fetchColumn();
$total_views = $db->query("SELECT SUM(views) FROM stories")->fetchColumn() ?? 0;

// Get recent stories
$recent_stories = $db->query("SELECT s.*, c.name as category_name, a.username as author_name
                               FROM stories s
                               LEFT JOIN categories c ON s.category_id = c.id
                               LEFT JOIN admin_users a ON s.author_id = a.id
                               ORDER BY s.created_at DESC LIMIT 10")->fetchAll();
?>

<div class="px-4 py-6 sm:px-0">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Dashboard</h1>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-newspaper text-3xl text-blue-500"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Stories</dt>
                            <dd class="text-3xl font-semibold text-gray-900"><?php echo $total_stories; ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-3xl text-green-500"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Published</dt>
                            <dd class="text-3xl font-semibold text-gray-900"><?php echo $published_stories; ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-star text-3xl text-yellow-500"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Featured</dt>
                            <dd class="text-3xl font-semibold text-gray-900"><?php echo $featured_stories; ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-eye text-3xl text-purple-500"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Views</dt>
                            <dd class="text-3xl font-semibold text-gray-900"><?php echo number_format($total_views); ?></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Stories -->
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Recent Stories</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
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
                    <?php foreach ($recent_stories as $story): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
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
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <?php if ($story['is_featured']): ?>
                                <i class="fas fa-star text-yellow-500"></i>
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
                            <a href="edit-story.php?id=<?php echo $story['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 border-t border-gray-200 sm:px-6">
            <a href="stories.php" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                View all stories &rarr;
            </a>
        </div>
    </div>
</div>

<?php require_once '../includes/admin-footer.php'; ?>
