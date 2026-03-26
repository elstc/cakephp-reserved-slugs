---
paths:
  - "src/"
  - "tests/"
  - "config/"
  - "templates/"
---
# CakePHP Plugin Coding Practices

## Comments

### Document decisions and behavior, not implementation

Comment on:
- Design decisions and their rationale (why this approach was chosen)
- Non-obvious behavior or side effects

Do not comment on:
- What the code literally does (the code should be self-explanatory)
- Trivial getters/setters

### Write all comments and documentation in English

- Inline comments (`//`, `/* */`)
- PHPDoc blocks (`/** */`)
- README.md (Japanese version as separate README.ja.md)

## Table Access

### Use `$this->fetchTable()` instead of `TableRegistry::getTableLocator()->get()` (CakePHP 4.3+)

```php
// Bad
use Cake\ORM\TableRegistry;
$users = TableRegistry::getTableLocator()->get('Users');

// Good
$users = $this->fetchTable('Users');
```

- Available in classes using `LocatorAwareTrait` (Controller, Command, TestCase, etc.)
- Calling without arguments returns the default table based on naming conventions

## Internationalization

### Always use `__d()` for user-facing strings

All output strings (flash messages, validation errors, labels, etc.) must use `__d()` with the plugin name as the domain:

```php
// Bad
$this->Flash->error('The record could not be saved.');
throw new \RuntimeException('Invalid configuration.');

// Good
$this->Flash->error(__d('my_plugin', 'The record could not be saved.'));
throw new \RuntimeException(__d('my_plugin', 'Invalid configuration.'));
```

- First argument: plugin name in snake_case (e.g., `'my_plugin'` for `MyPlugin`)
- Do not use `__()` — it uses the default domain and may conflict with the host application's translations
- Applies to: flash messages, validation messages, exception messages shown to users, form labels, and any other user-visible text