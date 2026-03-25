<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace ReservedSlugs\Test\TestCase\Model\Rule;

use Cake\ORM\Entity;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use ReservedSlugs\Model\Rule\IsNotReservedSlug;

class IsNotReservedSlugTest extends TestCase
{
    /**
     * @var list<string>
     */
    protected array $fixtures = [
        'plugin.ReservedSlugs.ReservedSlugs',
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
}
