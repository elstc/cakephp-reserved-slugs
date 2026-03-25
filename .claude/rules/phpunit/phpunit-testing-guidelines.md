---
paths:
  - tests/
---
# PHPUnit Testing Guidelines

## Overview

Guidelines for writing effective and maintainable PHPUnit tests. These practices ensure test reliability, readability, and proper isolation.

## Test Structure

Follow the AAA (Arrange-Act-Assert) pattern for clear test organization:

```php
#[Test]
public function calculateTotal_withItems_returnsCorrectSum(): void
{
    // Arrange
    $cart = new Cart();
    $cart->addItem(new Item(price: 100));
    $cart->addItem(new Item(price: 200));

    // Act
    $total = $cart->calculateTotal();

    // Assert
    $this->assertSame(300, $total);
}
```

### Naming Conventions

Use descriptive test method names following the pattern: `methodName_stateToBeTested_expectedOutcome`

```php
// Good
public function findById_withNonExistentId_returnsNull(): void
public function save_withValidData_persistsEntity(): void

// Also acceptable: Japanese descriptive names with #[Test] attribute
#[Test]
public function 存在しないIDで検索するとnullを返すこと(): void
```

## PHPUnit Attributes

Use PHP 8 attributes instead of annotations (PHPUnit 10+):

### Essential Attributes

| Attribute | Purpose |
|-----------|---------|
| `#[Test]` | Mark method as test (alternative to `test` prefix) |
| `#[CoversClass(ClassName::class)]` | Specify class under test for coverage |
| `#[UsesClass(ClassName::class)]` | Declare used but not covered classes |
| `#[DataProvider('methodName')]` | Specify data provider method |
| `#[Depends('testMethodName')]` | Declare test dependencies |

### Test Size Attributes

| Attribute | Use Case | Time Limit |
|-----------|----------|------------|
| `#[Small]` | Unit tests (isolated components) | < 100ms |
| `#[Medium]` | Integration tests (multiple components) | < 1s |
| `#[Large]` | E2E tests, browser tests | > 1s |

### Example

```php
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(Calculator::class)]
#[Small]
class CalculatorTest extends TestCase
{
    #[Test]
    #[DataProvider('additionProvider')]
    public function add_withValidNumbers_returnsSum(int $a, int $b, int $expected): void
    {
        $calculator = new Calculator();
        $this->assertSame($expected, $calculator->add($a, $b));
    }

    public static function additionProvider(): array
    {
        return [
            'positive numbers' => [1, 2, 3],
            'negative numbers' => [-1, -2, -3],
            'mixed numbers' => [-1, 2, 1],
            'zeros' => [0, 0, 0],
        ];
    }
}
```

## Assertions

### Use assertSame() Over assertEquals()

Prefer `assertSame()` for strict type comparison (uses `===`):

```php
// Good - strict comparison
$this->assertSame(42, $result);
$this->assertSame('expected', $actual);

// Avoid - loose comparison (uses ==)
$this->assertEquals(42, $result);
```

### When to Use assertEquals()

- **Object comparison**: When comparing object attribute values, not identity
- **Float comparison**: Use `assertEqualsWithDelta()` instead

```php
// Object comparison (attribute values)
$this->assertEquals($expectedUser, $actualUser);

// Float with delta
$this->assertEqualsWithDelta(3.14, $pi, 0.01);
```

### Recommended Assertions

| Assertion | Use Case |
|-----------|----------|
| `assertSame($expected, $actual)` | Strict equality (type + value) |
| `assertTrue($condition)` | Boolean true check |
| `assertFalse($condition)` | Boolean false check |
| `assertNull($value)` | Null check |
| `assertInstanceOf(Class::class, $obj)` | Type check |
| `assertCount($expected, $array)` | Array length |
| `assertArrayHasKey($key, $array)` | Key existence |
| `assertStringContainsString($needle, $haystack)` | Substring check |

## Data Providers

### When to Use DataProvider

DataProvider is recommended for reducing code duplication and improving test coverage. Use the following criteria to decide:

**Use DataProvider when:**

- Testing the same logic with multiple input/output combinations (e.g., validation, calculation, formatting)
- Boundary value testing (min, max, zero, negative, empty, null)
- Testing error cases across multiple invalid inputs
- The test logic (Act + Assert) is identical across all cases — only the data differs

