---
paths:
  - ".github/workflows/"
---
# CakePHP Plugin CI / GitHub Actions Configuration

## Overview

Official CakePHP plugins leverage reusable workflows from the `cakephp/.github` repository.

## Basic Configuration

### Simple Plugin (No Database)

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

### Plugin with Database

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

## Reusable Workflows

Reusable workflows provided by CakePHP official:

| Workflow | Purpose |
|----------|---------|
| `testsuite-without-db.yml` | Test execution without database |
| `testsuite-with-db.yml` | Test execution with database |
| `cs-stan.yml` | Coding standards + PHPStan |

## Custom CI (Multiple DB Support)

For complex database testing needs:

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

## Stale Issue/PR Management

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

## PHP Version Matrix

Recommended PHP version test configuration:

```yaml
matrix:
  php-version: ['8.1', '8.2', '8.3', '8.4']
  dependencies: [highest]
  include:
    - php-version: '8.1'
      dependencies: lowest  # Test with minimum dependency versions
```

## Code Coverage

Coverage reporting with Codecov:

```yaml
- name: Run PHPUnit with coverage
  run: vendor/bin/phpunit --coverage-clover=coverage.xml

- name: Code Coverage Report
  uses: codecov/codecov-action@v5
  with:
    token: ${{ secrets.CODECOV_TOKEN }}
```

## Best Practices

1. Set `persist-credentials: false` on checkout for improved security
2. Use Composer caching to reduce CI execution time
3. Set `fail-fast: false` to get full matrix test results
4. Include minimum dependency version testing (prefer-lowest)
5. Set `permissions: contents: read` for minimal permissions
6. Configure monthly scheduled builds with `schedule` to detect breaking changes in dependencies early
7. Run tests across multiple CakePHP versions to ensure compatibility