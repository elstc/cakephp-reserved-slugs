# CakePHP Plugin CI/GitHub Actions 設定ガイドライン

## 概要

CakePHP公式プラグインは `cakephp/.github` リポジトリの再利用可能ワークフローを活用している。

## 基本構成

### シンプルなプラグイン（データベース不要）

```yaml
name: CI

on:
  push:
    branches:
      - main
      - 3.x
  pull_request:
    branches:
      - '*'
  workflow_dispatch:

permissions:
  contents: read

jobs:
  testsuite:
    uses: cakephp/.github/.github/workflows/testsuite-without-db.yml@5.x
    secrets: inherit

  cs-stan:
    uses: cakephp/.github/.github/workflows/cs-stan.yml@5.x
    secrets: inherit
```

### データベースを使用するプラグイン

```yaml
name: CI

on:
  push:
    branches:
      - main
      - 3.x
  pull_request:
    branches:
      - '*'
  workflow_dispatch:

permissions:
  contents: read

jobs:
  testsuite:
    uses: cakephp/.github/.github/workflows/testsuite-with-db.yml@5.x
    secrets: inherit

  cs-stan:
    uses: cakephp/.github/.github/workflows/cs-stan.yml@5.x
    secrets: inherit
```

## 再利用可能ワークフロー

CakePHP公式が提供する再利用可能ワークフロー：

| ワークフロー | 用途 |
|-------------|------|
| `testsuite-without-db.yml` | データベース不要のテスト実行 |
| `testsuite-with-db.yml` | データベース使用のテスト実行 |
| `cs-stan.yml` | コーディング規約 + PHPStan |

## カスタムCIの作成（複数DB対応）

複雑なデータベーステストが必要な場合：

```yaml
name: CI

on:
  push:
    branches:
      - main
  pull_request:
    branches:
      - '*'
  workflow_dispatch:

permissions:
  contents: read

jobs:
  testsuite-linux:
    runs-on: ubuntu-22.04
    strategy:
      fail-fast: false
      matrix:
        php-version: ['8.1', '8.4']
        db-type: [mysql, pgsql, sqlite]
        include:
          - php-version: '8.1'
            db-type: 'sqlite'
            prefer-lowest: 'prefer-lowest'
    services:
      postgres:
        image: postgres
        ports:
          - 5432:5432
        env:
          POSTGRES_USER: postgres
          POSTGRES_PASSWORD: pg-password
          POSTGRES_DB: cakephp_test
        options: >-
          --health-cmd pg_isready
          --health-interval 10s
          --health-timeout 5s
          --health-retries 5

    steps:
      - uses: actions/checkout@v6
        with:
          persist-credentials: false

      - name: Setup MySQL
        if: matrix.db-type == 'mysql'
        run: |
          sudo service mysql start
          mysql -h 127.0.0.1 -u root -proot -e 'CREATE DATABASE cakephp_test;'

      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php-version }}
          extensions: mbstring, intl, pdo_${{ matrix.db-type }}
          coverage: pcov

      - name: Get composer cache directory
        id: composer-cache
        run: echo "dir=$(composer config cache-files-dir)" >> $GITHUB_OUTPUT

      - name: Cache composer dependencies
        uses: actions/cache@v5
        with:
          path: ${{ steps.composer-cache.outputs.dir }}
          key: ${{ runner.os }}-composer-${{ hashFiles('composer.json') }}

      - name: Composer install
        run: |
          if ${{ matrix.prefer-lowest == 'prefer-lowest' }}; then
            composer update --prefer-lowest --prefer-stable
          else
            composer update
          fi

      - name: Run PHPUnit
        run: vendor/bin/phpunit

      - name: Code Coverage Report
        if: success() && matrix.php-version == '8.1' && matrix.db-type == 'mysql'
        uses: codecov/codecov-action@v5

  cs-stan:
    uses: cakephp/.github/.github/workflows/cs-stan.yml@5.x
    secrets: inherit
```

## Stale Issue/PR 管理

```yaml
name: Mark stale issues and pull requests

on:
  schedule:
  - cron: "45 2 * * *"

jobs:
  stale:
    runs-on: ubuntu-latest

    steps:
    - uses: actions/stale@v10
      with:
        repo-token: ${{ secrets.GITHUB_TOKEN }}
        stale-issue-message: 'This issue is stale because it has been open for 120 days with no activity. Remove the `stale` label or comment or this will be closed in 15 days'
        stale-pr-message: 'This pull request is stale because it has been open 30 days with no activity. Remove the `stale` label or comment on this issue, or it will be closed in 15 days'
        stale-issue-label: 'stale'
        stale-pr-label: 'stale'
        days-before-stale: 120
        days-before-close: 15
        exempt-issue-labels: 'pinned'
        exempt-pr-labels: 'pinned'
```

## PHPバージョンマトリクス

推奨されるPHPバージョンのテスト構成：

```yaml
matrix:
  php-version: ['8.1', '8.2', '8.3', '8.4']
  dependencies: [highest]
  include:
    - php-version: '8.1'
      dependencies: lowest  # 最小依存バージョンのテスト
```

## コードカバレッジ

Codecov を使用したカバレッジレポート：

```yaml
- name: Run PHPUnit with coverage
  run: vendor/bin/phpunit --coverage-clover=coverage.xml

- name: Code Coverage Report
  uses: codecov/codecov-action@v5
  with:
    token: ${{ secrets.CODECOV_TOKEN }}
```

## ベストプラクティス

1. `persist-credentials: false` を checkout で設定し、セキュリティを強化する
2. Composer キャッシュを活用して CI 実行時間を短縮する
3. `fail-fast: false` でマトリクス全体のテスト結果を取得する
4. 最小依存バージョン（prefer-lowest）でのテストを含める
5. `permissions: contents: read` で最小限の権限を設定する
6. `schedule` で月1回の定期ビルドを設定し、依存関係の破壊的変更を早期に検出する
7. 複数のCakePHPバージョンでテストを実行し、互換性を保証する