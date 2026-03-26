<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace Elastic\SlugGuard\Test\TestCase\Model\Rule;

use Cake\ORM\Entity;
use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Elastic\SlugGuard\Model\Rule\IsNotRouteConflict;
use PHPUnit\Framework\Attributes\DataProvider;

class IsNotRouteConflictTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $builder = Router::createRouteBuilder('/');
        $builder->connect('/admin/dashboard', ['controller' => 'Admin', 'action' => 'dashboard']);
        $builder->connect('/api/users', ['controller' => 'ApiUsers', 'action' => 'index']);
        $builder->connect('/posts/{id}', ['controller' => 'Posts', 'action' => 'view']);
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        Router::reload();
    }

    /**
     * @return array<string, array{array<string, mixed>, bool}>
     */
    public static function defaultFieldProvider(): array
    {
        return [
            'conflicting slug is rejected' => [['slug' => 'admin'], false],
            'another conflicting slug is rejected' => [['slug' => 'api'], false],
            'non-conflicting slug is allowed' => [['slug' => 'my-custom-slug'], true],
            'empty slug is allowed' => [['slug' => ''], true],
            'null slug is allowed' => [['slug' => null], true],
            'missing slug field is allowed' => [['name' => 'test'], true],
        ];
    }

    /**
     * @param array<string, mixed> $entityData
     */
    #[DataProvider('defaultFieldProvider')]
    public function testDefaultFieldValidation(array $entityData, bool $expected): void
    {
        // Arrange
        $rule = new IsNotRouteConflict();
        $entity = new Entity($entityData);

        // Act
        $result = $rule($entity, []);

        // Assert
        $this->assertSame($expected, $result);
    }

    public function testCustomFieldValidation(): void
    {
        // Arrange
        $rule = new IsNotRouteConflict('username');
        $entity = new Entity(['username' => 'admin']);

        // Act
        $result = $rule($entity, []);

        // Assert
        $this->assertFalse($result);
    }

    public function testCustomFieldValidation_withNonConflictingValue(): void
    {
        // Arrange
        $rule = new IsNotRouteConflict('username');
        $entity = new Entity(['username' => 'john-doe']);

        // Act
        $result = $rule($entity, []);

        // Assert
        $this->assertTrue($result);
    }
}
