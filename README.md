# Task Management API

A robust RESTful API for task management with role-based access control, built with Laravel 11 and JWT authentication.

## Features

- **JWT Authentication** - Stateless authentication with token refresh
- **Role-Based Access Control** - Managers and Users with different permissions
- **Task Management** - Full CRUD operations with filtering
- **Task Dependencies** - Prevent task completion until dependencies are met
- **Input Validation** - Comprehensive request validation
- **Error Handling** - Consistent API error responses
- **Rate Limiting** - Built-in protection against abuse
- **Docker Support** - Ready for containerization

## Requirements

- PHP 8.2+
- Composer
- MySQL 8.0+
- Redis (optional, for caching)

## Installation

### Using Composer (Traditional)

```bash
# Clone the repository
git clone <repository-url>
cd task-management

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Generate JWT secret
php artisan jwt:secret

# Run migrations
php artisan migrate

# Seed the database
php artisan db:seed

# Start the server
php artisan serve
```

### Using Docker

```bash
# Clone the repository
git clone <repository-url>
cd task-management

# Start containers
docker-compose up -d

# Install dependencies (first time only)
docker-compose exec app composer install

# Generate application key
docker-compose exec app php artisan key:generate

# Generate JWT secret
docker-compose exec app php artisan jwt:secret

# Run migrations
docker-compose exec app php artisan migrate

# Seed the database
docker-compose exec app php artisan db:seed
```

The API will be available at `http://localhost:8000`

## API Documentation

### Authentication

#### Login
```http
POST /api/login
Content-Type: application/json

{
    "email": "manager@example.com",
    "password": "password"
}
```

**Response:**
```json
{
    "data": {
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "token_type": "bearer",
        "expires_in": 3600
    },
    "message": "Login successful"
}
```

#### Get Current User
```http
GET /api/me
Authorization: Bearer {token}
```

#### Logout
```http
POST /api/logout
Authorization: Bearer {token}
```

#### Refresh Token
```http
POST /api/refresh
Authorization: Bearer {token}
```

### Tasks

#### List Tasks
```http
GET /api/tasks?status=completed&due_date_from=2024-01-01&assigned_to=2
Authorization: Bearer {token}
```

**Query Parameters:**
- `status` - Filter by status (pending, in_progress, completed, cancelled)
- `due_date_from` - Filter by due date from (YYYY-MM-DD)
- `due_date_to` - Filter by due date to (YYYY-MM-DD)
- `due_date` - Filter by exact due date (YYYY-MM-DD)
- `assigned_to` - Filter by assigned user ID

#### Get Task Details
```http
GET /api/tasks/{id}
Authorization: Bearer {token}
```

#### Create Task
```http
POST /api/tasks
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "New Task",
    "description": "Task description",
    "due_date": "2024-12-31",
    "assigned_to": 2
}
```

#### Update Task
```http
PUT /api/tasks/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
    "title": "Updated Task",
    "description": "Updated description",
    "status": "in_progress",
    "due_date": "2024-12-31",
    "assigned_to": 3
}
```

#### Assign Task
```http
POST /api/tasks/{id}/assign
Authorization: Bearer {token}
Content-Type: application/json

{
    "user_id": 3
}
```

#### Add Dependency
```http
POST /api/tasks/{id}/dependencies
Authorization: Bearer {token}
Content-Type: application/json

{
    "dependency_id": 5
}
```

#### Remove Dependency
```http
DELETE /api/tasks/{id}/dependencies
Authorization: Bearer {token}
Content-Type: application/json

{
    "dependency_id": 5
}
```

## Role-Based Access Control

### Manager Permissions
- ✅ Create, read, update, delete all tasks
- ✅ Assign tasks to users
- ✅ Manage task dependencies
- ✅ View all tasks with filtering

### User Permissions
- ✅ View only assigned tasks
- ✅ Update only status of assigned tasks
- ❌ Cannot complete tasks with incomplete dependencies

## Default Users

The system comes with pre-seeded users:

| Email | Password | Role |
|-------|----------|------|
| manager@example.com | password | Manager |
| user@example.com | password | User |
| user2@example.com | password | User |

## Error Responses

All error responses follow a consistent format:

```json
{
    "message": "Error description",
    "errors": {
        "field": ["Validation error message"]
    }
}
```

**Common HTTP Status Codes:**
- `200` - Success
- `201` - Created
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `429` - Too Many Requests
- `500` - Internal Server Error

## Rate Limiting

- **Login endpoint:** 5 requests per minute
- **Other endpoints:** 60 requests per minute

## Database Schema

### Tasks Table
- `id` - Primary key
- `title` - Task title (required, max 255 chars)
- `description` - Task description (max 1000 chars)
- `status` - pending, in_progress, completed, cancelled
- `due_date` - Due date
- `assigned_to` - Foreign key to users table
- `created_by` - Foreign key to users table
- `created_at`, `updated_at` - Timestamps

### Task Dependencies Table
- `id` - Primary key
- `task_id` - Foreign key to tasks table
- `dependency_task_id` - Foreign key to tasks table
- `created_at`, `updated_at` - Timestamps

## Testing

```bash
# Run tests
php artisan test

# Run tests with coverage
php artisan test --coverage
```

## Security Features

- JWT token authentication
- Role-based access control
- Input validation and sanitization
- Rate limiting
- CORS headers
- Security headers (XSS protection, content type options)
- SQL injection protection (Eloquent ORM)
- CSRF protection (API routes exempt)

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).