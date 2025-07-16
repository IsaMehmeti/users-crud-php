# Users CRUD API

## Technologies Used

-   **Backend**: Laravel 12.x (PHP 8.2+)
-   **Database**: MySQL 8.0 with stored procedures
-   **Authentication**: JWT auth
-   **Containerization**: Docker & Docker Compose
-   **Database Management**: phpMyAdmin (included)

## Features

-   User CRUD operations with RESTful API
-   MySQL stored procedures
-   JWT authentication
-   Pagination
-   Error handling
-   Docker containerization

## Database Schema

### Users Table

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Stored Procedures

-   `create_user`
-   `get_user_by_id`
-   `get_user_by_email`
-   `get_all_users`
-   `update_user`
-   `delete_user`

## Quick Start (with Docker)

### Step 1: Clone the Repository

```bash
git clone <https://github.com/IsaMehmeti/users-crud-php.git>
```

### Step 2: Environment Setup

Create a `.env` file from the example:

```bash
cp .env.example .env
```

**Important:** Generate a JWT secret key:

```bash
# Generate a random 32-character secret
openssl rand -base64 32
```

Add this to your `.env` file:

```env
JWT_SECRET=your_generated_secret_here
```

### Step 3: Build and Run with Docker Compose

```bash
# Build and start all services
docker-compose up --build

# Or run in detached mode
docker-compose up -d --build
```

### Step 4: Wait for Services to Start

-   **API**: http://localhost:8080
-   **phpMyAdmin**: http://localhost:8081
-   **MySQL**: localhost:3306

## Manual Setup (without Docker)

### Prerequisites

-   PHP 8.2+
-   Composer
-   MySQL 8.0+
-   Apache/Nginx

### Step 1: Install Dependencies

```bash
composer install
```

### Step 2: Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Generate JWT secret
php artisan jwt:secret
```

### Step 3: Configure Database

Update your `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=company_db
DB_USERNAME=your_username
DB_PASSWORD=your_password

JWT_SECRET=your_jwt_secret_here
JWT_TTL=60
JWT_REFRESH_TTL=20160
```

### Step 4: Import Database Schema

```bash
# Connect to MySQL and run:
mysql -u your_username -p < database/company_db_schema.sql
```

### Step 5: Start Development Server

```bash
php artisan serve
```

## API Documentation

**A complete Postman collection is available.**
