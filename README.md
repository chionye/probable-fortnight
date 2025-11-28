# Modern Blog Platform

A full-featured blog platform built with PHP, JavaScript, jQuery, TailwindCSS, and MySQL. Features include a comprehensive admin dashboard, featured stories slider, news integration, search functionality, and more.

## Features

### Admin Features
- **Secure Authentication**: Login/logout system with password hashing
- **Admin Dashboard**: Overview with statistics (total stories, published, featured, views)
- **Story Management**:
  - Create, edit, publish, and delete stories
  - Rich content editor
  - Image upload with automatic thumbnail generation
  - Draft and published status
  - Category assignment
- **Featured Stories**: Mark stories as featured and set display order for homepage slider
- **Filtering**: Filter stories by status (draft/published) and category

### Public Features
- **Homepage**:
  - Responsive featured stories slider with Swiper.js
  - Latest stories grid
  - Live news feed from external API
- **Blog Listing**:
  - Paginated story list
  - Category filtering
  - Sidebar with categories and popular stories
- **Single Story Page**:
  - Full story display with featured image
  - Reading time estimate
  - View counter
  - Social media sharing buttons
  - Related stories section
- **Search**: Full-text search across story titles, content, and excerpts
- **Responsive Design**: Mobile-first design with TailwindCSS

### News Integration
- External news API integration (The Guardian API)
- Fallback to mock data if API is unavailable
- Displays 8 latest news articles on homepage

## Technology Stack

- **Backend**: PHP 7.4+ with PDO
- **Frontend**: HTML5, JavaScript, jQuery 3.7.1
- **Styling**: TailwindCSS 3.x
- **Database**: MySQL 5.7+
- **Slider**: Swiper.js 11.x
- **Icons**: Font Awesome 6.4.0

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (optional, for future enhancements)

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd probable-fortnight
   ```

2. **Configure Database**
   - Import the database schema:
     ```bash
     mysql -u root -p < database/schema.sql
     ```
   - Or manually create the database and run the SQL file

3. **Update Configuration**
   - Edit `includes/config.php` and update database credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     define('DB_NAME', 'blog_db');
     ```
   - Update `SITE_URL` to match your domain:
     ```php
     define('SITE_URL', 'http://yourdomain.com');
     ```

4. **Set Permissions**
   ```bash
   chmod 755 uploads/
   chmod 644 includes/config.php
   ```

5. **Access the Application**
   - Public site: `http://yourdomain.com`
   - Admin panel: `http://yourdomain.com/admin/login.php`

### Default Admin Credentials
- **Username**: admin
- **Password**: admin123

**⚠️ Important**: Change the default password immediately after first login!

## File Structure

```
probable-fortnight/
├── admin/                      # Admin panel
│   ├── dashboard.php          # Admin dashboard
│   ├── login.php              # Admin login
│   ├── logout.php             # Logout handler
│   ├── stories.php            # Story management
│   ├── add-story.php          # Create new story
│   ├── edit-story.php         # Edit existing story
│   └── delete-story.php       # Delete story handler
├── api/                        # API endpoints
│   └── news.php               # News API integration
├── assets/                     # Static assets
│   ├── css/                   # Custom CSS (if needed)
│   ├── js/                    # Custom JavaScript
│   └── images/                # Site images
├── database/                   # Database files
│   └── schema.sql             # Database schema
├── includes/                   # PHP includes
│   ├── config.php             # Configuration
│   ├── db.php                 # Database connection
│   ├── functions.php          # Helper functions
│   ├── header.php             # Public header
│   ├── footer.php             # Public footer
│   ├── admin-header.php       # Admin header
│   └── admin-footer.php       # Admin footer
├── uploads/                    # Uploaded images
├── index.php                   # Homepage
├── blog.php                    # Blog listing
├── post.php                    # Single story page
├── search.php                  # Search results
└── README.md                   # Documentation
```

