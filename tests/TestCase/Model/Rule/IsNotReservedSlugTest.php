<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace Elastic\SlugGuard\Test\TestCase\Model\Rule;

use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\TestSuite\TestCase;
use Elastic\SlugGuard\Model\Rule\IsNotReservedSlug;
use Elastic\SlugGuard\Model\Table\SlugExistenceInterface;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;

class IsNotReservedSlugTest extends TestCase
{
    /**
     * @var list<string>
     */
    protected array $fixtures = [
        'plugin.Elastic/SlugGuard.ReservedSlugs',
    ];

    /**
     * @return array<string, array{array<string, mixed>, bool}>
     */
    public static function defaultFieldProvider(): array
    {
        return [
            'reserved slug is rejected' => [['slug' => 'admin'], false],
            'non-reserved slug is allowed' => [['slug' => 'my-custom-slug'], true],
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
        $rule = new IsNotReservedSlug();
        $entity = new Entity($entityData);

        // Act
        $result = $rule($entity, []);

        // Assert
        $this->assertSame($expected, $result);
    }

    /**
     * @return array<string, array{string, bool}>
     */
    public static function customFieldProvider(): array
    {
        return [
            'reserved value is rejected' => ['admin', false],
            'non-reserved value is allowed' => ['my-custom-name', true],
        ];
    }

    #[DataProvider('customFieldProvider')]
    public function testCustomFieldValidation(string $value, bool $expected): void
    {
        // Arrange
        $rule = new IsNotReservedSlug('username');
        $entity = new Entity(['username' => $value]);

        // Act
        $result = $rule($entity, []);

        // Assert
        $this->assertSame($expected, $result);
    }

    public function testCustomTableReservedSlugIsRejected(): void
    {
        // Arrange
        // -----------------------------------------------
        $this->registerCustomSlugTable();
        $rule = new IsNotReservedSlug('slug', 'CustomReservedSlugs');
        $entity = new Entity(['slug' => 'taken']);

        // Act
        // -----------------------------------------------
        $result = $rule($entity, []);

        // Assert
        // -----------------------------------------------
        $this->assertFalse($result);
    }

    public function testCustomTableNonReservedSlugIsAllowed(): void
    {
        // Arrange
        // -----------------------------------------------
        $this->registerCustomSlugTable();
        $rule = new IsNotReservedSlug('slug', 'CustomReservedSlugs');
        $entity = new Entity(['slug' => 'available']);

        // Act
        // -----------------------------------------------
        $result = $rule($entity, []);

        // Assert
        // -----------------------------------------------
        $this->assertTrue($result);
    }

    /**
     * Registers a custom table implementing SlugExistenceInterface under the
     * 'CustomReservedSlugs' alias. The table treats only 'taken' as reserved.
     */
    private function registerCustomSlugTable(): void
    {
        $customTable = new class extends Table implements SlugExistenceInterface {
            public function slugExists(string $slug): bool
            {
                return $slug === 'taken';
            }
        };
        $this->getTableLocator()->set('CustomReservedSlugs', $customTable);
    }

    public function testTableWithoutInterfaceThrowsException(): void
    {
        // Arrange
        // -----------------------------------------------
        // Use a plain Table that does NOT implement SlugExistenceInterface
        $rule = new IsNotReservedSlug('slug', 'NonCompliantTable');
        $entity = new Entity(['slug' => 'test']);

        // Assert
        // -----------------------------------------------
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('must implement');

        // Act
        // -----------------------------------------------
        $rule($entity, []);
    }
}
