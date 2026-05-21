# Kvitter - Social Media Platform

Kvitter is a social media platform built as a school project. It allows users to register, create posts, like, comment, follow each other, send direct messages, and much more.

## Features
- User registration and login with secure password hashing
- Create, delete posts (kvitter)
- Like and comment on posts
- Follow/unfollow users
- Bookmark posts
- Direct messages between users
- Notifications (likes, comments, follows)
- Search posts and hashtags
- Password reset via email (SMTP)
- Dark mode / Light mode toggle
- Profile editing with bio, location, website

## Technologies
- PHP (procedural)
- MySQL (PDO)
- HTML/CSS (Bootstrap 5, custom CSS)
- JavaScript (Bootstrap JS, custom app.js, analog clock)
- PHPMailer (for email sending)

## Setup Instructions

### Requirements
- Web server with PHP 8.0+ (XAMPP, WAMP, or similar)
- MySQL database
- Composer (not required, PHPMailer is included manually)

### Installation

1. **Place files in web root**
   - Copy the `slutprojekt` folder to your web server directory
   - Example: `C:\xampp\htdocs\slutprojekt`

2. **Create database**
   - Open phpMyAdmin or HeidiSQL
   - Create a database named `kvitter`
   - Import `database/database.sql` if you want the full schema, or let the app create tables automatically

3. **Configure database connection**
   - Open `includes/db.php`
   - Update credentials if needed:
     ```php
     $host = 'localhost';
     $dbname = 'kvitter';
     $username = 'root';
     $password = 'root';
     ```

4. **Configure email (optional)**
   - Open `includes/mail_config.php`
   - Add your SMTP credentials:
     ```php
     define('KVITTER_SMTP_HOST', 'smtp.gmail.com');
     define('KVITTER_SMTP_USERNAME', 'your@gmail.com');
     define('KVITTER_SMTP_PASSWORD', 'your-app-password');
     ```

5. **Access the site**
   - Open your browser and go to:
     `http://localhost/slutprojekt/slutprojekt-Abdulle1212/index.php`

## Default Users
The app automatically creates these users on first run:

| Username | Email | Password | Role |
|----------|-------|----------|------|
| abdulle | abdulle@example.com | Password123 | admin |
| kvitter | kvitter@example.com | Password123 | user |
| anna | anna@example.com | Password123 | user |
| erik | erik@example.com | Password123 | user |

## Project Structure
```
slutprojekt/
├── includes/
│   ├── auth.php          # Session management, login helpers
│   ├── db.php            # Database connection and schema setup
│   ├── social.php        # Core functions (posts, follows, likes, etc.)
│   ├── mail.php          # Email sending (SMTP + fallback)
│   ├── mail_config.php   # SMTP credentials (not included by default)
│   ├── header.php        # HTML header with navbar
│   └── footer.php        # HTML footer
├── slutprojekt-Abdulle1212/
│   ├── index.php         # Home feed
│   ├── login.php         # Login page
│   ├── register.php      # Registration page
│   ├── logout.php        # Logout
│   ├── profile.php       # User profile with tabs
│   ├── edit_profile.php  # Profile settings
│   ├── post.php          # Single post view
│   ├── create_post.php   # Create new post
│   ├── delete_post.php   # Delete post
│   ├── toggle_like.php   # Like/unlike post
│   ├── add_comment.php   # Add comment
│   ├── delete_comment.php
│   ├── toggle_follow.php # Follow/unfollow user
│   ├── toggle_bookmark.php
│   ├── bookmarks.php     # Saved posts
│   ├── messages.php      # Direct messages
│   ├── send_message.php  # Send message
│   ├── notifications.php # Notifications
│   ├── search.php        # Search posts and hashtags
│   ├── forgot_password.php
│   ├── reset_password.php
│   ├── app.js            # Custom JavaScript
│   ├── clock.js          # Analog clock widget
│   ├── style.css         # All styles
│   └── database.sql      # Database schema
└── database/
    └── database.sql      # Full database schema
```

## About
Built as a high school project. A social media platform for sharing thoughts, connecting with others, and learning web development.
