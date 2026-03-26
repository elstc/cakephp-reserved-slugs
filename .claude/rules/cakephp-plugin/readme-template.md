---
paths:
  - "README.md"
---
# CakePHP Plugin README Template

## Structure

Create README.md with the following sections:

1. Title and badges
2. Description
3. Version map (when supporting multiple CakePHP versions)
4. Links to related plugins (if applicable)
5. Installation steps
6. Usage and configuration options
7. Documentation links
8. Additional info (IDE support, etc. — optional)

## Template (Official Style)

````markdown
# CakePHP {PluginName}

[![CI](https://github.com/{org}/{repo}/actions/workflows/ci.yml/badge.svg)](https://github.com/{org}/{repo}/actions/workflows/ci.yml)
[![Latest Stable Version](https://img.shields.io/github/v/release/{org}/{repo}?sort=semver&style=flat-square)](https://packagist.org/packages/{org}/{repo})
[![Total Downloads](https://img.shields.io/packagist/dt/{org}/{repo}?style=flat-square)](https://packagist.org/packages/{org}/{repo}/stats)
[![Coverage Status](https://img.shields.io/codecov/c/github/{org}/{repo}.svg?style=flat-square)](https://codecov.io/github/{org}/{repo})
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

{1-2 sentence description of the plugin}

## Installation

You can install this plugin into your CakePHP application using
[composer](https://getcomposer.org):

```
composer require {org}/{repo}
```

Then load the plugin:
```
bin/cake plugin load {PluginName}
```

## Documentation

Documentation for this plugin can be found in the [CakePHP Cookbook](https://book.cakephp.org/{plugin-name}/3/en/).
````

## Template (Third-Party Style)

Follow these principles so that AI agents can read the README and complete initial setup:

- Number each step to clarify the order
- Include file path comments in code blocks
- Annotate conditional steps (e.g., skip if no DB is required)
- Use `shell` blocks for commands and `php` blocks for PHP code

````markdown
# {PluginName} plugin for CakePHP

[![CI](https://github.com/{org}/{repo}/actions/workflows/ci.yml/badge.svg)](https://github.com/{org}/{repo}/actions/workflows/ci.yml)
[![Latest Stable Version](https://img.shields.io/github/v/release/{org}/{repo}?sort=semver&style=flat-square)](https://packagist.org/packages/{org}/{repo})
[![Total Downloads](https://img.shields.io/packagist/dt/{org}/{repo}?style=flat-square)](https://packagist.org/packages/{org}/{repo}/stats)
[![Coverage Status](https://img.shields.io/codecov/c/github/{org}/{repo}.svg?style=flat-square)](https://codecov.io/github/{org}/{repo})
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

{1-2 sentence description of the plugin}

## Requirements

- PHP {version}+
- CakePHP {version}+
- {List required PHP extensions if any (e.g., ext-intl)}

## Version Map

| CakePHP | Plugin | Branch |
|---------|--------|--------|
| 5.x     | 3.x    | cake5  |
| 4.x     | 2.x    | cake4  |
| 3.x     | 1.x    | cake3  |

## Installation

### Step 1: Install via Composer

```shell
composer require {org}/{repo}
```

### Step 2: Load the Plugin

Run the following command:

```shell
bin/cake plugin load {PluginName}
```

Or add the following to `src/Application.php`:

```php
// src/Application.php
public function bootstrap(): void
{
    parent::bootstrap();
    $this->addPlugin('{PluginName}');
}
```

### Step 3: Run Migrations (if applicable)

> Skip this step if the plugin does not require database tables.

```shell
bin/cake migrations migrate --plugin {PluginName}
```

### Step 4: Configuration (if applicable)

> Skip this step if the plugin works without additional configuration.

Add the following to your application config:

```php
// config/app.php
'{PluginName}' => [
    'optionName' => 'value',
],
```

{Add Middleware or Component loading instructions here if needed}

## Quick Start

{Provide a minimal working code example}

```php
// src/Controller/ExampleController.php
// Minimal usage example
```

## Configuration Options

### `optionName`

{Description of the option}

default: `'default_value'`

```php
// config/app_local.php
'{PluginName}' => [
    'optionName' => 'custom_value',
],
```
````

## Japanese README

Provide Japanese documentation as a separate `README.ja.md` file.
Badge sections can be identical to the English version; translate only the description text.

## Badge Types

### Required Badges

| Badge | Purpose |
|-------|---------|
| CI | GitHub Actions build status |
| Latest Stable Version | Latest release version |
| Software License | License display |

### Recommended Badges

| Badge | Purpose |
|-------|---------|
| Total Downloads | Packagist download count |
| Coverage Status | Code coverage |

## Badge URL Formats

### GitHub Actions CI

```markdown
[![CI](https://github.com/{org}/{repo}/actions/workflows/ci.yml/badge.svg)](https://github.com/{org}/{repo}/actions/workflows/ci.yml)
```

### Packagist Version

```markdown
[![Latest Stable Version](https://img.shields.io/github/v/release/{org}/{repo}?sort=semver&style=flat-square)](https://packagist.org/packages/{org}/{repo})
```

### Packagist Downloads

```markdown
[![Total Downloads](https://img.shields.io/packagist/dt/{org}/{repo}?style=flat-square)](https://packagist.org/packages/{org}/{repo}/stats)
```

### Codecov Coverage

```markdown
[![Coverage Status](https://img.shields.io/codecov/c/github/{org}/{repo}.svg?style=flat-square)](https://codecov.io/github/{org}/{repo})
```

### License

```markdown
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
```

## Notes

- For development-only tools (e.g., debug_kit), include a :warning: emoji warning about production use
- Add cross-links between related plugins (e.g., authentication <-> authorization)
- Link to the CakePHP Cookbook if documentation is available there