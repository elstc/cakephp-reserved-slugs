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
    }

    public function bootstrap(): void
    {
        $this->addPlugin(SlugGuardPlugin::class);
    }
}
