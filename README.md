# ReservedSlugs plugin for CakePHP

[![CI](https://github.com/elstc/cakephp-reserved-slugs/actions/workflows/ci.yml/badge.svg)](https://github.com/elstc/cakephp-reserved-slugs/actions/workflows/ci.yml)
[![Latest Stable Version](https://img.shields.io/github/v/release/elstc/cakephp-reserved-slugs?sort=semver&style=flat-square)](https://packagist.org/packages/elstc/cakephp-reserved-slugs)
[![Total Downloads](https://img.shields.io/packagist/dt/elstc/cakephp-reserved-slugs?style=flat-square)](https://packagist.org/packages/elstc/cakephp-reserved-slugs/stats)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)

A CakePHP plugin that ensures user-chosen slugs are safe and conflict-free for use in URLs.

[日本語ドキュメント / Japanese](README.ja.md)

## Version Map

| CakePHP | PHP    | Plugin | Branch |
|---------|--------|--------|--------|
| 5.x     | >= 8.2 | 5.x    | cake5  |

- **SlugValidator** validates that a string is well-formed for use as a subdomain label or URL path segment (lowercase alphanumeric + hyphens, length constraints).
- **IsNotReservedSlug** compares slugs against a reserved-word list so they never collide with system routes or well-known paths.

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```shell
composer require elstc/cakephp-reserved-slugs
```

Load the plugin by running the console command:

```shell
bin/cake plugin load ReservedSlugs
```

Run the migration to create the `reserved_slugs` table:

```shell
bin/cake migrations migrate --plugin ReservedSlugs
```

Import the default reserved slugs:

```shell
bin/cake reserved_slugs sync
```

## Usage

### Validation Rule

Add the `IsNotReservedSlug` rule to your table's `buildRules()` method:

```php
use ReservedSlugs\Model\Rule\IsNotReservedSlug;

public function buildRules(RulesChecker $rules): RulesChecker
{
    $rules->add(new IsNotReservedSlug('slug'), 'reservedSlug', [
        'errorField' => 'slug',
        'message' => 'This slug is reserved.',
    ]);

    return $rules;
}
```

#### Custom field name

```php
$rules->add(new IsNotReservedSlug('username'), 'reservedSlug', [
    'errorField' => 'username',
    'message' => 'This username is reserved.',
]);
```

### Slug Format Validator

The `SlugValidator` class provides a static method for validating slug format (lowercase alphanumeric and hyphens):

```php
use ReservedSlugs\Validation\SlugValidator;

// Use as a Validator provider
$validator->setProvider('slugValidator', SlugValidator::class);
$validator->add('slug', 'validSlug', [
    'rule' => ['isValid'],
    'provider' => 'slugValidator',
    'message' => 'Only lowercase letters, numbers, and hyphens are allowed.',
]);
```

#### Configuration Options

##### `minLength`

Minimum slug length. Default: `4`

##### `maxLength`

Maximum slug length. Default: `24`

```php
$validator->add('slug', 'validSlug', [
    'rule' => ['isValid', 3, 32],
    'provider' => 'slugValidator',
]);
```

### CLI Commands

#### List reserved slugs

```shell
bin/cake reserved_slugs list
bin/cake reserved_slugs list --count
bin/cake reserved_slugs list --search admin
```

#### Add a reserved slug

```shell
bin/cake reserved_slugs add my-reserved-slug
```

#### Remove a reserved slug

```shell
bin/cake reserved_slugs remove my-reserved-slug
```

#### Import slugs from a file

```shell
bin/cake reserved_slugs import /path/to/slugs.txt
```

File format: one slug per line, `#` for comments, empty lines are ignored.

#### Sync with a file

```shell
# Sync (auto-detects app config file or falls back to plugin built-in)
bin/cake reserved_slugs sync

# Sync with a specific file
bin/cake reserved_slugs sync --file /path/to/slugs.txt

# Preview changes without applying
bin/cake reserved_slugs sync --dry-run
```

When `--file` is not specified, the sync command resolves the seed file in the following order:

1. Application config file: `config/reserved-slugs.txt` (in app root)
2. Plugin built-in seed file

You can override the application config file path via `Configure`:

```php
// In config/app.php or config/app_local.php
'ReservedSlugs' => [
    'syncFile' => CONFIG . 'my-custom-slugs.txt',
],
```

### Reserved Slugs List File

The plugin ships with a default reserved slugs list at `config/reserved-slugs.txt`. This file contains ~710 common reserved words (e.g. `admin`, `api`, `login`, `settings`, `www`) that could conflict with system routes or well-known paths.

#### File format

- One slug per line
- Lines starting with `#` are comments
- Empty lines are ignored

```text
# System routes
admin
api
login

# Social media
facebook
twitter
youtube
```

#### Using the built-in list

The built-in list is used as a fallback by the `sync` command when no app-level config file exists. You can also import it directly:

```shell
bin/cake reserved_slugs import vendor/elstc/cakephp-reserved-slugs/config/reserved-slugs.txt
```

#### Using a custom list

Create your own file following the same format and use it with `import` or `sync`:

```shell
# Import additional slugs from a custom file
bin/cake reserved_slugs import /path/to/my-slugs.txt

# Sync the database to match your custom file exactly
bin/cake reserved_slugs sync --file /path/to/my-slugs.txt
```

> **Note:** `import` adds slugs from the file to the database (existing slugs are preserved). `sync` makes the database match the file exactly — slugs not in the file will be removed.
