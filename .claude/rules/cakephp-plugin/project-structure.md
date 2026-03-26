# CakePHP Plugin Project Structure

## Directory Structure

```
my-plugin/
├── .editorconfig              # Editor settings
├── .gitattributes             # Git attributes (export-ignore, etc.)
├── .gitignore                 # Git ignore settings
├── .github/
│   └── workflows/
│       ├── ci.yml             # CI configuration (required)
│       └── stale.yml          # Stale issue management (optional)
├── .phive/
│   └── phars.xml              # PHAR tool management (PHPStan, etc.)
├── composer.json              # Composer configuration (required)
├── LICENSE.txt                # License file (MIT recommended)
├── phpcs.xml                  # PHPCS rule configuration
├── phpstan.neon               # PHPStan configuration
├── phpstan-baseline.neon      # PHPStan baseline
├── phpunit.xml.dist           # PHPUnit configuration
├── README.md                  # Project description
├── src/                       # Source code
│   ├── Plugin.php             # Plugin class (if needed)
│   └── ...
├── tests/                     # Test code
│   ├── bootstrap.php          # Test bootstrap
│   ├── TestCase/              # Test cases
│   ├── test_app/              # Test application
│   └── data/                  # Test data (optional)
├── config/                    # Configuration files (optional)
├── templates/                 # Template files (optional)
└── Docs/                      # Documentation (optional)
```

## Required Files

### .editorconfig

```ini
; This file is for unifying the coding style for different editors and IDEs.
; More information at https://editorconfig.org

root = true

[*]
indent_style = space
indent_size = 4
end_of_line = lf
insert_final_newline = true
trim_trailing_whitespace = true

[*.bat]
end_of_line = crlf

[*.yml]
indent_size = 2

[phars.xml]
indent_size = 2

[*.neon]
indent_style = tab
```

### .gitattributes

```gitattributes
# Define the line ending behavior of the different file extensions
# Set default behaviour, in case users don't have core.autocrlf set.
* text=auto
* text eol=lf

# Remove files for archives generated using `git archive`
.editorconfig export-ignore
.gitattributes export-ignore
.gitignore export-ignore
CONTRIBUTING.md export-ignore
phpunit.xml export-ignore
phpcs.xml export-ignore
/.github export-ignore
/tests export-ignore
phpstan.neon export-ignore
phpstan-baseline.neon export-ignore
.phive export-ignore
/docs export-ignore
```

### .gitignore

```gitignore
# User specific & automatically generated files #
/composer.lock
/phpunit.xml
/tmp
/vendor
/tools
.phpunit.result.cache
.phpunit.cache

# OS generated files #
.DS_Store
.DS_Store?
._*
.Spotlight-V100
.Trashes
Icon?
ehthumbs.db
Thumbs.db

# Tool specific files #
# vim
*~
*.swp
*.swo
# sublime text & textmate
*.sublime-*
*.stTheme.cache
*.tmlanguage.cache
*.tmPreferences.cache
# Eclipse
.settings/*
# JetBrains, aka PHPStorm, IntelliJ IDEA
.idea/*
# NetBeans
nbproject/*
# Visual Studio Code
.vscode
```

## Namespace Conventions

- Plugin namespace: `{VendorName}\{PluginName}\`
- Examples: `Authentication\`, `Authorization\`, `DebugKit\`
- Follow PSR-4 autoloading