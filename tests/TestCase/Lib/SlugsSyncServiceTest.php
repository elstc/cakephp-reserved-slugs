<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace Elastic\SlugGuard\Test\TestCase\Lib;

use Cake\TestSuite\TestCase;
use Elastic\SlugGuard\Lib\SlugsSyncService;
use Elastic\SlugGuard\Model\Table\ReservedSlugsTable;
use InvalidArgumentException;

class SlugsSyncServiceTest extends TestCase
{
    /**
     * @var list<string>
     */
    protected array $fixtures = [
        'plugin.Elastic/SlugGuard.ReservedSlugs',
    ];

    protected SlugsSyncService $service;

    protected ReservedSlugsTable $table;

    public function setUp(): void
    {
        parent::setUp();
        /** @var \Elastic\SlugGuard\Model\Table\ReservedSlugsTable $table */
        $table = $this->fetchTable('Elastic/SlugGuard.ReservedSlugs');
        $this->table = $table;
        $this->service = new SlugsSyncService($this->table);
    }

    public function tearDown(): void
    {
        unset($this->service, $this->table);
        parent::tearDown();
    }

    public function testImportFromFile(): void
    {
        // Arrange
        // -----------------------------------------------
        $file = TMP . 'test_import_slugs.txt';
        file_put_contents($file, implode("\n", [
            '# comment line',
            'admin',
            'new-import-slug',
            'another-slug',
            '',
            '# another comment',
        ]));

        try {
            // Act
            // -----------------------------------------------
            $added = $this->service->importFromFile($file);

            // Assert
            // -----------------------------------------------
            $this->assertSame(2, $added);
            $this->assertTrue($this->table->slugExists('new-import-slug'));
            $this->assertTrue($this->table->slugExists('another-slug'));
        } finally {
            unlink($file);
        }
    }

    public function testImportFromFileNotFound(): void
    {
        // Assert
        // -----------------------------------------------
        $this->expectException(InvalidArgumentException::class);

        // Act
        // -----------------------------------------------
        $this->service->importFromFile('/nonexistent/file.txt');
    }

    public function testSyncFromFile(): void
    {
        // Arrange
        // -----------------------------------------------
        $file = TMP . 'test_sync_slugs.txt';
        file_put_contents($file, implode("\n", [
            'admin',
            'api',
            'new-sync-slug',
        ]));

        try {
            // Act
            // -----------------------------------------------
            $result = $this->service->syncFromFile($file);

            // Assert
            // -----------------------------------------------
            $this->assertSame(1, $result['added']);
            $this->assertSame(3, $result['removed']);
            $this->assertTrue($this->table->slugExists('admin'));
            $this->assertTrue($this->table->slugExists('api'));
            $this->assertTrue($this->table->slugExists('new-sync-slug'));
            $this->assertFalse($this->table->slugExists('blog'));
            $this->assertFalse($this->table->slugExists('dashboard'));
            $this->assertFalse($this->table->slugExists('help'));
        } finally {
            unlink($file);
        }
    }

    public function testSyncFromFileNotFound(): void
    {
        // Assert
        // -----------------------------------------------
        $this->expectException(InvalidArgumentException::class);

        // Act
        // -----------------------------------------------
        $this->service->syncFromFile('/nonexistent/file.txt');
    }

    public function testCalculateDiff(): void
    {
        // Arrange
        // -----------------------------------------------
        $file = TMP . 'test_diff_slugs.txt';
        file_put_contents($file, implode("\n", [
            'admin',
            'api',
            'new-diff-slug',
        ]));

        try {
            // Act
            // -----------------------------------------------
            $diff = $this->service->calculateDiff($file);

            // Assert
            // -----------------------------------------------
            $this->assertSame($file, $diff['filePath']);
            $this->assertSame(['new-diff-slug'], $diff['toAdd']);
            $this->assertCount(3, $diff['toRemove']);
            $this->assertContains('blog', $diff['toRemove']);
            $this->assertContains('dashboard', $diff['toRemove']);
            $this->assertContains('help', $diff['toRemove']);
        } finally {
            unlink($file);
        }
    }

    public function testCalculateDiffNotFound(): void
    {
        // Assert
        // -----------------------------------------------
        $this->expectException(InvalidArgumentException::class);

        // Act
        // -----------------------------------------------
        $this->service->calculateDiff('/nonexistent/file.txt');
    }
}
