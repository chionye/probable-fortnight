<?php
$page_title = 'Admin Settings';
require_once '../includes/admin-header.php';

$db = getDB();
$admin_id = $_SESSION['admin_id'];

// Get current admin info
$stmt = $db->prepare("SELECT * FROM admin_users WHERE id = :id");
$stmt->execute(['id' => $admin_id]);
$admin = $stmt->fetch();

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($username)) {
        $errors[] = 'Username is required';
    }
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }

    // Check if username/email already exists (excluding current user)
    if (empty($errors)) {
        $stmt = $db->prepare("SELECT id FROM admin_users WHERE (username = :username OR email = :email) AND id != :id");
        $stmt->execute(['username' => $username, 'email' => $email, 'id' => $admin_id]);
        if ($stmt->fetch()) {
            $errors[] = 'Username or email already exists';
        }
    }

    // Password change validation
    $update_password = false;
    if (!empty($new_password) || !empty($confirm_password)) {
        if (empty($current_password)) {
            $errors[] = 'Current password is required to change password';
        } elseif (!password_verify($current_password, $admin['password'])) {
            $errors[] = 'Current password is incorrect';
        } elseif (strlen($new_password) < 6) {
            $errors[] = 'New password must be at least 6 characters';
        } elseif ($new_password !== $confirm_password) {
            $errors[] = 'New passwords do not match';
        } else {
            $update_password = true;
        }
    }

    if (empty($errors)) {
        if ($update_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE admin_users SET username = :username, email = :email, password = :password WHERE id = :id");
            $result = $stmt->execute([
                'username' => $username,
                'email' => $email,
                'password' => $hashed_password,
                'id' => $admin_id
            ]);
        } else {
            $stmt = $db->prepare("UPDATE admin_users SET username = :username, email = :email WHERE id = :id");
            $result = $stmt->execute([
                'username' => $username,
                'email' => $email,
                'id' => $admin_id
            ]);
        }

        if ($result) {
            $_SESSION['admin_username'] = $username;
            $success = 'Settings updated successfully!';

            // Refresh admin data
            $stmt = $db->prepare("SELECT * FROM admin_users WHERE id = :id");
            $stmt->execute(['id' => $admin_id]);
            $admin = $stmt->fetch();
        } else {
            $errors[] = 'Failed to update settings';
        }
    }
}
?>

<div class="px-4 py-6 sm:px-0">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">Admin Settings</h1>

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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Account Information -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Account Information</h2>
            <form method="POST" action="">
                <div class="space-y-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            Username <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="username" name="username" required
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="<?php echo htmlspecialchars($admin['username']); ?>">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" required
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="<?php echo htmlspecialchars($admin['email']); ?>">
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                            <i class="fas fa-save mr-2"></i> Update Information
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Change Password -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Change Password</h2>
            <form method="POST" action="">
                <input type="hidden" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>">

                <div class="space-y-4">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Current Password
                        </label>
                        <input type="password" id="current_password" name="current_password"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                            New Password
                        </label>
                        <input type="password" id="new_password" name="new_password"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               minlength="6">
                        <p class="mt-1 text-sm text-gray-500">At least 6 characters</p>
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm New Password
                        </label>
                        <input type="password" id="confirm_password" name="confirm_password"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                            <i class="fas fa-key mr-2"></i> Update Password
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Account Stats -->
    <div class="bg-white shadow rounded-lg p-6 mt-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Account Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-500">Account ID</p>
                <p class="text-lg font-semibold">#<?php echo $admin['id']; ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Member Since</p>
                <p class="text-lg font-semibold"><?php echo formatDate($admin['created_at']); ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Last Login</p>
                <p class="text-lg font-semibold">Current Session</p>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/admin-footer.php'; ?>
