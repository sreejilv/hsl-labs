# 🧬 HSL LABS Provider Management System

## 📖 Overview

This project is a Laravel 12 application built to help **Licensed Providers (Plastic Surgeons)** manage their supplement distribution business for **HSL LABS**.  
The system supports:

-   Managing **inventory**, **patients**, and **subscriptions**
-   Recording **payments** and **renewals**
-   Tracking **surgical timelines**
-   Viewing **real-time billing and data analytics**

The app is built using **Laravel 12** with **Livewire**, **Spatie Roles & Permissions**, and **MySQL** as the preferred database.

---

## ⚙️ System Requirements

| Requirement    | Version / Details |
| -------------- | ----------------- |
| **PHP**        | ^8.2 or higher    |
| **Laravel**    | 12.x              |
| **Composer**   | 2.5 or higher     |
| **MySQL**      | 8.0 or higher     |
| **Node.js**    | 18.x or higher    |
| **NPM / Yarn** | Latest            |

---

## 🧩 Required Composer Packages

-   `livewire/livewire`
-   `spatie/laravel-permission`
-   `laravel/ui`
-   `laravel/sanctum`
-   `fakerphp/faker`
-   `guzzlehttp/guzzle`
-   `doctrine/dbal`

Install all dependencies using the command below.

---

## 🪄 Installation Steps

### 1️⃣ Clone the Repository

```bash
git clone https://github.com/sreejilv/hsl-labs.git
cd hsl-labs
```

---

### 2️⃣ Install Dependencies

```bash
composer update
npm install
npm run dev
```

---

### 3️⃣ Create `.env` File

Duplicate the `.env.example` file and rename it to `.env`:

```bash
cp .env.example .env
```

---

### 4️⃣ Set Database Connection

Update your `.env` file with your MySQL credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hsl_labs
DB_USERNAME=root
DB_PASSWORD=
```

Make sure your MySQL server is running and the database `hsl_labs` is created.
set the host, and db_username and db_password based on your environment

---

### 5️⃣ Generate Application Key

```bash
php artisan key:generate
```

---

### 6️⃣ Run Migrations & Seeders

To set up all necessary tables and seed initial data:

```bash
php artisan migrate --seed
```

### 7️⃣ Run the Application Locally

```bash
php artisan serve
```

Visit: [http://localhost:8000](http://localhost:8000)

---

## 🔐 User Login Information

The system includes multiple user roles with different access levels. After running the seeders, you can log in using the following credentials:

### 🏥 Medical Portal Login

**URL:** [http://localhost:8000/login](http://localhost:8000/login)

#### 👨‍⚕️ Surgeon Account

-   **Email:** `surgeon@example.com`
-   **Password:** `surgeon123`
-   **Access:** Full medical portal access including:
    -   Patient management (Create, Read, Update, Delete)
    -   Staff management
    -   Medical dashboard
    -   All medical portal features

#### 👩‍💼 Staff Account

-   **Email:** `staff@example.com`
-   **Password:** `staff123`
-   **Access:** Limited medical portal access including:
    -   Medical dashboard
    -   Order medical products

### 🛠️ Admin Portal Login

**URL:** [http://localhost:8000/admin/login](http://localhost:8000/admin/login)

#### 👨‍💻 Administrator Account

-   **Email:** `admin@example.com`
-   **Password:** `admin123`
-   **Access:** Full administrative access including:
    -   Surgeon registration and management
    -   System settings
    -   Account management
    -   Product and order management
    -   Admin dashboard
    -   All administrative features

---

## 🧱 Running with Docker

### Quick Setup (Recommended)

The easiest way to run HSL Labs locally is using the automated setup script:

```bash
# Make setup script executable and run
chmod +x docker-setup.sh
./docker-setup.sh
```

This script will:

-   ✅ Set up environment configuration
-   ✅ Build and start all Docker containers
-   ✅ Install PHP and Node.js dependencies
-   ✅ Run database migrations and seeders
-   ✅ Build frontend assets
-   ✅ Set proper file permissions

### Manual Docker Setup

If you prefer manual setup:

```bash
# Copy environment file
cp .env.docker .env

# Build and start containers
docker-compose up -d --build

# Install dependencies and setup database
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --seed

# Build assets
docker-compose exec node npm install && npm run build
```

### 🌐 Access Points

Once containers are running:

| Service              | URL                   | Purpose                 |
| -------------------- | --------------------- | ----------------------- |
| **Main Application** | http://localhost      | HSL Labs Medical Portal |
| **PHPMyAdmin**       | http://localhost:8080 | Database Management     |

### 🐳 Docker Services

The Docker setup includes:

-   **app**: Laravel application (PHP 8.2-FPM)
-   **nginx**: Web server (Nginx)
-   **mysql**: Database (MySQL 8.0)
-   **redis**: Cache and session storage
-   **queue**: Queue worker for background jobs
-   **scheduler**: Cron job scheduler
-   **phpmyadmin**: Database management interface

### 📋 Database Connection

| Setting      | Value        |
| ------------ | ------------ |
| **Host**     | localhost    |
| **Port**     | 3306         |
| **Database** | hsl_labs     |
| **Username** | hsl_user     |
| **Password** | hsl_password |

### 🔧 Useful Docker Commands

```bash
# View logs
docker-compose logs -f app

# Access application shell
docker-compose exec app bash

# Stop all services
docker-compose down

# Restart services
docker-compose restart

# Update dependencies
docker-compose exec app composer update
```

> 📚 For detailed Docker commands and troubleshooting, see `docker/DOCKER_COMMANDS.md`

---

## 🧩 Feature Testing

You can run vertical slice development locally by running:

```bash
php artisan serve
```

## 🧠 Notes

-   To refresh database:
    ```bash
    php artisan migrate:fresh --seed
    ```
    The files PLAN.md and ARCHITECTURE.md have been added to the documents folder. Additionally, the ER diagram and sample SQL files have also been included in the same folder.

---
