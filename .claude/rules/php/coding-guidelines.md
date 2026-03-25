---
paths:
  - "*.php"
  - "composer.json"
---
# PHP Coding Guidelines

## Overview

Best practices for writing modern, type-safe PHP code following PSR-12 standards. This guide covers coding style, PHP 8+ features, type declarations, and patterns for maintainable PHP applications.

## PSR-12 Coding Style

### File Structure

```php
<?php

declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\User\ValueObject\Email;
use App\Domain\User\ValueObject\UserId;
use DateTimeImmutable;

/**
 * User entity representing a registered user.
 */
final class User
{
    // Class content
}
```

### Key Rules

| Rule | Description |
|------|-------------|
| Opening tag | Use `<?php`, no short tags |
| Strict types | Add `declare(strict_types=1);` |
| Encoding | UTF-8 without BOM |
| Line endings | Unix LF only |
| Line length | Max 120 characters (80 preferred) |
| Indentation | 4 spaces, no tabs |
| Closing tag | Omit `?>` in PHP-only files |
| Blank lines | Single blank line after namespace, between use groups |

### Naming Conventions

| Element | Convention | Example |
|---------|------------|---------|
| Classes | PascalCase | `UserRepository` |
| Interfaces | PascalCase + suffix | `UserRepositoryInterface` |
| Methods | camelCase | `findById()` |
| Variables | camelCase | `$userName` |
| Constants | UPPER_SNAKE | `MAX_RETRY_COUNT` |
| Properties | camelCase | `$createdAt` |

### Braces and Spacing

```php
<?php

declare(strict_types=1);

namespace App\Service;

class UserService
{
    public function createUser(string $name, string $email): User
    {
        if ($name === '') {
            throw new InvalidArgumentException('Name cannot be empty');
        }

        return new User($name, $email);
    }

    public function findUsers(array $criteria): array
    {
        $users = [];

        foreach ($criteria as $key => $value) {
            // Process criteria
        }

        return $users;
    }
}
```

## PHP 8+ Features

### Constructor Property Promotion

```php
<?php

declare(strict_types=1);

// PHP 8.0+: Constructor promotion
class User
{
    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly string $email,
        private readonly ?string $phone = null,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }
}
```

### Readonly Properties & Classes

```php
<?php

declare(strict_types=1);

// PHP 8.1+: Readonly properties
class UserData
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
    ) {}
}

// PHP 8.2+: Readonly class (all properties are readonly)
readonly class ValueObject
{
    public function __construct(
        public string $value,
    ) {}
}
```

### Named Arguments

```php
<?php

// Use for improved readability with many parameters
$user = new User(
    id: $id,
    name: $name,
    email: $email,
    isActive: true,
);

// Useful for optional parameters
$response = $client->request(
    method: 'POST',
    uri: '/api/users',
    headers: ['Content-Type' => 'application/json'],
);
```

### Match Expression

```php
<?php

// PHP 8.0+: Match expression (strict comparison)
$statusText = match ($status) {
    Status::PENDING => 'Pending',
    Status::ACTIVE => 'Active',
    Status::INACTIVE => 'Inactive',
    default => 'Unknown',
};

// With multiple conditions
$category = match (true) {
    $age < 13 => 'child',
    $age < 20 => 'teenager',
    $age < 65 => 'adult',
    default => 'senior',
};
```

### Null Safe Operator

```php
<?php

// PHP 8.0+: Null safe operator
$country = $user?->getAddress()?->getCountry()?->getName();

// Instead of
$country = null;
if ($user !== null) {
    $address = $user->getAddress();
    if ($address !== null) {
        $country = $address->getCountry()?->getName();
    }
}
```

### Enums

```php
<?php

declare(strict_types=1);

// PHP 8.1+: Enums
enum Status: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending Review',
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
        };
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }
}

// Usage
function setStatus(Status $status): void
{
    // Type-safe enum parameter
}

setStatus(Status::ACTIVE);
```

## Type Declarations

### Always Enable Strict Types

```php
<?php

declare(strict_types=1);

// Every PHP file should start with this
```

### Return Types

```php
<?php

declare(strict_types=1);

class UserRepository
{
    // Explicit return type
    public function find(int $id): ?User
    {
        // Returns User or null
    }

    // Array return type (use generics in PHPDoc)
    /** @return User[] */
    public function findAll(): array
    {
        return [];
    }

    // Union types (PHP 8.0+)
    public function findByIdOrEmail(int|string $identifier): ?User
    {
        // ...
    }

    // Intersection types (PHP 8.1+)
    public function process(Countable&Iterator $items): void
    {
        // ...
    }

    // Never return type (PHP 8.1+)
    public function fail(): never
    {
        throw new RuntimeException('Operation failed');
    }

    // Void for no return value
    public function save(User $user): void
    {
        // ...
    }
}
```

