<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace ReservedSlugs\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class AddCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * @var list<string>
     */
    protected array $fixtures = [
        'plugin.ReservedSlugs.ReservedSlugs',
    ];

    public function testAddNewSlug(): void
    {
        $this->exec('reserved_slugs add new-slug');

        $this->assertExitSuccess();
        $this->assertOutputContains('Added: new-slug');
        $this->assertOutputContains('Added 1 slug(s).');

        $table = TableRegistry::getTableLocator()->get('ReservedSlugs.ReservedSlugs');
        $this->assertTrue($table->exists(['slug' => 'new-slug']));
    }

    public function testAddMultipleSlugs(): void
    {
        // Act
        // -----------------------------------------------
        $this->exec('reserved_slugs add new-slug-one new-slug-two');

        // Assert
        // -----------------------------------------------
        $this->assertExitSuccess();
        $this->assertOutputContains('Added: new-slug-one');
        $this->assertOutputContains('Added: new-slug-two');
        $this->assertOutputContains('Added 2 slug(s).');

        $table = TableRegistry::getTableLocator()->get('ReservedSlugs.ReservedSlugs');
        $this->assertTrue($table->exists(['slug' => 'new-slug-one']));
        $this->assertTrue($table->exists(['slug' => 'new-slug-two']));
    }

    public function testAddDuplicateSlug(): void
    {
        $this->exec('reserved_slugs add admin');

        $this->assertExitSuccess();
        $this->assertErrorContains('Already exists: admin');
        $this->assertOutputContains('Added 0 slug(s).');
    }
}
