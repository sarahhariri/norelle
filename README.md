# NORELLE

A fashion boutique demo built with React and a Laravel API, featuring a responsive storefront and an admin dashboard.

## Features

- Browse collections and filter products by category
- Search products and view product details
- Save favourites to a wishlist
- Manage a shopping bag with size and stock limits
- Place cash-on-delivery orders
- Manage products, categories, images and stock through the admin dashboard
- View orders and update their status

## Tech Stack

- React 19 and React Router 8
- Bootstrap 5 and Bootstrap Icons
- Laravel 13
- Vite 8

## Project Structure

- `norelle-api/` — Laravel API and Blade admin dashboard
- `norelle-frontend/` — React storefront

## Requirements

- PHP compatible with the Composer dependencies
- Composer
- Node.js compatible with Vite 8 and npm
- A database supported by Laravel

## Backend Setup

```bash
cd norelle-api
composer install
php -r "copy('.env.example', '.env');"
php artisan key:generate
```

Configure the database connection in `.env` and create the database.

For a new, empty database, run:

```bash
php artisan migrate --seed
php artisan storage:link
```

The seeders create seven categories and four sample products with S, M and L variants.

The variant seeder resets sample stock to five per size when rerun.

### Create an Admin Account

Run:

```bash
php artisan tinker
```

Then enter the following, replacing the email and password with your own local credentials:

```php
$user = new App\Models\User();
$user->name = 'Store Admin';
$user->email = 'your-email@example.com';
$user->password = 'replace-with-your-own-password';
$user->is_admin = true;
$user->save();
exit
```

Start the backend:

```bash
php artisan serve
```

Admin login: http://127.0.0.1:8000/admin/login

## Frontend Setup

Open another terminal:

```bash
cd norelle-frontend
npm ci
```

Copy `.env.example` to `.env` and set:

```env
VITE_API_URL=http://127.0.0.1:8000
```

Start the frontend:

```bash
npm run dev
```

Open the local URL displayed in the terminal.

## Images and Demo Data

Four sample product images are included in `norelle-api/public/products`.

Category images and additional products can be added through the admin dashboard.

Uploaded images and the local database are excluded from Git. A fresh installation will contain the seeded sample catalog, rather than the complete catalog shown in project videos.

## Frontend Checks

```bash
npm run lint
npm run build
```

## Author

Sarah Hariri — Smart Web Development