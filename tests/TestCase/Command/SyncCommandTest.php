<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace ReservedSlugs\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class SyncCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * @var list<string>
     */
    protected array $fixtures = [
        'plugin.ReservedSlugs.ReservedSlugs',
    ];

    public function testSync(): void
    {
        // Arrange
        $file = TMP . 'test_cmd_sync.txt';
        file_put_contents($file, "admin\napi\nnew-sync\n");

        // Act
        $this->exec('reserved_slugs sync --file ' . $file);

        // Assert
        $this->assertExitSuccess();
        $this->assertOutputContains('Sync complete. Added: 1, Removed: 3');

        $table = TableRegistry::getTableLocator()->get('ReservedSlugs.ReservedSlugs');
        $this->assertTrue($table->exists(['slug' => 'new-sync']));
        $this->assertFalse($table->exists(['slug' => 'blog']));

        unlink($file);
    }

    public function testSyncDryRun(): void
    {
        // Arrange
        $file = TMP . 'test_cmd_sync_dry.txt';
        file_put_contents($file, "admin\napi\nnew-slug\n");

        // Act
        $this->exec('reserved_slugs sync --file ' . $file . ' --dry-run');

        // Assert
        $this->assertExitSuccess();
        $this->assertOutputContains('Slugs to add (1):');
        $this->assertOutputContains('+ new-slug');
        $this->assertOutputContains('Slugs to remove (3):');

        // Verify no changes were applied
        $table = TableRegistry::getTableLocator()->get('ReservedSlugs.ReservedSlugs');
        $this->assertFalse($table->exists(['slug' => 'new-slug']));
        $this->assertTrue($table->exists(['slug' => 'blog']));

        unlink($file);
    }

    public function testSyncDryRunNoChanges(): void
    {
        // Arrange
        $file = TMP . 'test_cmd_sync_nochange.txt';
        file_put_contents($file, "admin\napi\nblog\ndashboard\nhelp\n");

        // Act
        $this->exec('reserved_slugs sync --file ' . $file . ' --dry-run');

        // Assert
        $this->assertExitSuccess();
        $this->assertOutputContains('No changes needed.');

        unlink($file);
    }

    public function testSyncWithoutFileOption(): void
    {
        // Arrange
        $appConfigFile = CONFIG . 'reserved-slugs.txt';
        file_put_contents($appConfigFile, "admin\napi\ncustom-app-slug\n");

        try {
            // Act
            $this->exec('reserved_slugs sync');

            // Assert
            $this->assertExitSuccess();
            $this->assertOutputContains('Sync complete.');

            $table = TableRegistry::getTableLocator()->get('ReservedSlugs.ReservedSlugs');
            $this->assertTrue($table->exists(['slug' => 'custom-app-slug']));
            $this->assertFalse($table->exists(['slug' => 'blog']));
        } finally {
            unlink($appConfigFile);
        }
    }

    public function testSyncDryRunWithoutFileOption(): void
    {
        // Arrange
        $appConfigFile = CONFIG . 'reserved-slugs.txt';
        file_put_contents($appConfigFile, "admin\napi\ncustom-dry-slug\n");

        try {
            // Act
            $this->exec('reserved_slugs sync --dry-run');

            // Assert
            $this->assertExitSuccess();
            $this->assertOutputContains('Slugs to add (1):');
            $this->assertOutputContains('+ custom-dry-slug');
        } finally {
            unlink($appConfigFile);
        }
    }

    public function testSyncWithConfiguredFile(): void
    {
        // Arrange
        $customFile = TMP . 'configured-slugs.txt';
        file_put_contents($customFile, "admin\nconfigured-slug\n");
        Configure::write('ReservedSlugs.syncFile', $customFile);

        try {
            // Act
            $this->exec('reserved_slugs sync');

            // Assert
            $this->assertExitSuccess();
            $this->assertOutputContains('Sync complete.');

            $table = TableRegistry::getTableLocator()->get('ReservedSlugs.ReservedSlugs');
            $this->assertTrue($table->exists(['slug' => 'configured-slug']));
        } finally {
            Configure::delete('ReservedSlugs.syncFile');
            unlink($customFile);
        }
    }

    public function testSyncWithNonExistentFile(): void
    {
        $this->exec('reserved_slugs sync --file /nonexistent/file.txt');

        $this->assertExitError();
        $this->assertErrorContains('File not found');
    }
}
