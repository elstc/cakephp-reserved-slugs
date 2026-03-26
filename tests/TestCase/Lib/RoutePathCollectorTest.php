<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace Elastic\SlugGuard\Test\TestCase\Lib;

use Cake\Routing\Router;
use Cake\TestSuite\TestCase;
use Elastic\SlugGuard\Lib\RoutePathCollector;

class RoutePathCollectorTest extends TestCase
{
    private RoutePathCollector $collector;

    public function setUp(): void
    {
        parent::setUp();
        $this->collector = new RoutePathCollector();
    }

    /**
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        Router::reload();
    }

    public function testCollect_withStaticRoutes_returnsFirstSegments(): void
    {
        // Arrange
        $this->buildRoute([
            '/admin/dashboard' => ['controller' => 'Admin', 'action' => 'dashboard'],
            '/api/users' => ['controller' => 'ApiUsers', 'action' => 'index'],
        ]);

        // Act
        $result = $this->collector->collect();

        // Assert
        $this->assertSame(['admin', 'api'], $result);
    }

    public function testCollect_withDynamicFirstSegment_skipsRoute(): void
    {
        // Arrange
        $this->buildRoute([
            '/{username}' => ['controller' => 'Users', 'action' => 'profile'],
        ]);

        // Act
        $result = $this->collector->collect();

        // Assert
        $this->assertSame([], $result);
    }

    public function testCollect_withRootRoute_skipsRoute(): void
    {
        // Arrange
        $this->buildRoute([
            '/' => ['controller' => 'Pages', 'action' => 'home'],
        ]);

        // Act
        $result = $this->collector->collect();

        // Assert
        $this->assertSame([], $result);
    }

    public function testCollect_withDuplicateFirstSegments_returnsUnique(): void
    {
        // Arrange
        $this->buildRoute([
            '/posts/new' => ['controller' => 'Posts', 'action' => 'add'],
            '/posts/{id}' => ['controller' => 'Posts', 'action' => 'view'],
        ]);

        // Act
        $result = $this->collector->collect();

        // Assert
        $this->assertSame(['posts'], $result);
    }

    public function testCollect_withEmptyCollection_returnsEmptyArray(): void
    {
        // Arrange
        Router::resetRoutes();

        // Act
        $result = $this->collector->collect();

        // Assert
        $this->assertSame([], $result);
    }

    public function testCollect_withMixedRoutes_returnsOnlyStaticFirstSegments(): void
    {
        // Arrange
        $this->buildRoute([
            '/' => ['controller' => 'Pages', 'action' => 'home'],
            '/admin/users' => ['controller' => 'Admin', 'action' => 'users'],
            '/api/v1/posts' => ['controller' => 'Api', 'action' => 'posts'],
            '/posts/{id}' => ['controller' => 'Posts', 'action' => 'view'],
            '/{username}' => ['controller' => 'Users', 'action' => 'profile'],
            '/blog' => ['controller' => 'Blog', 'action' => 'index'],
        ]);

        // Act
        $result = $this->collector->collect();

        // Assert
        $this->assertSame(['admin', 'api', 'blog', 'posts'], $result);
    }

    public function testCollect_returnsSortedResults(): void
    {
        // Arrange
        $this->buildRoute([
            '/zebra' => ['controller' => 'Zebra', 'action' => 'index'],
            '/alpha' => ['controller' => 'Alpha', 'action' => 'index'],
            '/middle' => ['controller' => 'Middle', 'action' => 'index'],
        ]);

        // Act
        $result = $this->collector->collect();

        // Assert
        $this->assertSame(['alpha', 'middle', 'zebra'], $result);
    }

    /**
     * Build a Route from an array of template => defaults.
     *
     * @param array<string, array<string, string>> $routes
     * @return void
     */
    private function buildRoute(array $routes): void
    {
        $builder = Router::createRouteBuilder('/');
        foreach ($routes as $template => $defaults) {
            $builder->connect($template, $defaults);
        }
    }
}
