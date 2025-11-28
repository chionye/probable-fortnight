    <!-- Footer -->
    <footer class="nyt-footer mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center mb-8">
                <a href="<?php echo SITE_URL; ?>" class="nyt-logo inline-block text-4xl">
                    <?php echo SITE_NAME; ?>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8 mb-8">
                <div>
                    <h3 class="sidebar-title">Sections</h3>
                    <ul class="space-y-2 text-xs">
                        <li><a href="<?php echo SITE_URL; ?>" class="hover:underline">Home</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/blog.php" class="hover:underline">All Stories</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/search.php" class="hover:underline">Search</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="sidebar-title">Categories</h3>
                    <ul class="space-y-2 text-xs">
                        <?php
                        $footer_categories = getAllCategories();
                        foreach (array_slice($footer_categories, 0, 4) as $cat):
                        ?>
                            <li>
                                <a href="<?php echo SITE_URL; ?>/blog.php?category=<?php echo $cat['id']; ?>"
                                   class="hover:underline">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div>
                    <h3 class="sidebar-title">More</h3>
                    <ul class="space-y-2 text-xs">
                        <?php foreach (array_slice($footer_categories, 4) as $cat): ?>
                            <li>
                                <a href="<?php echo SITE_URL; ?>/blog.php?category=<?php echo $cat['id']; ?>"
                                   class="hover:underline">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div>
                    <h3 class="sidebar-title">Follow Us</h3>
                    <ul class="space-y-2 text-xs">
                        <li><a href="#" class="hover:underline"><i class="fab fa-facebook mr-2"></i>Facebook</a></li>
                        <li><a href="#" class="hover:underline"><i class="fab fa-twitter mr-2"></i>Twitter</a></li>
                        <li><a href="#" class="hover:underline"><i class="fab fa-instagram mr-2"></i>Instagram</a></li>
                        <li><a href="#" class="hover:underline"><i class="fab fa-linkedin mr-2"></i>LinkedIn</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="sidebar-title">About</h3>
                    <ul class="space-y-2 text-xs">
                        <li><a href="#" class="hover:underline">About Us</a></li>
                        <li><a href="#" class="hover:underline">Contact</a></li>
                        <li><a href="#" class="hover:underline">Privacy Policy</a></li>
                        <li><a href="#" class="hover:underline">Terms of Service</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="sidebar-title">Subscribe</h3>
                    <p class="text-xs mb-3">Get the latest stories delivered to you.</p>
                    <form class="flex flex-col gap-2">
                        <input type="email" placeholder="Email address" class="nyt-search px-3 py-2 text-xs">
                        <button type="submit" class="nyt-button text-xs py-2">Subscribe</button>
                    </form>
                </div>
            </div>

            <div class="border-t border-gray-300 pt-6 text-center">
                <p class="text-xs text-gray-600">
                    &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
