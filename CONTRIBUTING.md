# Contributing to KV-ERP-CRM-SaaS

First off, thank you for considering contributing to KV-ERP-CRM-SaaS! It's people like you that make this project such a great tool.

## Table of Contents

1. [Code of Conduct](#code-of-conduct)
2. [Getting Started](#getting-started)
3. [How Can I Contribute?](#how-can-i-contribute)
4. [Development Workflow](#development-workflow)
5. [Coding Standards](#coding-standards)
6. [Testing Guidelines](#testing-guidelines)
7. [Documentation](#documentation)
8. [Commit Messages](#commit-messages)
9. [Pull Request Process](#pull-request-process)
10. [Community](#community)

## Code of Conduct

This project and everyone participating in it is governed by our Code of Conduct. By participating, you are expected to uphold this code. Please report unacceptable behavior to the project maintainers.

### Our Standards

**Examples of behavior that contributes to creating a positive environment include:**
- Using welcoming and inclusive language
- Being respectful of differing viewpoints and experiences
- Gracefully accepting constructive criticism
- Focusing on what is best for the community
- Showing empathy towards other community members

**Examples of unacceptable behavior include:**
- The use of sexualized language or imagery
- Trolling, insulting/derogatory comments, and personal or political attacks
- Public or private harassment
- Publishing others' private information without explicit permission
- Other conduct which could reasonably be considered inappropriate

## Getting Started

### Prerequisites

Before you begin, ensure you have:
- PHP 8.2 or higher
- Composer 2.x
- Node.js 18 LTS or higher
- PostgreSQL 15+ or MySQL 8+
- Redis 7+
- Git

### Setting Up Your Development Environment

1. **Fork the repository** on GitHub
2. **Clone your fork** locally:
   ```bash
   git clone https://github.com/YOUR_USERNAME/kv-erp-crm-saas.git
   cd kv-erp-crm-saas
   ```
3. **Add the upstream repository**:
   ```bash
   git remote add upstream https://github.com/kasunvimarshana/kv-erp-crm-saas.git
   ```
4. **Install dependencies**:
   ```bash
   composer install
   npm install
   ```
5. **Set up environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
6. **Run migrations**:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```
7. **Build assets**:
   ```bash
   npm run dev
   ```

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the issue list as you might find out that you don't need to create one. When you are creating a bug report, please include as many details as possible:

- **Use a clear and descriptive title**
- **Describe the exact steps to reproduce the problem**
- **Provide specific examples** to demonstrate the steps
- **Describe the behavior you observed** and what behavior you expected
- **Include screenshots or animated GIFs** if applicable
- **Include your environment details** (OS, PHP version, database version, etc.)

**Bug Report Template:**

```markdown
## Bug Description
A clear and concise description of what the bug is.

## Steps to Reproduce
1. Go to '...'
2. Click on '....'
3. Scroll down to '....'
4. See error

## Expected Behavior
A clear description of what you expected to happen.

## Actual Behavior
What actually happened.

## Screenshots
If applicable, add screenshots.

## Environment
- OS: [e.g., Ubuntu 22.04]
- PHP Version: [e.g., 8.2.10]
- Laravel Version: [e.g., 10.x]
- Database: [e.g., PostgreSQL 15.4]
- Browser: [e.g., Chrome 120]

## Additional Context
Add any other context about the problem here.
```

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion, please include:

- **Use a clear and descriptive title**
- **Provide a detailed description** of the suggested enhancement
- **Explain why this enhancement would be useful**
- **List some examples** of how it would be used
- **Describe alternatives** you've considered

### Your First Code Contribution

Unsure where to begin? You can start by looking through `beginner` and `help-wanted` issues:

- **Beginner issues** - issues which should only require a few lines of code
- **Help wanted issues** - issues which should be a bit more involved

### Pull Requests

- Fill in the required template
- Do not include issue numbers in the PR title
- Include screenshots and animated GIFs in your pull request whenever possible
- Follow the PHP, JavaScript, and CSS styleguides
- Document new code
- End all files with a newline

## Development Workflow

### Branch Naming Convention

Use the following prefixes:
- `feature/` - New features (e.g., `feature/sales-order-api`)
- `bugfix/` - Bug fixes (e.g., `bugfix/invoice-calculation-error`)
- `hotfix/` - Critical production fixes (e.g., `hotfix/security-vulnerability`)
- `refactor/` - Code refactoring (e.g., `refactor/repository-pattern`)
- `docs/` - Documentation changes (e.g., `docs/api-documentation`)
- `test/` - Adding or updating tests (e.g., `test/sales-order-tests`)

### Workflow

```bash
# 1. Sync your fork with upstream
git checkout main
git pull upstream main
git push origin main

# 2. Create a feature branch
git checkout -b feature/your-feature-name

# 3. Make your changes
# ... edit files ...

# 4. Stage and commit your changes
git add .
git commit -m "feat: add sales order validation"

# 5. Keep your branch up to date
git fetch upstream
git rebase upstream/main

# 6. Push to your fork
git push origin feature/your-feature-name

# 7. Create a Pull Request on GitHub
```

## Coding Standards

### PHP Standards

We follow **PSR-12** coding standard.

```bash
# Check code style
./vendor/bin/php-cs-fixer fix --dry-run --diff

# Fix code style
./vendor/bin/php-cs-fixer fix

# Run static analysis
./vendor/bin/phpstan analyse
```

**Key Points:**
- Use type hints for parameters and return types
- Use strict types: `declare(strict_types=1);`
- Follow SOLID principles
- Keep methods small and focused
- Use dependency injection
- Avoid static calls (except facades)

**Example:**

```php
<?php

declare(strict_types=1);

namespace Modules\Sales\Services;

use Modules\Sales\Models\SalesOrder;
use Modules\Sales\Repositories\Contracts\SalesOrderRepositoryInterface;

final class SalesOrderService implements SalesOrderServiceInterface
{
    public function __construct(
        private readonly SalesOrderRepositoryInterface $repository
    ) {}

    public function create(array $data): SalesOrder
    {
        // Implementation
    }
}
```

### JavaScript/TypeScript Standards

We use ESLint with Prettier.

```bash
# Check code
npm run lint

# Fix code
npm run lint:fix
```

**Key Points:**
- Use ES6+ features
- Use const/let, not var
- Use arrow functions where appropriate
- Follow Vue.js 3 Composition API best practices
- Add JSDoc comments for complex functions

### CSS Standards

We use Tailwind CSS. Avoid custom CSS where possible.

**Guidelines:**
- Use Tailwind utility classes
- Use @apply for repeated patterns
- Follow mobile-first responsive design
- Keep custom CSS minimal

## Testing Guidelines

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage

# Run specific test
php artisan test --filter=SalesOrderTest
```

### Writing Tests

**Unit Tests:**
- Test individual classes in isolation
- Mock dependencies
- Focus on business logic

```php
public function test_can_calculate_order_total(): void
{
    $calculator = new OrderTotalCalculator();
    
    $total = $calculator->calculate([
        'subtotal' => 100.00,
        'tax_rate' => 0.10,
        'discount' => 5.00,
    ]);
    
    $this->assertEquals(105.00, $total);
}
```

**Feature Tests:**
- Test HTTP endpoints
- Test full workflows
- Use database transactions

```php
public function test_can_create_sales_order(): void
{
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
        ->postJson('/api/v1/sales/orders', [
            'customer_id' => 1,
            'order_date' => now(),
            'lines' => [
                ['product_id' => 1, 'quantity' => 10],
            ],
        ]);
    
    $response->assertStatus(201);
    $this->assertDatabaseHas('sales_orders', [
        'customer_id' => 1,
    ]);
}
```

### Test Coverage

- Aim for **80%+ code coverage**
- All new features must include tests
- Bug fixes should include regression tests

## Documentation

### Code Documentation

Use PHPDoc blocks for all:
- Classes
- Methods
- Complex logic

```php
/**
 * Calculate the total amount for a sales order.
 *
 * This method calculates the total by adding subtotal, taxes,
 * shipping, and subtracting any discounts.
 *
 * @param SalesOrder $order The sales order to calculate
 * @return float The calculated total amount
 * @throws InvalidOrderException If order data is invalid
 */
public function calculateTotal(SalesOrder $order): float
{
    // Implementation
}
```

### API Documentation

Use OpenAPI 3.0 annotations:

```php
/**
 * @OA\Get(
 *     path="/api/v1/sales/orders",
 *     tags={"Sales Orders"},
 *     summary="List sales orders",
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Page number",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Success"
 *     )
 * )
 */
```

### Module Documentation

Each module must have:
- `README.md` - Overview and features
- `API.md` - API endpoints
- `CHANGELOG.md` - Version history

## Commit Messages

We follow [Conventional Commits](https://www.conventionalcommits.org/).

### Format

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Types

- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation changes
- `style`: Code style changes (formatting, etc.)
- `refactor`: Code refactoring
- `test`: Adding or updating tests
- `chore`: Build process or auxiliary tool changes
- `perf`: Performance improvement
- `ci`: CI/CD changes

### Examples

```bash
feat(sales): add sales order validation

Implement validation rules for sales order creation.
Validates customer, products, and quantities.

Closes #123

fix(inventory): correct stock calculation

Fixed incorrect stock calculation when multiple
warehouses are involved.

Fixes #456
```

### Scope

Use module names as scope:
- `core`
- `sales`
- `inventory`
- `crm`
- `accounting`
- etc.

## Pull Request Process

### Before Submitting

1. ✅ Run all tests and ensure they pass
2. ✅ Run code quality checks
3. ✅ Update documentation
4. ✅ Add/update tests for your changes
5. ✅ Rebase on latest main branch
6. ✅ Write clear commit messages

### Pull Request Template

```markdown
## Description
Brief description of what this PR does.

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Motivation and Context
Why is this change required? What problem does it solve?

## How Has This Been Tested?
Describe the tests you ran.

## Screenshots (if applicable)
Add screenshots to help explain your changes.

## Checklist
- [ ] My code follows the style guidelines
- [ ] I have performed a self-review
- [ ] I have commented my code where necessary
- [ ] I have updated the documentation
- [ ] My changes generate no new warnings
- [ ] I have added tests that prove my fix/feature works
- [ ] New and existing unit tests pass locally
- [ ] Any dependent changes have been merged

## Related Issues
Closes #(issue number)
```

### Review Process

1. At least one approval required
2. All CI checks must pass
3. No merge conflicts
4. Documentation updated
5. Tests added/updated

### After Merge

1. Delete your feature branch
2. Update your local repository
3. Close related issues

## Community

### Getting Help

- **Documentation**: Check our [docs](/)
- **Discussions**: [GitHub Discussions](https://github.com/kasunvimarshana/kv-erp-crm-saas/discussions)
- **Issues**: [GitHub Issues](https://github.com/kasunvimarshana/kv-erp-crm-saas/issues)

### Recognition

Contributors are recognized in:
- CONTRIBUTORS.md file
- Release notes
- Project documentation

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

---

**Thank you for contributing to KV-ERP-CRM-SaaS!** 🎉
