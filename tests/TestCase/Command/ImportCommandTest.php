<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace Elastic\SlugGuard\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

class ImportCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * @var list<string>
     */
    protected array $fixtures = [
        'plugin.Elastic/SlugGuard.ReservedSlugs',
    ];

    public function testImportFromFile(): void
    {
        // Arrange
        // -----------------------------------------------
        $file = TMP . 'test_cmd_import.txt';
        file_put_contents($file, "admin\nnew-import\nanother-import\n");

        try {
            // Act
            // -----------------------------------------------
            $this->exec('slug_guard import ' . $file);

            // Assert
            // -----------------------------------------------
            $this->assertExitSuccess();
            $this->assertOutputContains('Imported 2 new slug(s).');

            $table = $this->fetchTable('Elastic/SlugGuard.ReservedSlugs');
            $this->assertTrue($table->exists(['slug' => 'new-import']));
            $this->assertTrue($table->exists(['slug' => 'another-import']));
        } finally {
            unlink($file);
        }
    }

    public function testImportFromNonExistentFile(): void
    {
        // Act
        // -----------------------------------------------
        $this->exec('slug_guard import /nonexistent/file.txt');

        // Assert
        // -----------------------------------------------
        $this->assertExitError();
        $this->assertErrorContains('File not found');
    }
}
