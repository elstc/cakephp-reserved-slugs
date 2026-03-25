<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace Elastic\SlugGuard\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

class RemoveCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * @var list<string>
     */
    protected array $fixtures = [
        'plugin.Elastic/SlugGuard.ReservedSlugs',
    ];

    public function testRemoveExistingSlug(): void
    {
        $this->exec('slug_guard remove admin');

        $this->assertExitSuccess();
        $this->assertOutputContains('Removed: admin');
        $this->assertOutputContains('Removed 1 slug(s).');

        $table = $this->fetchTable('Elastic/SlugGuard.ReservedSlugs');
        $this->assertFalse($table->exists(['slug' => 'admin']));
    }

    public function testRemoveMultipleSlugs(): void
    {
        // Arrange
        // -----------------------------------------------
        $table = $this->fetchTable('Elastic/SlugGuard.ReservedSlugs');
        $table->addSlug('to-remove-one');
        $table->addSlug('to-remove-two');

        // Act
        // -----------------------------------------------
        $this->exec('slug_guard remove to-remove-one to-remove-two');

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
        $this->exec('slug_guard remove non-existing');

        $this->assertExitSuccess();
        $this->assertErrorContains('Not found: non-existing');
        $this->assertOutputContains('Removed 0 slug(s).');
    }
}
