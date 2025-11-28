<?php
require_once __DIR__ . '/functions.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin Dashboard'; ?> - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="<?php echo ADMIN_URL; ?>/dashboard.php" class="text-xl font-bold">
                            <?php echo SITE_NAME; ?> Admin
                        </a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="<?php echo ADMIN_URL; ?>/dashboard.php"
                           class="inline-flex items-center px-1 pt-1 text-sm font-medium hover:text-gray-200">
                            <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                        </a>
                        <a href="<?php echo ADMIN_URL; ?>/stories.php"
                           class="inline-flex items-center px-1 pt-1 text-sm font-medium hover:text-gray-200">
                            <i class="fas fa-newspaper mr-2"></i> Stories
                        </a>
                        <a href="<?php echo ADMIN_URL; ?>/add-story.php"
                           class="inline-flex items-center px-1 pt-1 text-sm font-medium hover:text-gray-200">
                            <i class="fas fa-plus mr-2"></i> New Story
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    <a href="<?php echo SITE_URL; ?>" target="_blank"
                       class="mr-4 text-sm hover:text-gray-200">
                        <i class="fas fa-external-link-alt mr-1"></i> View Site
                    </a>
                    <span class="mr-4 text-sm">
                        <i class="fas fa-user mr-1"></i> <?php echo $_SESSION['admin_username']; ?>
                    </span>
                    <a href="<?php echo ADMIN_URL; ?>/logout.php"
                       class="bg-red-500 hover:bg-red-600 px-3 py-2 rounded text-sm">
                        <i class="fas fa-sign-out-alt mr-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
