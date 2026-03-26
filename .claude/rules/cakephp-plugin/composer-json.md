---
paths:
  - "composer.json"
---
# CakePHP Plugin composer.json Configuration

## Basic Structure

```json
{
    "name": "{vendor-name}/{plugin-name}",
    "description": "Plugin description for CakePHP",
    "license": "MIT",
    "type": "cakephp-plugin",
    "keywords": [
        "cakephp",
        "plugin-specific-keyword"
    ],
    "authors": [
        {
            "name": "{author name}",
            "homepage": "https://github.com/{vendor-name}/{plugin-name}/graphs/contributors"
        }
    ],
    "homepage": "https://cakephp.org",
    "support": {
        "issues": "https://github.com/cakephp/{plugin-name}/issues",
        "forum": "https://discourse.cakephp.org/",
        "source": "https://github.com/cakephp/{plugin-name}",
        "docs": "https://book.cakephp.org/{plugin-name}/3/en/"
    },
    "require": {
        "php": ">=8.2",
        "cakephp/cakephp": "^5.0"
    },
    "require-dev": {
        "cakephp/cakephp-codesniffer": "^5.0",
        "phpunit/phpunit": "^10.5 || ^11.5 || ^12.0"
    },
    "suggest": {
        "ext-optional": "Optional extension description"
    },
    "autoload": {
        "psr-4": {
            "PluginNamespace\\": "src/"
        }
    },
    "autoload-dev": {
        "psr-4": {
            "PluginNamespace\\Test\\": "tests/",
            "Cake\\Test\\": "vendor/cakephp/cakephp/tests/",
            "TestApp\\": "tests/test_app/TestApp/",
            "TestPlugin\\": "tests/test_app/Plugin/TestPlugin/src/"
        }
    },
    "config": {
        "allow-plugins": {
            "dealerdirect/phpcodesniffer-composer-installer": true
        },
        "sort-packages": true
    },
    "scripts": {
        "check": [
            "@cs-check",
            "@test"
        ],
        "cs-check": "phpcs --colors -p src/ tests/",
        "cs-fix": "phpcbf --colors -p src/ tests/",
        "phpstan": "tools/phpstan analyse",
        "stan": "@phpstan",
        "stan-baseline": "tools/phpstan --generate-baseline",
        "stan-setup": "phive install",
        "test": "phpunit",
        "test-coverage": "phpunit --coverage-clover=clover.xml",
        "update-lowest": "composer update --prefer-lowest --prefer-stable"
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}
```

## Key Fields

### type

Must be set to `cakephp-plugin`. This allows CakePHP to correctly recognize the plugin.

### autoload / autoload-dev

- `autoload`: Production code (src/)
- `autoload-dev`: Test code
  - `PluginNamespace\Test\`: tests/
  - `Cake\Test\`: CakePHP test utilities
  - `TestApp\`: Test application

### scripts

Recommended Composer scripts:

| Script | Purpose |
|--------|---------|
| `check` | Run cs-check and test |
| `cs-check` | Coding standards check |
| `cs-fix` | Auto-fix coding standards |
| `phpstan` | Run static analysis |
| `stan-setup` | Install PHPStan via phive |
| `test` | Run PHPUnit tests |
| `test-coverage` | Run tests with coverage |
| `update-lowest` | Update dependencies to lowest versions for compatibility check |

### require-dev

Required development dependencies for CakePHP plugin development:

- `cakephp/cakephp-codesniffer`: CakePHP coding standards
- `phpunit/phpunit`: Testing framework

## PHP Version Support

CakePHP 5.x requires PHP 8.1 or higher:

```json
{
    "require": {
        "php": ">=8.1"
    }
}
```

## Minimize Dependencies

- Specify only the required components, not the entire framework
- Example: `cakephp/http`, `cakephp/orm` as individual packages
- Leverage PSR standard packages (`psr/http-message`, etc.)