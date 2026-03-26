---
paths:
  - tests/
---
# AAA (Arrange-Act-Assert) Test Pattern - PHPUnit

## Overview

The AAA pattern divides test code into three distinct sections. It enhances readability and clarifies test intent, so adopt this pattern for all tests.

## Three Phases

### 1. Arrange

Set up the objects, data, and state needed for the test.

- Instantiate the system under test
- Configure mocks and stubs
- Prepare test data
- Set preconditions

### 2. Act

Execute exactly one operation on the system under test.

- Call the target method
- Capture the return value
- Trigger side effects

### 3. Assert

Verify the expected results.

- Verify return values
- Verify state changes
- Verify exceptions
- Verify side effects

## Code Examples

### Basic Test

```php
<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class OrderServiceTest extends TestCase
{
    #[Test]
    public function calculateTotal_withMultipleItems_returnsCorrectSum(): void
    {
        // Arrange
        // -----------------------------------------------
        // Prepare order object and items
        $order = new Order();
        $order->addItem(new OrderItem('Product A', 1000, 2));
        $order->addItem(new OrderItem('Product B', 500, 3));

        // Act
        // -----------------------------------------------
        // Calculate the total
        $total = $order->calculateTotal();

        // Assert
        // -----------------------------------------------
        // Verify the total is correct
        $this->assertSame(3500, $total);
    }
}
```

### Exception Test

```php
#[Test]
public function placeOrder_withInsufficientStock_throwsException(): void
{
    // Arrange
    // -----------------------------------------------
    // Prepare a mock that fails stock check
    $stockService = $this->createMock(StockService::class);
    $stockService->method('checkStock')->willReturn(false);
    $orderService = new OrderService($stockService);
    $order = new Order();
    $order->addItem(new OrderItem('Product A', 1000, 100));

    // Assert
    // -----------------------------------------------
    // Declare exception expectation before Act
    $this->expectException(InsufficientStockException::class);
    $this->expectExceptionMessage('Insufficient stock');

    // Act
    // -----------------------------------------------
    // Place the order
    $orderService->placeOrder($order);
}
```

### Verifying Side Effects with Mocks

```php
#[Test]
public function placeOrder_onCompletion_sendsEmailNotification(): void
{
    // Arrange
    // -----------------------------------------------
    // Prepare email mock and order
    $mailer = $this->createMock(MailerInterface::class);
    $mailer->expects($this->once())
        ->method('send')
        ->with($this->callback(function ($mail) {
            return $mail->getSubject() === 'Order Confirmation';
        }));
    $orderService = new OrderService(mailer: $mailer);
    $order = new Order(customerEmail: 'test@example.com');

    // Act
    // -----------------------------------------------
    // Place the order
    $orderService->placeOrder($order);

    // Assert
    // -----------------------------------------------
    // Verified by mock expects above
}
```

## Best Practices

### Use Comments

Write a visually separated comment block at the beginning of each section.

```php
// Arrange
// -----------------------------------------------
// Prepare test data
$user = new User('test@example.com');

// Act
// -----------------------------------------------
// Validate the email address
$result = $user->isValidEmail();

// Assert
// -----------------------------------------------
// Verify the email is valid
$this->assertTrue($result);
```

### Separate with Blank Lines

Separate each section with blank lines for visual clarity.

### Limit Act to One Operation

Test only one behavior per test.

```php
// Bad: Multiple operations
// Act
$order->addItem($item);
$order->applyDiscount(10);
$total = $order->calculateTotal();

// Good: Single operation
// Arrange
// -----------------------------------------------
// Set up order with item and discount as preconditions
$order->addItem($item);
$order->applyDiscount(10);

// Act
// -----------------------------------------------
// Calculate the total
$total = $order->calculateTotal();
```

### Extract Arrange into Helper Methods

When Arrange becomes long, extract setup into helper methods.

```php
#[Test]
public function calculateTotal_withValidOrder_returnsCorrectSum(): void
{
    // Arrange
    // -----------------------------------------------
    // Prepare order with multiple items
    $order = $this->createOrderWithItems([
        ['name' => 'Product A', 'price' => 1000, 'quantity' => 2],
        ['name' => 'Product B', 'price' => 500, 'quantity' => 3],
    ]);

    // Act
    // -----------------------------------------------
    // Calculate the total
    $total = $order->calculateTotal();

    // Assert
    // -----------------------------------------------
    // Verify the total is correct
    $this->assertSame(3500, $total);
}

private function createOrderWithItems(array $items): Order
{
    $order = new Order();
    foreach ($items as $item) {
        $order->addItem(new OrderItem(
            $item['name'],
            $item['price'],
            $item['quantity']
        ));
    }
    return $order;
}
```

## Anti-patterns

### Scattered Assertions

```php
// Bad: Assertions scattered before and after Act
$this->assertSame(0, $cart->getItemCount());  // Assert
$cart->addItem($item);                         // Act
$this->assertSame(1, $cart->getItemCount());  // Assert
$cart->addItem($item);                         // Act
$this->assertSame(2, $cart->getItemCount());  // Assert
```

### Mixed Arrange and Act

```php
// Bad: Setup and execution mixed together
$user = new User();
$user->setName('Test');            // This is Arrange
$result1 = $user->validate();      // Is this Act?
$user->setEmail('test@example.com');  // This is Arrange
$result2 = $user->save();          // Is this also Act?
```

### Missing Assert

```php
// Bad: No verification
#[Test]
public function testSomething(): void
{
    $service = new SomeService();
    $service->doSomething();
    // No assertion!
}
```

## Exception Test Patterns

In PHPUnit, `expectException` must be declared before the Act phase.

```php
// Arrange
// -----------------------------------------------
// Prepare the payment service
$service = new PaymentService();

// Assert
// -----------------------------------------------
// Declare exception expectation
$this->expectException(InvalidArgumentException::class);
$this->expectExceptionMessage('Amount must be a positive number');

// Act
// -----------------------------------------------
// Execute with invalid amount
$service->process(-100);
```

## Assertion Usage Guide

Prefer `assertSame` over `assertEquals` (strict type comparison).

```php
// Recommended: Strict comparison including type
$this->assertSame(3500, $total);
$this->assertSame('expected', $actual);

// Not recommended: Loose type comparison
$this->assertEquals(3500, $total);
```

## References

- [Arrange-Act-Assert pattern - Microsoft](https://docs.microsoft.com/en-us/visualstudio/test/unit-test-basics)
- [PHPUnit Documentation](https://docs.phpunit.de/)
