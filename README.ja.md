# SlugGuard plugin for CakePHP

[![CI](https://github.com/elstc/cakephp-slug-guard/actions/workflows/ci.yml/badge.svg)](https://github.com/elstc/cakephp-slug-guard/actions/workflows/ci.yml)
[![Latest Stable Version](https://img.shields.io/github/v/release/elstc/cakephp-slug-guard?sort=semver&style=flat-square)](https://packagist.org/packages/elstc/cakephp-slug-guard)
[![Total Downloads](https://img.shields.io/packagist/dt/elstc/cakephp-slug-guard?style=flat-square)](https://packagist.org/packages/elstc/cakephp-slug-guard/stats)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)

ユーザーが選択したスラッグが URL で安全に使用でき、システムルートや予約語と衝突しないことを保証する CakePHP プラグインです。

[English](README.md)

## バージョン対応表

| CakePHP | PHP    | Plugin | Branch |
|---------|--------|--------|--------|
| 5.x     | >= 8.2 | 5.x    | cake5  |

- **SlugValidator** — 文字列がサブドメインラベルや URL パスセグメントとして適切な形式かを検証します（小文字英数字 + ハイフン、長さ制約）。
- **IsNotReservedSlug** — スラッグを予約語リストと照合し、システムルートや既知のパスとの衝突を防止します。

## インストール

### 前提条件

- CakePHP 5.x アプリケーション
- PHP >= 8.2
- データベース接続が設定済みであること（`config/app_local.php`）

### 手順

1. Composer でプラグインをインストールする:

   ```shell
   composer require elstc/cakephp-slug-guard
   ```

2. プラグインをロードする。`src/Application.php` に `$this->addPlugin('Elastic/SlugGuard')` が追加される:

   ```shell
   bin/cake plugin load Elastic/SlugGuard
   ```

3. マイグレーションを実行して `reserved_slugs` テーブルを作成する:

   ```shell
   bin/cake migrations migrate --plugin Elastic/SlugGuard
   ```

4. デフォルトの予約スラッグ（約710件の一般的な予約語）をインポートする:

   ```shell
   bin/cake slug_guard sync
   ```

### インストールの確認

以下のコマンドで予約スラッグがインポートされたことを確認する:

```shell
bin/cake slug_guard list --count
```

期待される結果: 約710件の予約スラッグがカウントされる。

## 使い方

### バリデーションルール

テーブルの `buildRules()` メソッドに `IsNotReservedSlug` ルールを追加します：

```php
use Elastic\SlugGuard\Model\Rule\IsNotReservedSlug;

public function buildRules(RulesChecker $rules): RulesChecker
{
    $rules->add(new IsNotReservedSlug('slug'), 'reservedSlug', [
        'errorField' => 'slug',
        'message' => 'このスラッグは予約されています。',
    ]);

    return $rules;
}
```

#### カスタムフィールド名

```php
$rules->add(new IsNotReservedSlug('username'), 'reservedSlug', [
    'errorField' => 'username',
    'message' => 'このユーザー名は予約されています。',
]);
```

### スラッグ形式バリデーター

`SlugValidator` クラスはスラッグ形式（小文字英数字とハイフン）を検証する静的メソッドを提供します：

```php
use Elastic\SlugGuard\Validation\SlugValidator;

// Validator プロバイダとして使用
$validator->setProvider('slugValidator', SlugValidator::class);
$validator->add('slug', 'validSlug', [
    'rule' => ['isValid'],
    'provider' => 'slugValidator',
    'message' => '小文字英数字とハイフンのみ使用できます。',
]);
```

#### 設定オプション

##### `minLength`

スラッグの最小長。デフォルト: `4`

##### `maxLength`

スラッグの最大長。デフォルト: `24`

```php
$validator->add('slug', 'validSlug', [
    'rule' => ['isValid', 3, 32],
    'provider' => 'slugValidator',
]);
```

### CLI コマンド

#### 予約スラッグの一覧表示

```shell
bin/cake slug_guard list
bin/cake slug_guard list --count
bin/cake slug_guard list --search admin
```

#### 予約スラッグの追加

```shell
bin/cake slug_guard add my-reserved-slug
bin/cake slug_guard add slug-one slug-two slug-three
```

#### 予約スラッグの削除

```shell
bin/cake slug_guard remove my-reserved-slug
bin/cake slug_guard remove slug-one slug-two slug-three
```

#### ファイルからスラッグをインポート

```shell
bin/cake slug_guard import /path/to/slugs.txt
```

ファイル形式: 1行に1スラッグ、`#` でコメント、空行は無視されます。

#### ファイルとの同期

```shell
# 同期（アプリの設定ファイルを自動検出、なければプラグイン内蔵ファイルを使用）
bin/cake slug_guard sync

# 特定のファイルと同期
bin/cake slug_guard sync --file /path/to/slugs.txt

# 変更内容をプレビュー（実際には適用しない）
bin/cake slug_guard sync --dry-run
```

`--file` を指定しない場合、sync コマンドは以下の優先順位でシードファイルを解決します：

1. アプリケーション設定ファイル: `config/reserved-slugs.txt`（アプリルート内）
2. プラグイン内蔵のシードファイル

アプリケーション設定ファイルのパスは `Configure` で変更できます：

```php
// config/app.php または config/app_local.php
'SlugGuard' => [
    'syncFile' => CONFIG . 'my-custom-slugs.txt',
],
```

### 予約スラッグリストファイル

プラグインには `config/reserved-slugs.txt` にデフォルトの予約スラッグリストが同梱されています。このファイルには、システムルートや既知のパスと衝突する可能性のある約710件の一般的な予約語（例: `admin`, `api`, `login`, `settings`, `www`）が含まれています。

#### ファイル形式

- 1行に1スラッグ
- `#` で始まる行はコメント
- 空行は無視

```text
# システムルート
admin
api
login

# ソーシャルメディア
facebook
twitter
youtube
```

#### 内蔵リストの使用

内蔵リストは、アプリレベルの設定ファイルが存在しない場合に `sync` コマンドのフォールバックとして使用されます。直接インポートすることもできます：

```shell
bin/cake slug_guard import vendor/elstc/cakephp-slug-guard/config/reserved-slugs.txt
```

#### カスタムリストの使用

同じ形式で独自のファイルを作成し、`import` や `sync` で使用できます：

```shell
# カスタムファイルからスラッグを追加インポート
bin/cake slug_guard import /path/to/my-slugs.txt

# データベースをカスタムファイルと完全に一致させる
bin/cake slug_guard sync --file /path/to/my-slugs.txt
```

> **注意:** `import` はファイルのスラッグをデータベースに追加します（既存のスラッグは保持されます）。`sync` はデータベースをファイルと完全に一致させます — ファイルに含まれないスラッグは削除されます。