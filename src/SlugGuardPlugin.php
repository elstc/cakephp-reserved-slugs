<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace Elastic\SlugGuard;

use Cake\Core\BasePlugin;

class SlugGuardPlugin extends BasePlugin
{
    // This plugin only provides CLI commands and ORM components.
    // No bootstrap, middleware, or route hooks are needed.

    /**
     * @var bool
     */
    protected bool $bootstrapEnabled = false;

    /**
     * @var bool
     */
    protected bool $middlewareEnabled = false;

    /**
     * @var bool
     */
    protected bool $routesEnabled = false;

    /**
     * @var bool
     */
    protected bool $consoleEnabled = true;
}
