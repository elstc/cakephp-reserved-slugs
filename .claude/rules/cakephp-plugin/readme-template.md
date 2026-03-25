# CakePHP Plugin README テンプレート

## 構成

README.mdは以下の構成で作成する：

1. タイトルとバッジ
2. 概要説明
3. バージョンマップ（複数CakePHPバージョン対応時）
4. 関連プラグインへのリンク（該当する場合）
5. インストール手順
6. 使用方法・設定オプション
7. ドキュメントリンク
8. 追加情報（IDEサポート等、任意）

## テンプレート（公式スタイル）

````markdown
# CakePHP {PluginName}

[![CI](https://github.com/{org}/{repo}/actions/workflows/ci.yml/badge.svg)](https://github.com/{org}/{repo}/actions/workflows/ci.yml)
[![Latest Stable Version](https://img.shields.io/github/v/release/{org}/{repo}?sort=semver&style=flat-square)](https://packagist.org/packages/{org}/{repo})
[![Total Downloads](https://img.shields.io/packagist/dt/{org}/{repo}?style=flat-square)](https://packagist.org/packages/{org}/{repo}/stats)
[![Coverage Status](https://img.shields.io/codecov/c/github/{org}/{repo}.svg?style=flat-square)](https://codecov.io/github/{org}/{repo})
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

{プラグインの概要説明を1-2文で記述する}

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

## テンプレート（サードパーティスタイル）

````markdown
# {PluginName} plugin for CakePHP

[![CI](https://github.com/{org}/{repo}/actions/workflows/ci.yml/badge.svg)](https://github.com/{org}/{repo}/actions/workflows/ci.yml)
[![Latest Stable Version](https://img.shields.io/github/v/release/{org}/{repo}?sort=semver&style=flat-square)](https://packagist.org/packages/{org}/{repo})
[![Total Downloads](https://img.shields.io/packagist/dt/{org}/{repo}?style=flat-square)](https://packagist.org/packages/{org}/{repo}/stats)
[![Coverage Status](https://img.shields.io/codecov/c/github/{org}/{repo}.svg?style=flat-square)](https://codecov.io/github/{org}/{repo})
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

{プラグインの概要説明を1-2文で記述する}

## Version Map

| CakePHP Version | Plugin Version | Branch |
|-----------------|----------------|--------|
| 5.x             | 3.x            | cake5  |
| 4.x             | 2.x            | cake4  |
| 3.x             | 1.x            | cake3  |

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```shell
composer require {org}/{repo}
```

Load the plugin by adding the following statement in your project's `src/Application.php`:

```php
$this->addPlugin('{PluginName}');
```

or running the console command:

```shell
bin/cake plugin load {PluginName}
```

## Usage

### Basic Usage

{基本的な使用方法のコード例}

### Configuration Options

#### `optionName`

{オプションの説明}

default: `'default_value'`

```php
// コード例
```
````

## 日本語README

日本語ドキュメントを提供する場合は `README.ja.md` として別ファイルで作成する。
バッジセクションは英語版と同一でよいが、説明文は日本語に翻訳する

## バッジの種類

### 必須バッジ

| バッジ | 用途 |
|--------|------|
| CI | GitHub Actions のビルド状態 |
| Latest Stable Version | 最新リリースバージョン |
| Software License | ライセンス表示 |

### 推奨バッジ

| バッジ | 用途 |
|--------|------|
| Total Downloads | Packagist ダウンロード数 |
| Coverage Status | コードカバレッジ |

## バッジURL形式

### GitHub Actions CI

```markdown
[![CI](https://github.com/{org}/{repo}/actions/workflows/ci.yml/badge.svg)](https://github.com/{org}/{repo}/actions/workflows/ci.yml)
```

### Packagist バージョン

```markdown
[![Latest Stable Version](https://img.shields.io/github/v/release/{org}/{repo}?sort=semver&style=flat-square)](https://packagist.org/packages/{org}/{repo})
```

### Packagist ダウンロード数

```markdown
[![Total Downloads](https://img.shields.io/packagist/dt/{org}/{repo}?style=flat-square)](https://packagist.org/packages/{org}/{repo}/stats)
```

### Codecov カバレッジ

```markdown
[![Coverage Status](https://img.shields.io/codecov/c/github/{org}/{repo}.svg?style=flat-square)](https://codecov.io/github/{org}/{repo})
```

### ライセンス

```markdown
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
```

## 注意事項

- 開発専用ツール（debug_kit等）は :warning: 絵文字で本番環境での使用に関する警告を記載する
- 関連プラグインがある場合は相互リンクを設置する（authentication ↔ authorization など）
- ドキュメントが CakePHP Cookbook にある場合はそちらにリンクする