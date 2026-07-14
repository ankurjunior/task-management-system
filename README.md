# Task Management System (TMS)

A comprehensive, role-based Task Management System built with Laravel 11 and modern web technologies. TMS provides organizations with a powerful platform to manage tasks, track project progress, and maintain organizational hierarchy with real-time notifications and comprehensive task tracking.

## 🎯 Features

### Core Functionality
- **Task Management** - Create, update, and manage tasks with detailed tracking
- **Priority & Status Tracking** - Organize tasks by priority levels and current state
- **User Hierarchy** - Support organizational structure with reporting relationships
- **Role-Based Access Control** - Fine-grained permissions for different user roles
- **Real-time Notifications** - Instant notifications for task assignments and updates

### Task Management
- **Task Attachments** - Attach files to tasks for easy collaboration
- **Task Updates** - Track task progress with timestamped updates
- **Perennial Tasks** - Support for recurring tasks with configurable frequencies
- **Task Audit Logs** - Complete history of all task changes
- **Overdue Tracking** - Automatic overdue status management

### User Management
- **Multi-level Hierarchy** - Support for reporting structures and team organization
- **District-based Organization** - Group users by district/region
- **Designation Management** - Role-based designations with permissions
- **User Authentication** - Secure login with password management
- **Login Audit Logs** - Track user access history

### Dashboard & Analytics
- **Executive Dashboard** - Key metrics and task statistics
- **Task Statistics** - Open tasks, completed tasks, overdue tasks
- **Workload Overview** - View task distribution across team members
- **Task Filtering** - Filter by status, priority, date range

## 🛠️ Tech Stack

- **Backend**: Laravel 11 (PHP 8.x)
- **Frontend**: Blade Templates with Tailwind CSS
- **Database**: MySQL/PostgreSQL
- **Build Tool**: Vite
- **Frontend Framework**: Alpine.js/Vue.js
- **Authentication**: Laravel Sanctum
- **Queue System**: Redis/Database

## 📋 Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js 18.x or higher
- npm or yarn
- MySQL 8.0+ or PostgreSQL 12+
- Git

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone https://github.com/ankurjunior/tms.git
cd tms
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install JavaScript Dependencies
```bash
npm install
```

### 4. Copy Environment Configuration
```bash
cp .env.example .env
```

### 5. Generate Application Key
```bash
php artisan key:generate
```

### 6. Configure Database
Update your `.env` file with database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tms
DB_USERNAME=root
DB_PASSWORD=
```

### 7. Run Migrations
```bash
php artisan migrate
```

### 8. Seed Database (Optional)
```bash
php artisan db:seed
```

### 9. Build Frontend Assets
```bash
npm run build
```

### 10. Start Development Server
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## 📖 Configuration

### Mail Configuration
Update `.env` for email notifications:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
```

### Queue Configuration
Configure background jobs in `.env`:
```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## 💻 Usage

### Running Development Server
```bash
php artisan serve
```

### Watching Assets
```bash
npm run dev
```

### Running Tests
```bash
php artisan test
```

### Database Migrations
```bash
# Run all pending migrations
php artisan migrate

# Rollback last batch
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset
```

### Cache & Queue Management
```bash
# Clear application cache
php artisan cache:clear

# Process queued jobs
php artisan queue:work
```

## 📁 Project Structure

```
tms/
├── app/
│   ├── Console/           # Artisan commands
│   ├── Exceptions/        # Custom exceptions
│   ├── Http/
│   │   ├── Controllers/   # Application controllers
│   │   ├── Middleware/    # HTTP middleware
│   │   ├── Requests/      # Form request validations
│   │   └── Kernel.php
│   ├── Mail/              # Mailable classes
│   ├── Models/            # Eloquent models
│   ├── Providers/         # Service providers
│   └── View/
│       └── Components/    # Blade components
├── bootstrap/             # Framework bootstrap
├── config/                # Configuration files
├── database/
│   ├── factories/         # Model factories
│   ├── migrations/        # Database migrations
│   └── seeders/           # Database seeders
├── resources/
│   ├── css/               # Stylesheets
│   ├── js/                # JavaScript files
│   └── views/             # Blade templates
├── routes/                # Route definitions
├── storage/               # File storage
├── tests/                 # Test suites
└── vendor/                # Composer packages
```

## 🔑 Key Endpoints

### Authentication
- `GET /` - Login page
- `POST /logout` - Logout user

### Dashboard
- `GET /dashboard` - Main dashboard (authenticated)

### Task Management
- `GET /tasks` - List all tasks
- `GET /tasks/{task}` - View task details
- `POST /tasks` - Create new task
- `PATCH /tasks/{task}` - Update task
- `GET /tasks/create` - Task creation form

### User Management
- `GET /users` - List all users
- `GET /users/create` - User creation form
- `POST /users` - Create new user
- `GET /users/{user}/edit` - Edit user form
- `PATCH /users/{user}` - Update user

### Profile
- `GET /profile` - Edit profile
- `PATCH /profile` - Update profile
- `DELETE /profile` - Delete account

### Notifications
- `POST /notifications/{notification}/read` - Mark notification as read

## 📊 Database Schema

### Core Tables
- **users** - Application users with roles and hierarchy
- **tasks** - Main task records
- **task_updates** - Task progress updates
- **task_attachments** - File attachments for tasks
- **notifications** - User notifications
- **login_logs** - User login audit trail
- **task_audit_logs** - Task change history

### Master Tables
- **roles** - User roles
- **master_designations** - User designations
- **master_districts** - Geographic districts
- **master_task_states** - Task state definitions
- **master_task_priorities** - Priority levels
- **master_update_frequencies** - Update frequency options

## 🔐 Security

- Password hashing using bcrypt
- CSRF protection on all forms
- SQL injection prevention via Eloquent ORM
- XSS protection in Blade templates
- Rate limiting on authentication endpoints
- Secure password reset mechanism
- Email verification support

## 📝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📋 Code Standards

This project follows PSR-12 PHP coding standards:
- Proper namespace organization
- Comprehensive PHPDoc comments
- Type hints on all methods
- Consistent indentation and formatting
- Eloquent best practices for database queries

## 🐛 Known Issues & Limitations

- Real-time notifications require WebSocket configuration
- Large file attachments may require storage configuration optimization
- Email notifications require proper mail server configuration

## 🗺️ Roadmap

### Upcoming Features
- [ ] Kanban board view for visual task management
- [ ] Advanced search and filtering
- [ ] Email notification system
- [ ] Task templates for recurring patterns
- [ ] Task dependencies and relationships
- [ ] REST API endpoints
- [ ] Bulk import/export functionality
- [ ] Time tracking and estimation
- [ ] Custom reports and analytics

## 📞 Support

For issues, questions, or contributions, please:
1. Check existing GitHub issues
2. Create a new issue with detailed description
3. Include steps to reproduce the problem
4. Provide system information and error logs

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 👨‍💻 Author

**Your Team/Organization Name**
- Email: contact@example.com
- GitHub: [@ankurjunior](https://github.com/ankurjunior)

## 🙏 Acknowledgments

- Laravel Framework
- Tailwind CSS
- Vite
- All contributors and maintainers

---

**Version**: 1.0.0  
**Last Updated**: July 13, 2026  
**Status**: Active Development