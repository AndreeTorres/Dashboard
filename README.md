# 🍽️ Karina's Dashboard

**Comprehensive restaurant management system with advanced analytics**

Karina's Dashboard is a modern web application built with Laravel and Filament PHP that enables complete restaurant management, including products, orders, supplies, expenses, and detailed statistical analysis with interactive charts.

## 📋 Table of Contents

-   [Features](#-features)
-   [Technologies](#-technologies)
-   [Prerequisites](#-prerequisites)
-   [Installation](#-installation)
    -   [Option A: With Laravel Sail (Docker)](#option-a-with-laravel-sail-docker-recommended)
    -   [Option B: Local Installation](#option-b-local-installation)
-   [Configuration](#-configuration)
-   [Usage](#-usage)
-   [Project Structure](#-project-structure)
-   [API & Endpoints](#-api--endpoints)
-   [Contributing](#-contributing)
-   [License](#-license)

## ✨ Features

### 🏪 Restaurant Management

-   **Products**: Complete catalog with categories and subcategories
-   **Orders**: Order system with details and status tracking
-   **Tables**: Restaurant table management
-   **Supplies**: Supply management with categories

### 💰 Expense Management

-   **Expenses**: Detailed expense tracking by supplies
-   **Supply Categories**: Organization of supply types

### 📊 Analytics & Reports

-   **Statistics Dashboard**: Real-time key metrics and overview
-   **Interactive Charts**:
    -   Sales vs expenses trends (30 days)
    -   Monthly income and expenses comparison
    -   Expense distribution by category

### 🔐 User Management

-   **Secure Authentication**: Login system with Filament Breezy
-   **Roles & Permissions**: Access control with Filament Shield
-   **User Profiles**: Complete user management

### 🌐 Modern Interface

-   **Admin Panel**: Intuitive interface with Filament PHP
-   **Responsive Design**: Mobile device compatible
-   **Multi-language**: Full Spanish support
-   **Modern Theme**: Clean and professional design

## 🛠️ Technologies

### Backend

-   **Laravel 12** - Modern PHP framework
-   **PHP 8.2+** - Programming language
-   **MySQL/SQLite** - Database
-   **Eloquent ORM** - Object-relational mapping

### Frontend & UI

-   **Filament PHP 3.3** - Modern admin panel
-   **Tailwind CSS 4.0** - Utility-first CSS framework
-   **Livewire** - Dynamic components
-   **Chart.js** - Interactive charts
-   **Alpine.js** - Reactive JavaScript

### Tools & Utilities

-   **Laravel Sail** - Docker development environment
-   **Vite 6.2** - Build tool and bundler
-   **Faker** - Test data generation
-   **Laravel Tinker** - Interactive REPL

### Specialized Packages

-   **Filament Shield** - Role and permission management
-   **Filament Breezy** - Authentication system
-   **Laravel Lang** - Spanish localizations

## 📋 Prerequisites

### Option A: With Docker (Recommended)

-   **Docker Desktop** 4.0+
-   **Git** 2.0+
-   **Node.js** 18+ and npm 8+

### Option B: Local Installation

-   **PHP** 8.2 or higher
-   **Composer** 2.0+
-   **Node.js** 18+ and npm 8+
-   **MySQL** 8.0+ or **SQLite** 3.8+
-   **Git** 2.0+

### Required PHP Extensions

```bash
# Check installed extensions
php -m | grep -E "(pdo|pdo_mysql|pdo_sqlite|mbstring|xml|ctype|json|bcmath|openssl|tokenizer|fileinfo)"
```

## 🚀 Installation

### Option A: With Laravel Sail (Docker) - Recommended

#### 1. Clone the repository

```bash
git clone https://github.com/your-username/karinas-dashboard.git
cd karinas-dashboard
```

#### 2. Install PHP dependencies

```bash
# If you have Composer installed locally
composer install

# Or using Docker without local Composer
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php82-composer:latest \
    composer install --ignore-platform-reqs
```

#### 3. Configure environment variables

```bash
cp .env.example .env
```

#### 4. Generate application key

```bash
# With Sail (recommended)
./vendor/bin/sail artisan key:generate

# Without Sail
php artisan key:generate
```

#### 5. Start the environment with Sail

```bash
# Build and start containers
./vendor/bin/sail up -d

# Verify containers are running
./vendor/bin/sail ps
```

#### 6. Install Node.js dependencies

```bash
./vendor/bin/sail npm install
```

#### 7. Run migrations and seeders

```bash
# Create database and populate with test data
./vendor/bin/sail artisan migrate:fresh --seed
```

#### 8. Compile assets

```bash
# For development
./vendor/bin/sail npm run dev

# For production
./vendor/bin/sail npm run build
```

#### 9. Create admin user

```bash
./vendor/bin/sail artisan make:filament-user
```

### Option B: Local Installation

#### 1. Clone the repository

```bash
git clone https://github.com/your-username/karinas-dashboard.git
cd karinas-dashboard
```

#### 2. Install dependencies

```bash
# PHP dependencies
composer install

# Node.js dependencies
npm install
```

#### 3. Configure environment variables

```bash
cp .env.example .env
```

Edit `.env` with your database configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=karinas_dashboard
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

#### 4. Generate key and run migrations

```bash
# Generate application key
php artisan key:generate

# Create database and populate with data
php artisan migrate:fresh --seed
```

#### 5. Compile assets and run server

```bash
# Compile assets for development
npm run dev

# In another terminal, run development server
php artisan serve
```

#### 6. Create admin user

```bash
php artisan make:filament-user
```

## ⚙️ Configuration

### Important Environment Variables

```env
# Application
APP_NAME="Karina's Dashboard"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=karinas_dashboard
DB_USERNAME=root
DB_PASSWORD=

# Filament Configuration
FILAMENT_PATH=admin

# Language Configuration
APP_LOCALE=es
APP_FALLBACK_LOCALE=en
```

### Database Configuration

#### For MySQL:

```bash
# Create database
mysql -u root -p
CREATE DATABASE karinas_dashboard CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### For SQLite (simple alternative):

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

### Permissions (Linux/macOS)

```bash
# Give permissions to storage directories
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## 🎯 Usage

### System Access

1. **Admin Panel**: `http://localhost/admin` (or your configured port)
2. **Credentials**: Use the admin user you created

### Main Navigation

-   **🏠 Home**: Main dashboard with informative widgets
-   **💰 Sales**: Order and sales management
-   **🍽️ Restaurant Management**:
    -   Products and categories
    -   Subcategories
    -   Tables
-   **📦 Supply Management**:
    -   Expenses and records
    -   Supplies and inventory
    -   Supply categories
-   **📊 Reports**: Statistics page with interactive charts
-   **👥 Access Management**: Users and roles

### Key Features

#### Product Management

1. Create main categories
2. Add specific subcategories
3. Register products with prices and details

#### Order Registration

1. Select table and products
2. Calculate totals automatically
3. Manage order statuses

#### Expense Control

1. Register expenses by supplies
2. Categorize by supply type
3. Analyze costs and trends

#### Statistics Analysis

1. View real-time key metrics
2. Analyze trends with charts
3. Compare periods and categories

## 📁 Project Structure

```
karinas-dashboard/
├── app/
│   ├── Enums/                 # System enumerations
│   ├── Filament/
│   │   ├── Pages/             # Custom pages
│   │   ├── Resources/         # CRUD resources
│   │   └── Widgets/           # Widgets and charts
│   ├── Http/Controllers/      # Controllers
│   ├── Models/                # Eloquent models
│   └── Providers/             # Service providers
├── database/
│   ├── factories/             # Data factories
│   ├── migrations/            # Database migrations
│   └── seeders/               # Data seeders
├── lang/
│   ├── es.json               # Spanish translations
│   └── en/                   # English translations
├── resources/
│   ├── css/                  # CSS styles
│   ├── js/                   # JavaScript
│   └── views/                # Blade views
└── routes/                   # Route definitions
```

### Main Models

-   **User**: System users
-   **Category/Subcategory**: Product categories
-   **Product**: Menu products
-   **Order/OrderDetail**: Orders and details
-   **Table**: Restaurant tables
-   **SupplyCategory/Supply**: Categories and supplies
-   **Expense**: Expense records

## 🔗 API & Endpoints

### Main Web Routes

-   `/admin` - Filament admin panel
-   `/admin/login` - Login page
-   `/admin/statistics` - Statistics and reports page

### Custom Artisan Commands

```bash
# Generate test data
./vendor/bin/sail artisan db:seed

# Clear cache
./vendor/bin/sail artisan optimize:clear

# Create Filament user
./vendor/bin/sail artisan make:filament-user
```

## 🤝 Contributing

### Contribution Process

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/new-feature`)
3. **Commit** your changes (`git commit -am 'Add new feature'`)
4. **Push** to the branch (`git push origin feature/new-feature`)
5. **Create** a Pull Request

### Code Standards

-   Follow **PSR-12** for PHP
-   Use **Laravel Pint** for automatic formatting
-   Write **tests** for new features
-   Document **important changes**

### Run Tests

```bash
# With Sail
./vendor/bin/sail test

# Local
php artisan test
```

### Format Code

```bash
# With Sail
./vendor/bin/sail pint

# Local
./vendor/bin/pint
```

## 📝 License

This project is licensed under the [MIT License](https://opensource.org/licenses/MIT).

## 🆘 Support & Help

### Common Issues

**Storage permission errors**

```bash
chmod -R 755 storage bootstrap/cache
```

**Database errors**

```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

**Uncompiled assets errors**

```bash
./vendor/bin/sail npm run build
```

### Useful Links

-   [Laravel Documentation](https://laravel.com/docs)
-   [Filament Documentation](https://filamentphp.com/docs)
-   [Laravel Sail](https://laravel.com/docs/sail)
-   [Tailwind CSS](https://tailwindcss.com/docs)

---

<p align="center">
Developed with ❤️ for modern restaurant management
</p>
