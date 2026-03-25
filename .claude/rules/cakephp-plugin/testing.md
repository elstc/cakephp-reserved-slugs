---
paths:
  - tests/
---
# CakePHP Plugin テスト作成ガイドライン

## ディレクトリ構造

```
tests/
├── bootstrap.php              # テスト用ブートストラップ
├── TestCase/                  # テストケース
│   ├── AuthenticatorTest.php  # 例: 認証系テスト
│   └── {Category}/            # カテゴリ別サブディレクトリ
│       └── SomeTest.php
├── test_app/                  # テスト用アプリケーション
│   ├── TestApp/               # テストアプリ本体
│   │   ├── Application.php
│   │   └── ...
│   ├── Plugin/                # テスト用プラグイン
│   │   └── TestPlugin/
│   ├── config/                # テスト用設定
│   └── templates/             # テスト用テンプレート
└── data/                      # テストデータ（任意）
```

## bootstrap.php

```php
<?php
declare(strict_types=1);

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use Cake\Routing\Router;
use Cake\Utility\Security;
use {PluginNamespace}\Plugin as MyPlugin;
use function Cake\Core\env;

$findRoot = function ($root) {
    do {
        $lastRoot = $root;
        $root = dirname($root);
        if (is_dir($root . '/vendor/cakephp/cakephp')) {
            return $root;
        }
    } while ($root !== $lastRoot);
    throw new Exception('Cannot find the root of the application, unable to run tests');
};
$root = $findRoot(__FILE__);
unset($findRoot);
chdir($root);

require_once 'vendor/autoload.php';

define('ROOT', $root . DS . 'tests' . DS . 'test_app' . DS);
define('APP', ROOT . 'App' . DS);
define('TMP', sys_get_temp_dir() . DS);
define('CONFIG', ROOT . DS . 'config' . DS);

Configure::write('debug', true);
Configure::write('App', [
    'namespace' => 'TestApp',
    'encoding' => 'UTF-8',
    'paths' => [
        'plugins' => [ROOT . 'Plugin' . DS],
        'templates' => [ROOT . 'templates' . DS],
    ],
]);

Cache::setConfig([
    '_cake_translations_' => [
        'engine' => 'Array',
    ],
]);

if (!getenv('DB_URL')) {
    putenv('DB_URL=sqlite:///:memory:');
}
ConnectionManager::setConfig('test', ['url' => getenv('DB_URL')]);
Router::reload();
Security::setSalt('YJfIxfs2guVoUubWDYhG93b0qyJfIxfs2guwvniR2G0FgaC9mi');

Plugin::getCollection()->add(new MyPlugin());

$_SERVER['PHP_SELF'] = '/';
```

## phpunit.xml.dist

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         colors="true"
         processIsolation="false"
         stopOnFailure="false"
         failOnDeprecation="true"
         displayDetailsOnTestsThatTriggerDeprecations="true"
         cacheDirectory=".phpunit.cache"
         bootstrap="tests/bootstrap.php"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/10.1/phpunit.xsd">

    <testsuites>
        <testsuite name="plugin-name">
            <directory>tests/TestCase/</directory>
        </testsuite>
    </testsuites>

    <extensions>
        <bootstrap class="Cake\TestSuite\Fixture\Extension\PHPUnitExtension"/>
    </extensions>

    <source>
        <include>
            <directory suffix=".php">src/</directory>
        </include>
        <exclude>
            <!-- 必要に応じてカバレッジ除外ファイルを指定 -->
        </exclude>
    </source>

    <php>
        <ini name="memory_limit" value="-1"/>
        <env name="FIXTURE_SCHEMA_METADATA" value="./vendor/cakephp/cakephp/tests/schema.php"/>
    </php>
</phpunit>
```

## テストクラスの基本構造

```php
<?php
declare(strict_types=1);

namespace {PluginNamespace}\Test\TestCase;

use Cake\TestSuite\TestCase;

class SomeFeatureTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'core.AuthUsers',
        'core.Users',
    ];

    /**
     * setUp method
     */
    public function setUp(): void
    {
        parent::setUp();
        // テストのセットアップ
    }

    /**
     * tearDown method
     */
    public function tearDown(): void
    {
        parent::tearDown();
        // テストのクリーンアップ
    }

    /**
     * Test some feature
     *
     * @return void
     */
    public function testSomeFeature(): void
    {
        $this->assertSame('expected', 'actual');
    }
}
```

## テスト命名規則

- テストクラス: `{対象クラス名}Test.php`
- テストメソッド: `test{テスト対象の動作を説明する名前}`

例:
- `SessionAuthenticatorTest.php`
- `testAuthenticateSuccess()`
- `testAuthenticateWithInvalidCredentials()`

## Fixture の使用

CakePHP の Fixture を活用してテストデータを管理する：

```php
protected array $fixtures = [
    'core.AuthUsers',          // CakePHP コアの Fixture
    'plugin.MyPlugin.Items',   // プラグイン固有の Fixture
];
```

## モックの活用

```php
$this->sessionMock = $this->getMockBuilder(Session::class)
    ->disableOriginalConstructor()
    ->onlyMethods(['read', 'write', 'delete', 'renew', 'check'])
    ->getMock();

$this->sessionMock->expects($this->once())
    ->method('read')
    ->with('Auth')
    ->willReturn(['username' => 'test']);
```

## アサーション

`assertSame` を `assertEquals` より優先して使用する（型の厳密な比較）：

```php
// 推奨
$this->assertSame('expected', $actual);

// 非推奨
$this->assertEquals('expected', $actual);
```