# CakePHP Plugin コード品質管理ガイドライン

## コーディング規約（PHPCS）

### phpcs.xml

```xml
<?xml version="1.0"?>
<ruleset name="CakePHP Core">
    <rule ref="CakePHP"/>

    <file>src/</file>
    <file>tests/</file>
</ruleset>
```

### 実行コマンド

```bash
# チェック
composer cs-check
# または
vendor/bin/phpcs --colors -p src/ tests/

# 自動修正
composer cs-fix
# または
vendor/bin/phpcbf --colors -p src/ tests/
```

## 静的解析（PHPStan）

### ツールインストール（phive）

`.phive/phars.xml`:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phive xmlns="https://phar.io/phive">
  <phar name="phpstan" version="2.1.x" installed="2.1.33" location="./tools/phpstan" copy="false"/>
</phive>
```

インストール:

```bash
composer stan-setup
# または
phive install
```

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

### 推奨レベル

| レベル | 説明 |
|--------|------|
| 8 | 最高レベル（CakePHP公式プラグイン標準） |

### ベースライン管理

新規エラーを防ぎながら既存エラーを段階的に修正：

```bash
# ベースライン生成
composer stan-baseline
# または
tools/phpstan --generate-baseline
```

### 実行コマンド

```bash
composer phpstan
# または
tools/phpstan analyse
```

## 依存関係

### cakephp-codesniffer

CakePHP専用のコーディング規約：

```json
{
    "require-dev": {
        "cakephp/cakephp-codesniffer": "^5.0"
    }
}
```

### allow-plugins 設定

```json
{
    "config": {
        "allow-plugins": {
            "dealerdirect/phpcodesniffer-composer-installer": true
        }
    }
}
```

## PHP宣言

### 厳密な型宣言

すべてのPHPファイルの先頭に記述：

```php
<?php
declare(strict_types=1);
```

### ライセンスヘッダ

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

## CI での品質チェック

cs-stan ワークフローが以下を実行する：

1. PHPStan による静的解析
2. PHPCS によるコーディング規約チェック

```yaml
cs-stan:
  uses: cakephp/.github/.github/workflows/cs-stan.yml@5.x
  secrets: inherit
```

## 品質チェックの実行順序

1. コーディング規約チェック（高速）
2. 静的解析（中速）
3. ユニットテスト（低速）

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