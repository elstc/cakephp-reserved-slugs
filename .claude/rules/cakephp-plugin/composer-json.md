# CakePHP Plugin composer.json 設定

## 基本構造

```json
{
    "name": "cakephp/{plugin-name}",
    "description": "Plugin description for CakePHP",
    "license": "MIT",
    "type": "cakephp-plugin",
    "keywords": [
        "cakephp",
        "plugin-specific-keyword"
    ],
    "authors": [
        {
            "name": "CakePHP Community",
            "homepage": "https://github.com/cakephp/{plugin-name}/graphs/contributors"
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

## 重要なフィールド

### type

`cakephp-plugin` を必ず指定する。これにより、CakePHPがプラグインを正しく認識する。

### autoload / autoload-dev

- `autoload`: 本番コード用（src/）
- `autoload-dev`: テストコード用
  - `PluginNamespace\Test\`: tests/
  - `Cake\Test\`: CakePHPのテストユーティリティ
  - `TestApp\`: テストアプリケーション用

### scripts

推奨されるComposerスクリプト：

| スクリプト | 用途 |
|-----------|------|
| `check` | cs-check と test を実行 |
| `cs-check` | コーディング規約チェック |
| `cs-fix` | コーディング規約の自動修正 |
| `phpstan` | 静的解析実行 |
| `stan-setup` | PHPStan を phive でインストール |
| `test` | PHPUnitテスト実行 |
| `test-coverage` | カバレッジ付きテスト実行 |
| `update-lowest` | 最小依存バージョンでの互換性確認用に依存を更新 |

### require-dev

CakePHPプラグイン開発で必須の開発依存関係：

- `cakephp/cakephp-codesniffer`: CakePHPコーディング規約
- `phpunit/phpunit`: テストフレームワーク

## PHPバージョン対応

CakePHP 5.x系は PHP 8.1 以上を必要とする：

```json
{
    "require": {
        "php": ">=8.1"
    }
}
```

## 依存関係の最小化

- フレームワーク全体ではなく、必要なコンポーネントのみを依存として指定する
- 例: `cakephp/http`, `cakephp/orm` など個別パッケージ
- PSR標準パッケージ（`psr/http-message` など）を活用する