### Nullable Types

```php
<?php

declare(strict_types=1);

class Order
{
    public function __construct(
        private int $id,
        private ?string $note = null,  // Nullable with default
    ) {}

    // Nullable return type
    public function getNote(): ?string
    {
        return $this->note;
    }

    // Nullable parameter
    public function setNote(?string $note): void
    {
        $this->note = $note;
    }
}
```

### PHPDoc for Complex Types

```php
<?php

declare(strict_types=1);

class UserService
{
    /**
     * Find users by criteria.
     *
     * @param array<string, mixed> $criteria Search criteria
     * @return array<int, User> List of matching users
     * @throws UserNotFoundException When no users found
     */
    public function findBy(array $criteria): array
    {
        // ...
    }

    /**
     * @param callable(User): bool $predicate
     * @return User[]
     */
    public function filter(callable $predicate): array
    {
        // ...
    }
}
```

## Classes & Methods

### Final Classes by Default

```php
<?php

declare(strict_types=1);

// Prefer final classes - extend only when designed for it
final class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $repository,
        private readonly EventDispatcherInterface $dispatcher,
    ) {}
}

// Use interfaces for contracts
interface UserRepositoryInterface
{
    public function find(int $id): ?User;
    public function save(User $user): void;
}
```

### Single Responsibility

```php
<?php

declare(strict_types=1);

// Good: Single responsibility
final class UserCreator
{
    public function __construct(
        private readonly UserRepository $repository,
        private readonly PasswordHasher $hasher,
        private readonly EventDispatcher $dispatcher,
    ) {}

    public function create(CreateUserCommand $command): User
    {
        $user = new User(
            name: $command->name,
            email: $command->email,
            password: $this->hasher->hash($command->password),
        );

        $this->repository->save($user);
        $this->dispatcher->dispatch(new UserCreatedEvent($user));

        return $user;
    }
}
```

### Dependency Injection

```php
<?php

declare(strict_types=1);

// Good: Constructor injection
final class OrderProcessor
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly PaymentGatewayInterface $paymentGateway,
        private readonly LoggerInterface $logger,
    ) {}

    public function process(Order $order): void
    {
        // Dependencies injected, testable
    }
}

// Bad: Service locator
final class BadOrderProcessor
{
    public function process(Order $order): void
    {
        // Don't do this
        $repository = Container::get(OrderRepository::class);
    }
}
```

## Error Handling

### Custom Exceptions

```php
<?php

declare(strict_types=1);

namespace App\Exception;

use RuntimeException;

final class UserNotFoundException extends RuntimeException
{
    public static function withId(int $id): self
    {
        return new self(sprintf('User with ID %d not found', $id));
    }

    public static function withEmail(string $email): self
    {
        return new self(sprintf('User with email "%s" not found', $email));
    }
}

// Usage
throw UserNotFoundException::withId($id);
```

### Exception Handling

```php
<?php

declare(strict_types=1);

final class UserController
{
    public function show(int $id): Response
    {
        try {
            $user = $this->userService->find($id);
            return $this->json($user);
        } catch (UserNotFoundException $e) {
            return $this->notFound($e->getMessage());
        } catch (DatabaseException $e) {
            $this->logger->error('Database error', ['exception' => $e]);
            return $this->serverError('An error occurred');
        }
    }
}
```

### Result Pattern (Alternative to Exceptions)

```php
<?php

declare(strict_types=1);

/**
 * @template T
 */
final class Result
{
    private function __construct(
        private readonly bool $success,
        private readonly mixed $value,
        private readonly ?string $error,
    ) {}

    /**
     * @template U
     * @param U $value
     * @return Result<U>
     */
    public static function success(mixed $value): self
    {
        return new self(true, $value, null);
    }

    public static function failure(string $error): self
    {
        return new self(false, null, $error);
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getError(): ?string
    {
        return $this->error;
    }
}

// Usage
$result = $userService->createUser($data);
if ($result->isSuccess()) {
    $user = $result->getValue();
} else {
    $error = $result->getError();
}
```

## Best Practices

### Use Value Objects

```php
<?php

declare(strict_types=1);

// Instead of primitive string
final readonly class Email
{
    public function __construct(
        public string $value,
    ) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }
}

// Usage in entity
final class User
{
    public function __construct(
        private UserId $id,
        private Email $email,  // Type-safe, validated
    ) {}
}
```

