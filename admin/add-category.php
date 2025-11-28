<?php
$page_title = 'Add Category';
require_once '../includes/admin-header.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $slug = sanitize($_POST['slug'] ?? '');

    // Auto-generate slug if empty
    if (empty($slug) && !empty($name)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
    }

    // Validation
    if (empty($name)) {
        $errors[] = 'Category name is required';
    }
    if (empty($slug)) {
        $errors[] = 'Category slug is required';
    }

    // Check for duplicates
    if (empty($errors)) {
        $db = getDB();
        $stmt = $db->prepare("SELECT id FROM categories WHERE name = :name OR slug = :slug");
        $stmt->execute(['name' => $name, 'slug' => $slug]);
        if ($stmt->fetch()) {
            $errors[] = 'A category with this name or slug already exists';
        }
    }

    if (empty($errors)) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO categories (name, slug) VALUES (:name, :slug)");
        $result = $stmt->execute(['name' => $name, 'slug' => $slug]);

        if ($result) {
            $success = 'Category created successfully!';
            header('Refresh: 2; URL=categories.php');
        } else {
            $errors[] = 'Failed to create category';
        }
    }
}
?>

<div class="px-4 py-6 sm:px-0">
    <div class="mb-6">
        <a href="categories.php" class="text-blue-600 hover:text-blue-900">
            <i class="fas fa-arrow-left mr-2"></i> Back to Categories
        </a>
    </div>

    <h1 class="text-3xl font-bold text-gray-900 mb-6">Add New Category</h1>

    <?php if (!empty($errors)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" class="bg-white shadow rounded-lg p-6 max-w-2xl">
        <div class="space-y-6">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Category Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="name" name="name" required
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                       placeholder="e.g., Technology">
            </div>

            <!-- Slug -->
            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                    Slug <span class="text-gray-500 text-xs">(auto-generated if left empty)</span>
                </label>
                <input type="text" id="slug" name="slug"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       value="<?php echo htmlspecialchars($_POST['slug'] ?? ''); ?>"
                       placeholder="e.g., technology">
                <p class="mt-1 text-sm text-gray-500">URL-friendly version (lowercase, no spaces)</p>
            </div>

            <!-- Submit -->
            <div class="flex gap-4">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                    <i class="fas fa-save mr-2"></i> Create Category
                </button>
                <a href="categories.php"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>

<script>
// Auto-generate slug from name
$('#name').on('input', function() {
    if ($('#slug').val() === '') {
        const slug = $(this).val()
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '');
        $('#slug').val(slug);
    }
});
</script>

<?php require_once '../includes/admin-footer.php'; ?>
