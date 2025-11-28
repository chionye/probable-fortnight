<?php
$page_title = 'Edit Story';
require_once '../includes/admin-header.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    redirect('stories.php');
}

$story = getStoryById($id);
if (!$story) {
    redirect('stories.php');
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $excerpt = sanitize($_POST['excerpt'] ?? '');
    $category_id = $_POST['category_id'] ?? null;
    $status = $_POST['status'] ?? 'draft';
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $featured_order = (int)($_POST['featured_order'] ?? 0);

    // Validation
    if (empty($title)) {
        $errors[] = 'Title is required';
    }
    if (empty($content)) {
        $errors[] = 'Content is required';
    }

    // Handle image upload
    $image = $story['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $new_image = uploadImage($_FILES['image']);
        if ($new_image) {
            // Delete old image
            if ($image) {
                deleteImage($image);
            }
            $image = $new_image;
        } else {
            $errors[] = 'Failed to upload image. Please check file size and format.';
        }
    }

    if (empty($errors)) {
        $db = getDB();

        $sql = "UPDATE stories SET
                title = :title,
                content = :content,
                excerpt = :excerpt,
                image = :image,
                category_id = :category_id,
                status = :status,
                is_featured = :is_featured,
                featured_order = :featured_order,
                published_at = :published_at
                WHERE id = :id";

        $published_at = $story['published_at'];
        if ($status === 'published' && !$published_at) {
            $published_at = date('Y-m-d H:i:s');
        }

        $stmt = $db->prepare($sql);
        $result = $stmt->execute([
            'title' => $title,
            'content' => $content,
            'excerpt' => $excerpt,
            'image' => $image,
            'category_id' => $category_id ?: null,
            'status' => $status,
            'is_featured' => $is_featured,
            'featured_order' => $featured_order,
            'published_at' => $published_at,
            'id' => $id
        ]);

        if ($result) {
            $success = 'Story updated successfully!';
            $story = getStoryById($id);
        } else {
            $errors[] = 'Failed to update story';
        }
    }
}

$categories = getAllCategories();
?>

<div class="px-4 py-6 sm:px-0">
    <div class="mb-6">
        <a href="stories.php" class="text-blue-600 hover:text-blue-900">
            <i class="fas fa-arrow-left mr-2"></i> Back to Stories
        </a>
    </div>

    <h1 class="text-3xl font-bold text-gray-900 mb-6">Edit Story</h1>

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

    <form method="POST" action="" enctype="multipart/form-data" class="bg-white shadow rounded-lg p-6">
        <div class="grid grid-cols-1 gap-6">
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Title <span class="text-red-500">*</span>
                </label>
                <input type="text" id="title" name="title" required
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       value="<?php echo htmlspecialchars($story['title']); ?>">
            </div>

            <!-- Category -->
            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                <select id="category_id" name="category_id"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Select Category --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"
                                <?php echo ($story['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Excerpt -->
            <div>
                <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">Excerpt</label>
                <textarea id="excerpt" name="excerpt" rows="3"
                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Brief summary of the story..."><?php echo htmlspecialchars($story['excerpt'] ?? ''); ?></textarea>
            </div>

            <!-- Content -->
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                    Content <span class="text-red-500">*</span>
                </label>
                <textarea id="content" name="content" rows="12" required
                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Write your story here..."><?php echo htmlspecialchars($story['content']); ?></textarea>
            </div>

            <!-- Current Image -->
            <?php if ($story['image']): ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Image</label>
                    <img src="<?php echo UPLOAD_URL . $story['image']; ?>" alt="" class="max-w-xs rounded-lg shadow">
                </div>
            <?php endif; ?>

            <!-- Image Upload -->
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                    <?php echo $story['image'] ? 'Change Image' : 'Upload Image'; ?>
                </label>
                <input type="file" id="image" name="image" accept="image/*"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="mt-1 text-sm text-gray-500">Max size: 5MB. Formats: JPG, PNG, GIF, WebP</p>
                <div id="image-preview" class="mt-3"></div>
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="status" name="status"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="draft" <?php echo ($story['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                    <option value="published" <?php echo ($story['status'] === 'published') ? 'selected' : ''; ?>>Published</option>
                </select>
            </div>

            <!-- Featured -->
            <div class="flex items-center">
                <input type="checkbox" id="is_featured" name="is_featured" value="1"
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                       <?php echo $story['is_featured'] ? 'checked' : ''; ?>>
                <label for="is_featured" class="ml-2 block text-sm text-gray-700">
                    Mark as Featured (will appear on homepage slider)
                </label>
            </div>

            <!-- Featured Order -->
            <div id="featured-order-container" style="<?php echo $story['is_featured'] ? '' : 'display: none;'; ?>">
                <label for="featured_order" class="block text-sm font-medium text-gray-700 mb-2">Featured Order</label>
                <input type="number" id="featured_order" name="featured_order" min="0"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       value="<?php echo $story['featured_order']; ?>"
                       placeholder="0 = highest priority">
                <p class="mt-1 text-sm text-gray-500">Lower numbers appear first in the slider (0 = highest priority)</p>
            </div>

            <!-- Submit -->
            <div class="flex gap-4">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                    <i class="fas fa-save mr-2"></i> Update Story
                </button>
                <a href="stories.php"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg">
                    Cancel
                </a>
                <a href="delete-story.php?id=<?php echo $story['id']; ?>"
                   class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg ml-auto"
                   onclick="return confirm('Are you sure you want to delete this story?')">
                    <i class="fas fa-trash mr-2"></i> Delete Story
                </a>
            </div>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // Image preview
    $('#image').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#image-preview').html(`
                    <img src="${e.target.result}" alt="Preview" class="max-w-xs rounded-lg shadow">
                `);
            };
            reader.readAsDataURL(file);
        }
    });

    // Show/hide featured order
    function toggleFeaturedOrder() {
        if ($('#is_featured').is(':checked')) {
            $('#featured-order-container').slideDown();
        } else {
            $('#featured-order-container').slideUp();
        }
    }

    $('#is_featured').on('change', toggleFeaturedOrder);
});
</script>

<?php require_once '../includes/admin-footer.php'; ?>
