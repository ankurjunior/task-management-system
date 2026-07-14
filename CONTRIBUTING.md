# Contributing to Task Management System (TMS)

Thank you for considering contributing to the Task Management System project! We're excited to collaborate with you. This document provides guidelines and instructions for contributing.

## Code of Conduct

This project and everyone participating in it is governed by our Code of Conduct. By participating, you are expected to uphold this code.

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the issue list as you might find out that you don't need to create one. When you are creating a bug report, please include as many details as possible:

* **Use a clear and descriptive title**
* **Describe the exact steps which reproduce the problem**
* **Provide specific examples to demonstrate the steps**
* **Describe the behavior you observed after following the steps**
* **Explain which behavior you expected to see instead and why**
* **Include screenshots and animated GIFs if possible**
* **Include your environment details** (OS, PHP version, Laravel version, etc.)

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. Please provide:

* **A clear and descriptive title**
* **A step-by-step description of the suggested enhancement**
* **Specific examples to demonstrate the steps**
* **Explanation of why this enhancement would be useful**
* **Links to related features or similar functionality in other projects**

### Pull Requests

Please follow these steps:

1. Fork the repository and create your branch from `main`
2. Follow the code style and standards outlined below
3. Make your changes with clear, atomic commits
4. Write or update tests as needed
5. Update documentation as appropriate
6. Submit your pull request with a clear description

## Development Setup

### Prerequisites
- PHP 8.1+
- Node.js 18+
- Composer
- npm or yarn

### Steps

1. Fork and clone the repository:
```bash
git clone https://github.com/ankurjunior/tms.git
cd tms
```

2. Create a feature branch:
```bash
git checkout -b feature/your-feature-name
```

3. Install dependencies:
```bash
composer install
npm install
```

4. Copy environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Create test database and run migrations:
```bash
php artisan migrate:fresh --seed
```

7. Start development servers:
```bash
php artisan serve
npm run dev
```

## Code Standards

### PHP Code Style

This project follows **PSR-12** coding standards. Key points:

- Use 4 spaces for indentation (not tabs)
- Maximum line length of 120 characters
- Use meaningful variable names
- Add PHPDoc comments to all public methods and classes

Example:
```php
/**
 * Create a new task.
 *
 * @param string $name The task name
 * @param string $description The task description
 * @return Task
 */
public function createTask(string $name, string $description): Task
{
    return Task::create([
        'task_name' => $name,
        'task_details' => $description,
    ]);
}
```

### Run PHP Linter/Formatter

```bash
# Check code style
./vendor/bin/pint --test

# Fix code style
./vendor/bin/pint
```

### JavaScript/CSS

- Use consistent indentation (4 spaces)
- Use meaningful variable names
- Add comments for complex logic
- Follow Tailwind CSS conventions

## Commit Messages

Write clear, descriptive commit messages:

- Use imperative mood ("Add feature" not "Added feature")
- Start with a capital letter
- Keep the first line to 50 characters or less
- Reference issues and pull requests when applicable

Example:
```
Add task filtering by priority and date range

- Implement advanced filtering in TaskController
- Add filter UI components to task index view
- Write tests for filtering functionality

Closes #123
```

## Testing

### Run Tests
```bash
php artisan test
```

### Write Tests

Create tests in `tests/Feature` or `tests/Unit` directories:

```php
<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_task(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/tasks', [
                'task_name' => 'Test Task',
                'task_details' => 'Test Details',
                'priority_id' => 1,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'task_name' => 'Test Task',
        ]);
    }
}
```

## Documentation

### Update Documentation

- Update relevant documentation files
- Add comments to your code
- Update the README if adding major features
- Document any new configuration options in `.env.example`

### DocBlocks

All public classes, interfaces, and methods should have DocBlocks:

```php
/**
 * Task Model
 *
 * Represents a task in the application with attachments and updates.
 *
 * @package App\Models
 */
class Task extends Model
{
    // ...
}
```

## Database Migrations

When modifying the database:

1. Create a new migration:
```bash
php artisan make:migration create_new_table
```

2. Write reversible migrations:
```php
public function up(): void
{
    Schema::create('new_table', function (Blueprint $table) {
        $table->id();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('new_table');
}
```

## Request Process

1. **Submit PR**: Create a pull request with a clear description
2. **Discussion**: Repository maintainers will review and discuss
3. **Revisions**: Make requested changes in subsequent commits
4. **Approval**: Once approved, your changes will be merged

## Changelog

Maintain updates to `CHANGELOG.md` for significant changes:

```markdown
## [1.1.0] - 2026-07-15

### Added
- New task filtering feature
- API endpoint for task export

### Fixed
- Task notification bug
- Dashboard loading issue

### Changed
- Updated task validation rules
```

## Questions?

Feel free to create an issue for questions or reach out to the maintainers:

- Email: contact@example.com
- GitHub Issues: [Create an issue](https://github.com/ankurjunior/tms/issues)

## Additional Notes

- Be respectful and constructive in all interactions
- Keep discussions focused and professional
- Help fellow contributors when you can
- Share knowledge and best practices

Thank you for contributing to making TMS better! 🎉
