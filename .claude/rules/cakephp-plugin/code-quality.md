---
paths:
  - "phpcs.xml"
  - "phpstan.neon"
---
# CakePHP Plugin Code Quality Management

## Coding Standards (PHPCS)

### phpcs.xml

```xml
<?xml version="1.0"?>
<ruleset name="CakePHP Plugin">
    <rule ref="CakePHP"/>

    <file>src/</file>
    <file>tests/</file>
</ruleset>
```

### Commands

```bash
# Check
composer cs-check
# or
vendor/bin/phpcs --colors -p src/ tests/

# Auto-fix
composer cs-fix
# or
vendor/bin/phpcbf --colors -p src/ tests/
```

## Static Analysis (PHPStan)

### Tool Installation (phive)

`.phive/phars.xml`:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phive xmlns="https://phar.io/phive">
  <phar name="phpstan" version="^2" installed="2.1.33" location="./tools/phpstan" copy="false"/>
</phive>
```

Installation:

```bash
composer stan-setup
# or
phive install
```

### Keep PHPStan Up to Date

Always update PHPStan to the latest version when setting up or modifying a project:

```bash
phive update phpstan
```

When the installed version in `.phive/phars.xml` is outdated, update it by running the command above. This ensures you get the latest analysis rules and bug fixes.

### phpstan.neon

```neon
includes:
    - phpstan-baseline.neon

parameters:
    level: 8
    paths:
        - src/
    ignoreErrors:
        - identifier: missingType.generics
        - identifier: missingType.iterableValue
```

### Recommended Level

| Level | Description |
|-------|-------------|
| 8 | Maximum level (CakePHP official plugin standard) |

### Baseline Management

Prevent new errors while gradually fixing existing ones:

```bash
# Generate baseline
composer stan-baseline
# or
tools/phpstan --generate-baseline
```

### Commands

```bash
composer phpstan
# or
tools/phpstan analyse
```

## Dependencies

### cakephp-codesniffer

CakePHP-specific coding standards:

```json
{
    "require-dev": {
        "cakephp/cakephp-codesniffer": "^5.0"
    }
}
```

### allow-plugins Configuration

```json
{
    "config": {
        "allow-plugins": {
            "dealerdirect/phpcodesniffer-composer-installer": true
        }
    }
}
```

## PHP Declarations

### Strict Type Declaration

Add at the top of every PHP file:

```php
<?php
declare(strict_types=1);
```

### License Header

```php
<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */
```

## CI Quality Checks

The cs-stan workflow executes the following:

1. Static analysis with PHPStan
2. Coding standards check with PHPCS

```yaml
cs-stan:
  uses: cakephp/.github/.github/workflows/cs-stan.yml@5.x
  secrets: inherit
```

## Quality Check Execution Order

1. Coding standards check (fast)
2. Static analysis (medium)
3. Unit tests (slow)

```json
{
    "scripts": {
        "check": [
            "@cs-check",
            "@phpstan",
            "@test"
        ]
    }
}
```