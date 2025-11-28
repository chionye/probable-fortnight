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
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>
<body class="bg-white">
    <!-- Date Bar -->
    <div class="border-b border-gray-200 py-2">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center text-xs">
                <div class="article-date">
                    <?php echo strtoupper(date('l, F j, Y')); ?>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="#" class="text-gray-600 hover:text-gray-900"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-gray-600 hover:text-gray-900"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-gray-600 hover:text-gray-900"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="nyt-nav sticky top-0 z-50 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Logo -->
            <div class="text-center py-4 border-b border-gray-200">
                <a href="<?php echo SITE_URL; ?>" class="nyt-logo inline-block">
                    <?php echo SITE_NAME; ?>
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="hidden md:flex items-center justify-between py-3">
                <div class="flex items-center space-x-1">
                    <a href="<?php echo SITE_URL; ?>" class="nyt-nav-link">Home</a>
                    <a href="<?php echo SITE_URL; ?>/blog.php" class="nyt-nav-link">All Stories</a>
                    <?php foreach (array_slice($categories, 0, 5) as $cat): ?>
                        <a href="<?php echo SITE_URL; ?>/blog.php?category=<?php echo $cat['id']; ?>"
                           class="nyt-nav-link">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <div class="flex items-center space-x-3">
                    <form action="<?php echo SITE_URL; ?>/search.php" method="GET" class="flex">
                        <input type="text" name="q" placeholder="SEARCH"
                               class="nyt-search px-3 py-1.5 text-xs w-40"
                               value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                        <button type="submit" class="px-3 py-1.5 bg-black text-white text-xs hover:bg-gray-800">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden flex justify-between items-center py-3">
                <button id="mobile-menu-button" class="text-gray-900">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <form action="<?php echo SITE_URL; ?>/search.php" method="GET">
                    <button type="button" onclick="this.parentElement.querySelector('input').classList.toggle('hidden'); this.parentElement.querySelector('input').focus();" class="text-gray-900">
                        <i class="fas fa-search"></i>
                    </button>
                    <input type="text" name="q" placeholder="Search..." class="hidden nyt-search px-2 py-1 text-sm ml-2">
                </form>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200 py-4">
                <div class="space-y-2">
                    <a href="<?php echo SITE_URL; ?>" class="block nyt-nav-link">Home</a>
                    <a href="<?php echo SITE_URL; ?>/blog.php" class="block nyt-nav-link">All Stories</a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="<?php echo SITE_URL; ?>/blog.php?category=<?php echo $cat['id']; ?>"
                           class="block nyt-nav-link">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </nav>

    <script>
        // Mobile menu toggle
        $('#mobile-menu-button').on('click', function() {
            $('#mobile-menu').toggleClass('hidden');
        });
    </script>
