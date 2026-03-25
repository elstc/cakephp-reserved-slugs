---
paths:
  - tests/
---
# AAA（Arrange-Act-Assert）テスト記述パターン - PHPUnit

## 概要

AAAパターンは、テストコードを3つの明確なセクションに分割する記述方式である。
可読性が高く、テストの意図が明確になるため、すべてのテストでこのパターンを採用する。

## 3つのフェーズ

### 1. Arrange（準備）

テストに必要なオブジェクト、データ、状態をセットアップする。

- テスト対象オブジェクトのインスタンス化
- モック・スタブの設定
- テストデータの準備
- 前提条件の設定

### 2. Act（実行）

テスト対象の操作を1つだけ実行する。

- テスト対象メソッドの呼び出し
- 戻り値の取得
- 副作用の発生

### 3. Assert（検証）

期待する結果を検証する。

- 戻り値の検証
- 状態変化の検証
- 例外の検証
- 副作用の検証

## コード例

### 基本的なテスト

```php
<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class OrderServiceTest extends TestCase
{
    #[Test]
    public function 注文合計金額を正しく計算できること(): void
    {
        // Arrange
        // -----------------------------------------------
        // 注文オブジェクトと商品を準備する
        $order = new Order();
        $order->addItem(new OrderItem('商品A', 1000, 2));
        $order->addItem(new OrderItem('商品B', 500, 3));

        // Act
        // -----------------------------------------------
        // 合計金額を計算する
        $total = $order->calculateTotal();

        // Assert
        // -----------------------------------------------
        // 合計金額が正しいことを検証する
        $this->assertSame(3500, $total);
    }
}
```

### 例外テスト

```php
#[Test]
public function 在庫不足の場合は例外が発生すること(): void
{
    // Arrange
    // -----------------------------------------------
    // 在庫チェックが失敗するモックを準備する
    $stockService = $this->createMock(StockService::class);
    $stockService->method('checkStock')->willReturn(false);
    $orderService = new OrderService($stockService);
    $order = new Order();
    $order->addItem(new OrderItem('商品A', 1000, 100));

    // Assert
    // -----------------------------------------------
    // 例外検証は実行前に宣言する
    $this->expectException(InsufficientStockException::class);
    $this->expectExceptionMessage('在庫が不足しています');

    // Act
    // -----------------------------------------------
    // 注文を実行する
    $orderService->placeOrder($order);
}
```

### モックを使った副作用の検証

```php
#[Test]
public function 注文完了時にメール通知が送信されること(): void
{
    // Arrange
    // -----------------------------------------------
    // メール送信モックと注文を準備する
    $mailer = $this->createMock(MailerInterface::class);
    $mailer->expects($this->once())
        ->method('send')
        ->with($this->callback(function ($mail) {
            return $mail->getSubject() === '注文完了のお知らせ';
        }));
    $orderService = new OrderService(mailer: $mailer);
    $order = new Order(customerEmail: 'test@example.com');

    // Act
    // -----------------------------------------------
    // 注文を実行する
    $orderService->placeOrder($order);

    // Assert
    // -----------------------------------------------
    // モックの expects で検証済み
}
```

## ベストプラクティス

### コメントの活用

各セクションの先頭に視覚的に区切られたコメントブロックを記述する。

```php
// Arrange
// -----------------------------------------------
// テストデータを準備する
$user = new User('test@example.com');

// Act
// -----------------------------------------------
// メールアドレスの妥当性を検証する
$result = $user->isValidEmail();

// Assert
// -----------------------------------------------
// 妥当なメールアドレスであることを確認する
$this->assertTrue($result);
```

### 空行による分離

各セクション間は空行で分離し、視覚的な区切りを明確にする。

### Act は1つの操作に限定する

1つのテストで1つの振る舞いのみをテストする。

```php
// ❌ 悪い例：複数の操作
// Act
$order->addItem($item);
$order->applyDiscount(10);
$total = $order->calculateTotal();

// ✅ 良い例：1つの操作
// Arrange
// -----------------------------------------------
// 前提条件として注文に商品と割引を設定する
$order->addItem($item);
$order->applyDiscount(10);

// Act
// -----------------------------------------------
// 合計金額を計算する
$total = $order->calculateTotal();
```

### Arrange のヘルパーメソッド化

Arrange が長くなる場合は、セットアップをヘルパーメソッドに抽出する。

```php
#[Test]
public function 有効な注文の合計金額を計算できること(): void
{
    // Arrange
    // -----------------------------------------------
    // 複数商品を含む注文を準備する
    $order = $this->createOrderWithItems([
        ['name' => '商品A', 'price' => 1000, 'quantity' => 2],
        ['name' => '商品B', 'price' => 500, 'quantity' => 3],
    ]);

    // Act
    // -----------------------------------------------
    // 合計金額を計算する
    $total = $order->calculateTotal();

    // Assert
    // -----------------------------------------------
    // 合計金額が正しいことを検証する
    $this->assertSame(3500, $total);
}

private function createOrderWithItems(array $items): Order
{
    $order = new Order();
    foreach ($items as $item) {
        $order->addItem(new OrderItem(
            $item['name'],
            $item['price'],
            $item['quantity']
        ));
    }
    return $order;
}
```

## アンチパターン

### ❌ 複数の Assert の分散

```php
// 悪い例：Act の前後に Assert が分散
$this->assertSame(0, $cart->getItemCount());  // Assert
$cart->addItem($item);                         // Act
$this->assertSame(1, $cart->getItemCount());  // Assert
$cart->addItem($item);                         // Act
$this->assertSame(2, $cart->getItemCount());  // Assert
```

### ❌ Arrange と Act の混在

```php
// 悪い例：準備と実行が混在
$user = new User();
$user->setName('テスト');      // これは Arrange
$result1 = $user->validate();  // これは Act？
$user->setEmail('test@example.com');  // これは Arrange
$result2 = $user->save();      // これも Act？
```

### ❌ Assert の欠落

```php
// 悪い例：検証がない
#[Test]
public function なにかをテストする(): void
{
    $service = new SomeService();
    $service->doSomething();
    // Assert がない！
}
```

## 例外テストのパターン

PHPUnit では `expectException` は Act の前に宣言する。

```php
// Arrange
// -----------------------------------------------
// 決済サービスを準備する
$service = new PaymentService();

// Assert
// -----------------------------------------------
// 例外期待の宣言
$this->expectException(InvalidArgumentException::class);
$this->expectExceptionMessage('金額は正の数である必要があります');

// Act
// -----------------------------------------------
// 不正な金額で処理を実行する
$service->process(-100);
```

## アサーションの使い分け

`assertSame` を `assertEquals` より優先して使用する（型の厳密な比較）。

```php
// ✅ 推奨：型も含めて厳密に比較
$this->assertSame(3500, $total);
$this->assertSame('expected', $actual);

// ❌ 非推奨：型の比較が緩い
$this->assertEquals(3500, $total);
```

## 参考文献

- [Arrange-Act-Assert pattern - Microsoft](https://docs.microsoft.com/en-us/visualstudio/test/unit-test-basics)
- [PHPUnit Documentation](https://docs.phpunit.de/)