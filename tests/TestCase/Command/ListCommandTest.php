<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace Elastic\SlugGuard\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

class ListCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * @var list<string>
     */
    protected array $fixtures = [
        'plugin.Elastic/SlugGuard.ReservedSlugs',
    ];

    public function testListAllSlugs(): void
    {
        // Act
        // -----------------------------------------------
        $this->exec('slug_guard list');

        // Assert
        // -----------------------------------------------
        $this->assertExitSuccess();
        $this->assertOutputContains('admin');
        $this->assertOutputContains('api');
        $this->assertOutputContains('Total: 5');
    }

    public function testListWithCount(): void
    {
        // Act
        // -----------------------------------------------
        $this->exec('slug_guard list --count');

        // Assert
        // -----------------------------------------------
        $this->assertExitSuccess();
        $this->assertOutputContains('5');
    }

    public function testListWithSearch(): void
    {
        // Act
        // -----------------------------------------------
        $this->exec('slug_guard list --search bl');

        // Assert
        // -----------------------------------------------
        $this->assertExitSuccess();
        $this->assertOutputContains('blog');
        $this->assertOutputNotContains('admin');
    }

    public function testListWithSearchNoResults(): void
    {
        // Act
        // -----------------------------------------------
        $this->exec('slug_guard list --search zzzzz');

        // Assert
        // -----------------------------------------------
        $this->assertExitSuccess();
        $this->assertOutputContains('No reserved slugs found.');
    }
}
