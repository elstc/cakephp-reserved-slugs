---
paths:
  - tests/
---

# bypass-finals Usage Guidelines

This project uses `dg/bypass-finals` to enable mocking of `final` classes. This library is powerful but can override design intent, so restrict its usage to specific cases.

## Acceptable Use Cases

1. **Third-party library `final` classes**
   - When no interface is provided
   - When the library does not intend for mocking in its design

## Cases to Avoid

1. **`final readonly` classes in the project's domain layer**
   - Define interfaces and create test doubles instead
   - Use functional (integration) tests to verify with actual classes

2. **Mocking `final` classes without understanding the intent**
   - `final` is an intentional design decision to prohibit inheritance
   - If mocking is needed, consider revising the design

## Recommended Approach

When you need to mock a `final` class, consider the following options in order:

1. **Dependency injection via interfaces**
   - If the `final` class implements an interface, depend on the interface
   - Use interface mocks in tests

2. **Verification through functional tests**
   - Skip unit test mocking and use actual classes in functional tests
   - Only mock external dependencies (DB, APIs, etc.)

3. **bypass-finals as a last resort**
   - Use bypass-finals only when the above approaches are impractical
   - Document the reason in the test code with a comment

```php
/**
 * @covers ::someMethod
 * @note bypass-finals used: CakePHP Entity class is final
 */
public function testSomeMethod(): void
{
    // Test code
}
```
