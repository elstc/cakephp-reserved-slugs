<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace Elastic\SlugGuard\Test\TestCase\Model\Table;

use Cake\TestSuite\TestCase;
use Elastic\SlugGuard\Model\Table\ReservedSlugsTable;
use PHPUnit\Framework\Attributes\DataProvider;

class ReservedSlugsTableTest extends TestCase
{
    /**
     * @var list<string>
     */
    protected array $fixtures = [
        'plugin.Elastic/SlugGuard.ReservedSlugs',
    ];

    protected ReservedSlugsTable $ReservedSlugs;

    public function setUp(): void
    {
        parent::setUp();
        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->ReservedSlugs = $this->fetchTable('Elastic/SlugGuard.ReservedSlugs');
    }

    public function tearDown(): void
    {
        unset($this->ReservedSlugs);
        parent::tearDown();
    }

    public function testInitialize(): void
    {
        $this->assertSame('reserved_slugs', $this->ReservedSlugs->getTable());
        $this->assertSame('slug', $this->ReservedSlugs->getDisplayField());
        $this->assertSame('slug', $this->ReservedSlugs->getPrimaryKey());
    }

    /**
     * @return array<string, array{string, bool}>
     */
    public static function slugExistsProvider(): array
    {
        return [
            'existing slug' => ['admin', true],
            'non-existing slug' => ['non-existing-slug', false],
        ];
    }

    #[DataProvider('slugExistsProvider')]
    public function testSlugExists(string $slug, bool $expected): void
    {
        $this->assertSame($expected, $this->ReservedSlugs->slugExists($slug));
    }

    public function testAddSlug(): void
    {
        $result = $this->ReservedSlugs->addSlug('new-slug');
        $this->assertNotFalse($result);
        $this->assertSame('new-slug', $result->slug);
        $this->assertTrue($this->ReservedSlugs->slugExists('new-slug'));
    }

    public function testAddSlugDuplicate(): void
    {
        $result = $this->ReservedSlugs->addSlug('admin');
        $this->assertFalse($result);
    }

    /**
     * @return array<string, array{string, bool}>
     */
    public static function removeSlugProvider(): array
    {
        return [
            'existing slug' => ['admin', true],
            'non-existing slug' => ['non-existing-slug', false],
        ];
    }

    #[DataProvider('removeSlugProvider')]
    public function testRemoveSlug(string $slug, bool $expected): void
    {
        $this->assertSame($expected, $this->ReservedSlugs->removeSlug($slug));
    }

    public function testValidationDefaultWithEmptySlug(): void
    {
        // Arrange & Act
        // -----------------------------------------------
        $entity = $this->ReservedSlugs->newEntity(['slug' => '']);

        // Assert
        // -----------------------------------------------
        $this->assertNotEmpty($entity->getErrors());
    }

    public function testValidationDefaultWithMissingSlug(): void
    {
        // Arrange & Act
        // -----------------------------------------------
        $entity = $this->ReservedSlugs->newEntity([]);

        // Assert
        // -----------------------------------------------
        $this->assertNotEmpty($entity->getErrors());
    }
}
