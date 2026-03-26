<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace TestApp;

use Cake\Http\BaseApplication;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\RouteBuilder;
use Elastic\SlugGuard\SlugGuardPlugin;

class Application extends BaseApplication
{
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        return $middlewareQueue;
    }

    public function routes(RouteBuilder $routes): void
    {
        $routes->connect('/admin/dashboard', ['controller' => 'Admin', 'action' => 'dashboard']);
        $routes->connect('/api/users', ['controller' => 'ApiUsers', 'action' => 'index']);
        $routes->connect('/posts/{id}', ['controller' => 'Posts', 'action' => 'view']);
        $routes->connect('/{username}', ['controller' => 'Users', 'action' => 'profile']);
    }

    public function bootstrap(): void
    {
        $this->addPlugin(SlugGuardPlugin::class);
    }
}
