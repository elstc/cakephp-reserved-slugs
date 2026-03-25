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
        $this->exec('slug_guard list');

        $this->assertExitSuccess();
        $this->assertOutputContains('admin');
        $this->assertOutputContains('api');
        $this->assertOutputContains('Total: 5');
    }

    public function testListWithCount(): void
    {
        $this->exec('slug_guard list --count');

        $this->assertExitSuccess();
        $this->assertOutputContains('5');
    }

    public function testListWithSearch(): void
    {
        $this->exec('slug_guard list --search bl');

        $this->assertExitSuccess();
        $this->assertOutputContains('blog');
        $this->assertOutputNotContains('admin');
    }

    public function testListWithSearchNoResults(): void
    {
        $this->exec('slug_guard list --search zzzzz');

        $this->assertExitSuccess();
        $this->assertOutputContains('No reserved slugs found.');
    }
}
