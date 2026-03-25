<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace ReservedSlugs\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class ImportCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * @var list<string>
     */
    protected array $fixtures = [
        'plugin.ReservedSlugs.ReservedSlugs',
    ];

    public function testImportFromFile(): void
    {
        // Arrange
        $file = TMP . 'test_cmd_import.txt';
        file_put_contents($file, "admin\nnew-import\nanother-import\n");

        // Act
        $this->exec('reserved_slugs import ' . $file);

        // Assert
        $this->assertExitSuccess();
        $this->assertOutputContains('Imported 2 new slug(s).');

        $table = TableRegistry::getTableLocator()->get('ReservedSlugs.ReservedSlugs');
        $this->assertTrue($table->exists(['slug' => 'new-import']));
        $this->assertTrue($table->exists(['slug' => 'another-import']));

        unlink($file);
    }

    public function testImportFromNonExistentFile(): void
    {
        $this->exec('reserved_slugs import /nonexistent/file.txt');

        $this->assertExitError();
        $this->assertErrorContains('File not found');
    }
}