## Database Schema

### Tables

1. **admin_users**: Admin user accounts
   - id, username, password, email, created_at

2. **categories**: Story categories
   - id, name, slug, created_at

3. **stories**: Blog posts/stories
   - id, title, slug, content, excerpt, image
   - category_id, author_id, status, is_featured, featured_order
   - views, created_at, updated_at, published_at

## Usage Guide

### Creating a Story

1. Log in to admin panel
2. Click "Add New Story" or navigate to `/admin/add-story.php`
3. Fill in the form:
   - **Title**: Story headline (required)
   - **Category**: Select from dropdown
   - **Excerpt**: Brief summary (optional but recommended)
   - **Content**: Main story text (required)
   - **Image**: Upload featured image (optional)
   - **Status**: Choose Draft or Published
   - **Featured**: Check to display on homepage slider
   - **Featured Order**: Lower numbers appear first (0 = highest priority)
4. Click "Create Story"

### Managing Stories

- **Edit**: Click edit icon next to any story
- **Delete**: Click delete icon (confirmation required)
- **Filter**: Use status and category dropdowns to filter stories
- **Publish**: Change status from Draft to Published

### Featured Stories

- Check "Mark as Featured" when creating/editing a story
- Set Featured Order to control slider position (0-99)
- Up to 5 featured stories display on homepage slider
- Stories automatically sorted by featured order, then by date

### News API Configuration

The blog uses The Guardian API by default. To use NewsAPI instead:

1. Get API key from https://newsapi.org/
2. Edit `api/news.php`:
   ```php
   $apiKey = 'your_api_key_here';
   $useNewsAPI = true;
   ```

## Customization

### Changing Site Name
Edit `includes/config.php`:
```php
define('SITE_NAME', 'Your Blog Name');
```

### Adjusting Posts Per Page
Edit `includes/config.php`:
```php
define('POSTS_PER_PAGE', 12); // Change from 9
```

### Styling
- The site uses TailwindCSS CDN
- Add custom CSS in `assets/css/`
- Modify color scheme by editing Tailwind classes

### Adding Categories
Insert directly into database:
```sql
INSERT INTO categories (name, slug) VALUES ('New Category', 'new-category');
```

## Security Features

- Password hashing with `password_hash()`
- SQL injection prevention with PDO prepared statements
- XSS protection with `htmlspecialchars()`
- CSRF protection through session validation
- Secure file upload validation
- Admin-only access controls

## Performance Optimization

- Database indexes on frequently queried columns
- Efficient pagination queries
- Image upload size limits (5MB max)
- CDN usage for libraries (TailwindCSS, jQuery, Font Awesome)

## Browser Support

- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Troubleshooting

### Images not uploading
- Check `uploads/` directory permissions (755)
- Verify PHP `upload_max_filesize` and `post_max_size` settings
- Ensure file extensions match allowed types

### Database connection errors
- Verify credentials in `includes/config.php`
- Check MySQL service is running
- Ensure database exists and schema is imported

### News not loading
- Check internet connectivity
- API may have rate limits (Guardian API has free tier limits)
- Fallback mock data will display if API fails

## Future Enhancements

Potential features for future development:
- Comments system
- User roles (editor, contributor, subscriber)
- Tags/multi-category support
- Rich text WYSIWYG editor
- Image gallery
- SEO meta tags management
- Analytics dashboard
- Email notifications
- RSS feed
- Multi-language support

## License

This project is open-source and available for personal and commercial use.

## Support

For issues and questions:
1. Check the troubleshooting section
2. Review database schema and configuration
3. Verify file permissions and server requirements

## Credits

Built with:
- [TailwindCSS](https://tailwindcss.com/)
- [jQuery](https://jquery.com/)
- [Swiper.js](https://swiperjs.com/)
- [Font Awesome](https://fontawesome.com/)
- [The Guardian API](https://open-platform.theguardian.com/)
