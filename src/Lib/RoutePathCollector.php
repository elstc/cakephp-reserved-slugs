<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace Elastic\SlugGuard\Lib;

use Cake\Routing\Router;

/**
 * Collects first-level URL path segments from CakePHP routes.
 *
 * Useful for detecting slugs that would conflict with application routes
 * (e.g. preventing a user from choosing "admin" as their username when
 * /admin/* routes exist).
 */
class RoutePathCollector
{
    /**
     * Collect first-level URL segments from all registered routes.
     *
     * @return list<string> Unique, sorted first-level path segments.
     */
    public function collect(): array
    {
        $segments = [];
        foreach (Router::routes() as $route) {
            $segment = $this->extractFirstSegment($route->template);
            if ($segment !== null) {
                $segments[$segment] = true;
            }
        }

        $result = array_keys($segments);
        sort($result);

        return $result;
    }

    /**
     * Extract the first static path segment from a route template.
     *
     * Returns null for root routes ("/") and routes whose first segment
     * is dynamic (e.g. "/{username}").
     *
     * @param string $template Route template string (e.g. "/admin/users/{id}").
     * @return string|null The first segment, or null if not extractable.
     */
    protected function extractFirstSegment(string $template): ?string
    {
        $path = ltrim($template, '/');
        if ($path === '') {
            return null;
        }

        $firstSegment = explode('/', $path, 2)[0];

        // Skip dynamic segments like {id} or :slug
        if (str_starts_with($firstSegment, '{') || str_starts_with($firstSegment, ':')) {
            return null;
        }

        return $firstSegment;
    }
}
