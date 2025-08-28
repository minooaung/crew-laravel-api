# Min Lara React Backend API

A Laravel-based REST API that provides authentication, user management, and organization management functionality with role-based access control.

## Features

### Authentication

-   Sanctum-based authentication with CSRF protection
-   User registration (signup) with default EMPLOYEE role
-   Login with session-based authentication
-   Secure logout with session invalidation
-   Session inactivity timeout

### Dashboard

-   Real-time statistics and metrics
-   Organization growth tracking over time
-   User role distribution visualization
-   Quick action shortcuts for common tasks
-   Key performance indicators (KPIs)
    -   Total users count
    -   Total organizations count
    -   Recent activity metrics
    -   User role distribution stats

### User Management

-   CRUD operations for users
-   Role-based access (ADMIN and EMPLOYEE roles)
-   Admin user limit (max 5 admins)
-   Secure password hashing
-   Search users by ID, name, or email
-   Pagination support (10 users per page)

### Organization Management

-   CRUD operations for organizations
-   User-organization relationships with pivot table
-   Track user assignments with 'assigned_by' attribute
-   Search organizations by ID or name
-   Pagination support (10 organizations per page)

### Report Generation

-   Generate reports in multiple formats (CSV, Excel, HTML, JSON, PDF)
-   Customizable field rendering with dedicated renderers
-   Support for default and specialized field formatting
-   Extensible architecture for adding new report formats
-   Field value transformation and formatting

### Security

-   Role-based access control via policies
-   Input validation using Form Requests
-   CSRF protection
-   Database transaction support
-   Foreign key constraint checks
-   Input sanitization middleware
-   Secure session handling

## API Endpoints

### Authentication Endpoints

```
POST /api/signup     - Register a new user
POST /api/login      - Authenticate user
POST /api/logout     - Logout user (authenticated)
GET  /api/user       - Get authenticated user
```

### Dashboard Endpoints

```
GET  /api/dashboard/stats     - Get dashboard statistics and metrics
```

### User Management Endpoints

```
GET    /api/users            - List users (with search & pagination)
GET    /api/users/{id}       - Get specific user
POST   /api/users            - Create user (admin only)
PUT    /api/users/{id}       - Update user
DELETE /api/users/{id}       - Delete user
```

### Organization Management Endpoints

```
GET    /api/organisations            - List organizations (with search & pagination)
GET    /api/organisations/{id}       - Get specific organization with users
POST   /api/organisations           - Create organization (admin only)
PUT    /api/organisations/{id}      - Update organization
DELETE /api/organisations/{id}      - Delete organization
```

### Report Generation Endpoints

```
POST   /api/reports            - Generate report in specified format (CSV/Excel/HTML/JSON/PDF)
```

## Technical Stack

-   Laravel 10.x
-   MySQL/MariaDB
-   Laravel Sanctum for authentication
-   PHP 8.x

## Development Setup

1. Install dependencies:

    ```bash
    composer install
    ```

2. Configure environment:

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

3. Configure database in `.env`:

    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database
    DB_USERNAME=your_username
    DB_PASSWORD=your_password
    ```

4. Run migrations and seeders:

    ```bash
    php artisan migrate
    php artisan db:seed
    ```

5. Start development server:
    ```bash
    php artisan serve
    ```

## Testing

Run the test suite:

```bash
php artisan test
```

Key test files:

-   `tests/Feature/OrganisationPolicyTest.php`
-   `tests/Feature/UserPolicyTest.php`

## Key Dependencies

-   `laravel/sanctum` - API authentication
-   `laravel/framework` - Core framework
-   Additional packages listed in `composer.json`

## Security Features

-   CSRF Protection via Sanctum
-   Request validation and sanitization
-   Role-based access control
-   Secure password hashing
-   Session management
-   Database transaction safety
-   Input sanitization middleware