### Immutability

```php
<?php

declare(strict_types=1);

final readonly class Money
{
    public function __construct(
        public int $amount,
        public string $currency,
    ) {}

    public function add(self $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException('Currency mismatch');
        }

        return new self($this->amount + $other->amount, $this->currency);
    }
}
```

### Early Returns

```php
<?php

declare(strict_types=1);

// Good: Early returns
public function process(Order $order): void
{
    if ($order->isPaid()) {
        return;
    }

    if ($order->isCancelled()) {
        throw new OrderCancelledException();
    }

    // Main logic here
    $this->processPayment($order);
}

// Bad: Deep nesting
public function processBad(Order $order): void
{
    if (!$order->isPaid()) {
        if (!$order->isCancelled()) {
            // Deeply nested logic
            $this->processPayment($order);
        } else {
            throw new OrderCancelledException();
        }
    }
}
```

## Anti-patterns

### ❌ Missing Strict Types

```php
<?php

// Bad - no strict types
class User
{
    public function setAge($age)
    {
        $this->age = $age; // Accepts any type
    }
}
```

✅ Enable strict types:
```php
<?php

declare(strict_types=1);

class User
{
    public function setAge(int $age): void
    {
        $this->age = $age;
    }
}
```

### ❌ Using Arrays for Everything

```php
<?php

// Bad - untyped array
function createUser(array $data): array
{
    return [
        'id' => generateId(),
        'name' => $data['name'],
        'email' => $data['email'],
    ];
}
```

✅ Use DTOs/Value Objects:
```php
<?php

declare(strict_types=1);

final readonly class CreateUserRequest
{
    public function __construct(
        public string $name,
        public string $email,
    ) {}
}

final readonly class UserResponse
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
    ) {}
}

function createUser(CreateUserRequest $request): UserResponse
{
    // Type-safe
}
```

### ❌ Static Methods for Business Logic

```php
<?php

// Bad - hard to test, hidden dependencies
class UserService
{
    public static function createUser(string $name): User
    {
        $db = Database::getInstance();
        // ...
    }
}
```

✅ Use dependency injection:
```php
<?php

declare(strict_types=1);

final class UserService
{
    public function __construct(
        private readonly UserRepository $repository,
    ) {}

    public function createUser(string $name): User
    {
        // Testable, explicit dependencies
    }
}
```

### ❌ Catch-All Exception Handling

```php
<?php

// Bad - swallows all errors
try {
    $this->process($data);
} catch (Exception $e) {
    // Silent failure
}
```

✅ Handle specific exceptions:
```php
<?php

try {
    $this->process($data);
} catch (ValidationException $e) {
    return $this->badRequest($e->getErrors());
} catch (NotFoundException $e) {
    return $this->notFound($e->getMessage());
}
// Let unexpected exceptions bubble up
```

### ❌ God Classes

```php
<?php

// Bad - does too much
class UserManager
{
    public function create() {}
    public function update() {}
    public function delete() {}
    public function sendEmail() {}
    public function generateReport() {}
    public function exportToCsv() {}
    // ...50 more methods
}
```

✅ Split into focused classes:
```php
<?php

final class UserCreator {}
final class UserUpdater {}
final class UserEmailSender {}
final class UserReportGenerator {}
```

## Code Quality Tools

### PHP CS Fixer

```bash
# Install
composer require --dev friendsofphp/php-cs-fixer

# Run
./vendor/bin/php-cs-fixer fix src/
```

```php
// .php-cs-fixer.php
<?php

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        'strict_param' => true,
        'declare_strict_types' => true,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()->in(['src', 'tests'])
    );
```

### PHPStan

```bash
# Install
composer require --dev phpstan/phpstan

# Run
./vendor/bin/phpstan analyse src/ --level=max
```

```yaml
# phpstan.neon
parameters:
    level: 9
    paths:
        - src
    treatPhpDocTypesAsCertain: false
```

## References

- [PSR-12: Extended Coding Style](https://www.php-fig.org/psr/psr-12/)
- [PHP Manual: Type Declarations](https://www.php.net/manual/en/language.types.declarations.php)
- [PHP 8.0 Constructor Promotion](https://php.watch/versions/8.0/constructor-property-promotion)
- [PHP 8.1 Readonly Properties](https://php.watch/versions/8.1/readonly)
- [PHP 8.2 Readonly Classes](https://php.watch/versions/8.2/readonly-classes)
- [PHP CS Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer)
- [PHPStan](https://phpstan.org/)
