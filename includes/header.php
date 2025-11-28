<?php
require_once __DIR__ . '/functions.php';
$categories = getAllCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? SITE_NAME; ?></title>
    <meta name="description" content="<?php echo $page_description ?? 'A modern blog with the latest stories and news'; ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <style>
        .swiper-button-next, .swiper-button-prev {
            color: white;
        }
        .swiper-pagination-bullet-active {
            background: white;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="<?php echo SITE_URL; ?>" class="text-2xl font-bold text-blue-600">
                            <i class="fas fa-blog mr-2"></i><?php echo SITE_NAME; ?>
                        </a>
                    </div>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:items-center sm:space-x-8">
                    <a href="<?php echo SITE_URL; ?>" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium">
                        Home
                    </a>
                    <a href="<?php echo SITE_URL; ?>/blog.php" class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium">
                        Blog
                    </a>
                    <div class="relative group">
                        <button class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium flex items-center">
                            Categories <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        <div class="absolute hidden group-hover:block bg-white shadow-lg rounded-md mt-2 py-2 w-48">
                            <?php foreach ($categories as $cat): ?>
                                <a href="<?php echo SITE_URL; ?>/blog.php?category=<?php echo $cat['id']; ?>"
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <form action="<?php echo SITE_URL; ?>/search.php" method="GET" class="flex">
                        <input type="text" name="q" placeholder="Search..."
                               class="border border-gray-300 rounded-l-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                        <button type="submit" class="bg-blue-600 text-white px-3 rounded-r-md hover:bg-blue-700">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                <!-- Mobile menu button -->
                <div class="flex items-center sm:hidden">
                    <button id="mobile-menu-button" class="text-gray-700 hover:text-blue-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden sm:hidden border-t border-gray-200">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="<?php echo SITE_URL; ?>" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                    Home
                </a>
                <a href="<?php echo SITE_URL; ?>/blog.php" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                    Blog
                </a>
                <div class="px-3 py-2">
                    <p class="text-sm font-medium text-gray-500 mb-2">Categories</p>
                    <?php foreach ($categories as $cat): ?>
                        <a href="<?php echo SITE_URL; ?>/blog.php?category=<?php echo $cat['id']; ?>"
                           class="block px-3 py-1 text-sm text-gray-700 hover:text-blue-600">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <form action="<?php echo SITE_URL; ?>/search.php" method="GET" class="px-3 py-2">
                    <input type="text" name="q" placeholder="Search..."
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </form>
            </div>
        </div>
    </nav>

    <script>
        // Mobile menu toggle
        $('#mobile-menu-button').on('click', function() {
            $('#mobile-menu').toggleClass('hidden');
        });
    </script>
