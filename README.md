<div align="center">
  <img src="public/assets/images/logos/LOGO JAVADEV.png" alt="JAVADEV Logo" width="200">

  # JAVADEV - Community for University Dinamika

  [![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
  [![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
  [![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
  [![Vite](https://img.shields.io/badge/Vite-646CFF?style=for-the-badge&logo=vite&logoColor=white)](https://vitejs.dev)
  [![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)

  Welcome to **JAVADEV**, a modern platform designed for developers to showcase their portfolios, track their courses, and connect with mentors.
</div>

---

## 🚀 Features

-   **Dynamic Portfolio**: Showcase your projects with screenshots and links.
-   **Course Tracking**: Keep track of your learning progress.
-   **Mentor Dashboard**: Specialized interface for mentors to manage events and guidance.
-   **Customizable Profiles**: Professional profiles with custom avatars and bio.
-   **Integrated Events**: Join or host developer events.

## 🛠️ Tech Stack

-   **Backend**: Laravel 11.x
-   **Frontend**: Blade + Tailwind CSS + JavaScript
-   **Database**: MySQL / MariaDB
-   **Tools**: Vite, Composer, NPM

## ⚙️ Installation & Setup

Follow these steps to get your local development environment running:

### 1. Prerequisites
Ensure you have the following installed:
-   PHP 8.2 or higher
-   Composer
-   Node.js & NPM
-   Laragon or XAMPP (MySQL)

### 2. Clone the Repository
```bash
git clone "Link"
cd "Folder"
```

### 3. Install Dependencies
```bash
composer install
npm install
```

### 4. Environment Configuration
Copy the `.env.example` file to `.env` and configure your database settings:
```bash
cp .env.example .env
```
Generate the application key:
```bash
php artisan key:generate
```

### 5. Database & Seeding
Prepare the database schema and populate it with initial data (roles, etc.):
```bash
php artisan migrate:fresh --seed
```

### 6. Storage & Infrastructure
This is critical for serving logos, avatars, and other media:
```bash
# Link the storage directory
php artisan storage:link

# Optimize and clear cache
composer dump-autoload
php artisan optimize:clear
```

## 👨‍💻 Development

Run the development servers:

### Start Vite (Frontend Assets)
```bash
npm run dev
```

### Start Laravel Server
```bash
php artisan serve
```

## 📦 Production Deployment

When deploying to production, remember to:
-   Run `npm run build` to compile assets.
-   Run `php artisan optimize` for better performance.
-   Ensure `storage/framework` directories exist and are writable.

## ⚠️ Troubleshooting

### "Please provide a valid cache path" (InvalidArgumentException)
This error occurs if the `storage/framework` subdirectories are missing. Laravel does not create these automatically in some environments.
**Fix**:
```bash
mkdir -p storage/framework/{sessions,views,cache}
chmod -R 775 storage bootstrap/cache
```

### Photos/Avatars not appearing
Ensure the symbolic link is created and the `APP_URL` in `.env` matches your local development URL.
**Fix**:
```bash
php artisan storage:link
```

## 🤝 Contributing

We welcome contributions! Please feel free to submit pull requests or open issues for bugs and feature requests.

---

Built with ❤️ by the JAVADEV Team.