**Do NOT use DataProvider when:**

- Each case requires different setup (Arrange) or different assertions
- The test has only 1-2 cases with no realistic expectation of more
- Complex object graphs are needed as test data — readability suffers
- The test verifies side effects or mock interactions that vary per case
- Adding a DataProvider would obscure the intent of individual test scenarios

**Rule of thumb:** If you find yourself copy-pasting a test method and only changing the input values, use a DataProvider. If each test case tells a different story, keep them as separate test methods.

### Static Method (Recommended)

```php
#[DataProvider('emailProvider')]
public function testValidateEmail(string $email, bool $expected): void
{
    $this->assertSame($expected, $this->validator->isValid($email));
}

public static function emailProvider(): array
{
    return [
        'valid email' => ['user@example.com', true],
        'missing @' => ['userexample.com', false],
        'missing domain' => ['user@', false],
        'empty string' => ['', false],
    ];
}
```

### Inline Data (TestWith)

For simple cases, use `#[TestWith]` attribute:

```php
#[TestWith([1, 1, 2])]
#[TestWith([0, 0, 0])]
#[TestWith([-1, 1, 0])]
public function testAdd(int $a, int $b, int $expected): void
{
    $this->assertSame($expected, $this->calc->add($a, $b));
}
```

## Mocking

### Basic Mock Creation

```php
// Create mock with specific methods
$mock = $this->createMock(UserRepository::class);
$mock->expects($this->once())
    ->method('find')
    ->with(1)
    ->willReturn(new User(id: 1, name: 'Test'));

// Create stub (no expectations)
$stub = $this->createStub(Logger::class);
$stub->method('log')->willReturn(true);
```

### Framework-Specific Mocks

For CakePHP:

```php
// Use getMockForModel for Table classes
$usersTable = $this->getMockForModel('Users', ['sendNotification']);
$usersTable->expects($this->once())
    ->method('sendNotification')
    ->willReturn(true);
```

## Best Practices

### One Assertion Per Test (When Practical)

- Each test should verify one behavior
- Makes failure diagnosis easier
- Exception: Related assertions that verify a single concept

### Test Isolation

- Tests must not depend on execution order
- Clean up shared state in `setUp()` and `tearDown()`
- Use transactions for database tests (rollback after each test)

### Avoid Testing Implementation Details

```php
// Bad - testing private implementation
$this->assertSame(['a', 'b'], $obj->getInternalArray());

// Good - testing public behavior
$this->assertSame(2, $obj->count());
$this->assertTrue($obj->contains('a'));
```

### Keep Tests Fast

- Mock external services (APIs, file system, databases)
- Use in-memory databases when possible
- Avoid unnecessary setup

## Anti-patterns

### Testing Private Methods Directly

```php
// Bad
$reflection = new ReflectionMethod($obj, 'privateMethod');
$reflection->setAccessible(true);
$result = $reflection->invoke($obj);
```

```php
// Good - test through public interface
$result = $obj->publicMethod();
$this->assertSame($expected, $result);
```

### Multiple Unrelated Assertions

```php
// Bad
public function testUser(): void
{
    $user = new User('John', 'john@example.com');
    $this->assertSame('John', $user->getName());
    $this->assertSame('john@example.com', $user->getEmail());
    $this->assertTrue($user->isActive());
    $this->assertNull($user->getLastLogin());
}
```

```php
// Good - separate concerns
public function testGetName_returnsName(): void
{
    $user = new User('John', 'john@example.com');
    $this->assertSame('John', $user->getName());
}

public function testNewUser_isActiveByDefault(): void
{
    $user = new User('John', 'john@example.com');
    $this->assertTrue($user->isActive());
}
```

### Hardcoded Test Data Without Context

```php
// Bad
$this->assertSame(42, $result);

// Good - use named data providers or constants
$this->assertSame(self::EXPECTED_TAX_RATE, $result);
```

## References

- [PHPUnit Manual](https://docs.phpunit.de/en/11.5/)
- [PHPUnit Attributes Reference](https://docs.phpunit.de/en/11.5/attributes.html)
- [PHPUnit Assertions Reference](https://docs.phpunit.de/en/10.5/assertions.html)
- [PHPUnit Best Practices Guide](https://gnugat.github.io/2025/07/31/phpunit-best-practices.html)
