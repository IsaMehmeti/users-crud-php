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

### Base URL

```
http://localhost:8080/api
```

### Authentication

**JWT Authentication Required!** All user CRUD endpoints require authentication using JWT tokens.

**Auth Token Header:**

```
Authorization: Bearer {your_jwt_token_here}
```

---

## Authentication Endpoints

### 1. Register User

**POST** `/auth/register`

**Request Body:**

```json
{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john.doe@example.com",
    "password": "securepassword123"
}
```

**Success Response (201):**

```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "first_name": "John",
            "last_name": "Doe",
            "email": "john.doe@example.com",
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z"
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "token_type": "bearer",
        "expires_in": 3600
    }
}
```

---

### 2. Login

**POST** `/auth/login`

**Request Body:**

```json
{
    "email": "john.doe@example.com",
    "password": "securepassword123"
}
```

**Success Response (200):**

```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "first_name": "John",
            "last_name": "Doe",
            "email": "john.doe@example.com",
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z"
        },
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "token_type": "bearer",
        "expires_in": 3600
    }
}
```

---

### 3. Get Current User

**GET** `/auth/me`

**Headers:** `Authorization: Bearer {token}`

**Success Response (200):**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "first_name": "John",
        "last_name": "Doe",
        "email": "john.doe@example.com",
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

---

### 4. Refresh Token

**POST** `/auth/refresh`

**Headers:** `Authorization: Bearer {token}`

**Success Response (200):**

```json
{
    "success": true,
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "token_type": "bearer",
        "expires_in": 3600
    }
}
```

---

### 5. Logout

**POST** `/auth/logout`

**Headers:** `Authorization: Bearer {token}`

**Success Response (200):**

```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

---

## User CRUD Endpoints (Protected)

### 1. Create User

**POST** `/users`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**

```json
{
    "first_name": "John",
    "last_name": "Doe",
    "email": "john.doe@example.com",
    "password": "securepassword123"
}
```

**Success Response (201):**

```json
{
    "success": true,
    "message": "User created successfully",
    "data": {
        "id": 1,
        "first_name": "John",
        "last_name": "Doe",
        "email": "john.doe@example.com",
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

---

### 2. Get All Users (with Pagination)

**GET** `/users?page=1&limit=10`

**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**

-   `page` (optional): Page number (default: 1)
-   `limit` (optional): Items per page (default: 10, max: 100)

**Success Response (200):**

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "first_name": "John",
            "last_name": "Doe",
            "email": "john.doe@example.com",
            "created_at": "2024-01-15T10:30:00.000000Z",
            "updated_at": "2024-01-15T10:30:00.000000Z"
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 10,
        "total": 1,
        "total_pages": 1
    }
}
```

---

### 3. Get User by ID

**GET** `/users/{id}`

**Headers:** `Authorization: Bearer {token}`

**Success Response (200):**

```json
{
    "success": true,
    "data": {
        "id": 1,
        "first_name": "John",
        "last_name": "Doe",
        "email": "john.doe@example.com",
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:30:00.000000Z"
    }
}
```

---

### 4. Update User

**PUT** `/users/{id}`

**Headers:** `Authorization: Bearer {token}`

**Request Body:**

```json
{
    "first_name": "Jane",
    "last_name": "Updated",
    "email": "jane.updated@example.com",
    "password": "newpassword123"
}
```

**Success Response (200):**

```json
{
    "success": true,
    "message": "User updated successfully",
    "data": {
        "id": 1,
        "first_name": "Jane",
        "last_name": "Updated",
        "email": "jane.updated@example.com",
        "created_at": "2024-01-15T10:30:00.000000Z",
        "updated_at": "2024-01-15T10:35:00.000000Z"
    }
}
```

---

### 5. Delete User

**DELETE** `/users/{id}`

**Headers:** `Authorization: Bearer {token}`

**Success Response (200):**

```json
{
    "success": true,
    "message": "User deleted successfully"
}
```

---

A complete Postman collection is available.
