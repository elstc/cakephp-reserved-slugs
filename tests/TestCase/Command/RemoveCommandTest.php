<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace ReservedSlugs\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class RemoveCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * @var list<string>
     */
    protected array $fixtures = [
        'plugin.ReservedSlugs.ReservedSlugs',
    ];

    public function testRemoveExistingSlug(): void
    {
        $this->exec('reserved_slugs remove admin');

        $this->assertExitSuccess();
        $this->assertOutputContains('Removed: admin');
        $this->assertOutputContains('Removed 1 slug(s).');

        $table = TableRegistry::getTableLocator()->get('ReservedSlugs.ReservedSlugs');
        $this->assertFalse($table->exists(['slug' => 'admin']));
    }

    public function testRemoveMultipleSlugs(): void
    {
        // Arrange
        // -----------------------------------------------
        $table = TableRegistry::getTableLocator()->get('ReservedSlugs.ReservedSlugs');
        $table->addSlug('to-remove-one');
        $table->addSlug('to-remove-two');

        // Act
        // -----------------------------------------------
        $this->exec('reserved_slugs remove to-remove-one,to-remove-two');

        // Assert
        // -----------------------------------------------
        $this->assertExitSuccess();
        $this->assertOutputContains('Removed: to-remove-one');
        $this->assertOutputContains('Removed: to-remove-two');
        $this->assertOutputContains('Removed 2 slug(s).');

        $this->assertFalse($table->exists(['slug' => 'to-remove-one']));
        $this->assertFalse($table->exists(['slug' => 'to-remove-two']));
    }

    public function testRemoveNonExistingSlug(): void
    {
        $this->exec('reserved_slugs remove non-existing');

        $this->assertExitSuccess();
        $this->assertErrorContains('Not found: non-existing');
        $this->assertOutputContains('Removed 0 slug(s).');
    }
}
