<?php
$page_title = 'Edit Category';
require_once '../includes/admin-header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    redirect('categories.php');
}

$category = getCategoryById($id);
if (!$category) {
    redirect('categories.php');
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $slug = sanitize($_POST['slug'] ?? '');

    // Validation
    if (empty($name)) {
        $errors[] = 'Category name is required';
    }
    if (empty($slug)) {
        $errors[] = 'Category slug is required';
    }

    // Check for duplicates (excluding current category)
    if (empty($errors)) {
        $db = getDB();
        $stmt = $db->prepare("SELECT id FROM categories WHERE (name = :name OR slug = :slug) AND id != :id");
        $stmt->execute(['name' => $name, 'slug' => $slug, 'id' => $id]);
        if ($stmt->fetch()) {
            $errors[] = 'A category with this name or slug already exists';
        }
    }

    if (empty($errors)) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE categories SET name = :name, slug = :slug WHERE id = :id");
        $result = $stmt->execute(['name' => $name, 'slug' => $slug, 'id' => $id]);

        if ($result) {
            $success = 'Category updated successfully!';
            $category = getCategoryById($id);
        } else {
            $errors[] = 'Failed to update category';
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

    <h1 class="text-3xl font-bold text-gray-900 mb-6">Edit Category</h1>

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
                       value="<?php echo htmlspecialchars($category['name']); ?>">
            </div>

            <!-- Slug -->
            <div>
                <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                    Slug <span class="text-red-500">*</span>
                </label>
                <input type="text" id="slug" name="slug" required
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       value="<?php echo htmlspecialchars($category['slug']); ?>">
                <p class="mt-1 text-sm text-gray-500">URL-friendly version (lowercase, no spaces)</p>
            </div>

            <!-- Submit -->
            <div class="flex gap-4">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                    <i class="fas fa-save mr-2"></i> Update Category
                </button>
                <a href="categories.php"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                    Cancel
                </a>
                <a href="delete-category.php?id=<?php echo $category['id']; ?>"
                   class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg ml-auto"
                   onclick="return confirm('Are you sure you want to delete this category?')">
                    <i class="fas fa-trash mr-2"></i> Delete Category
                </a>
            </div>
        </div>
    </form>
</div>

<?php require_once '../includes/admin-footer.php'; ?>
