<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace Elastic\SlugGuard\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * Tests rely on routes defined in tests/test_app/TestApp/Application::routes().
 *
 * Registered routes: /admin/dashboard, /api/users, /posts/{id}, /{username}
 * Expected collected paths: admin, api, posts (/{username} is dynamic, skipped)
 */
class RoutesCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    public function testExecute_displaysRoutePaths(): void
    {
        // Act
        $this->exec('slug_guard routes');

        // Assert
        $this->assertExitSuccess();
        $this->assertOutputContains('admin');
        $this->assertOutputContains('api');
        $this->assertOutputContains('posts');
        $this->assertOutputNotContains('Total');
    }

    public function testExecute_withCountOption(): void
    {
        // Act
        $this->exec('slug_guard routes --count');

        // Assert
        $this->assertExitSuccess();
        $this->assertOutputContains('3');
    }

    public function testExecute_outputIsPipeFriendly(): void
    {
        // Act
        $this->exec('slug_guard routes');

        // Assert — output contains only path segments, no decoration
        $this->assertExitSuccess();
        $this->assertOutputNotContains('Total');
        $this->assertOutputNotContains('detected');
    }
}
