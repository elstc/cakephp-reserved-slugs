<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace Elastic\SlugGuard\Model\Rule;

use Cake\Datasource\EntityInterface;
use Elastic\SlugGuard\Lib\RoutePathCollector;

/**
 * Application rule that prevents slugs from conflicting with application routes.
 *
 * Compares the entity's slug against the first-level path segments extracted
 * from the CakePHP route collection.
 *
 * NOTE: This rule skips the check for empty/null values — it always returns
 * true in that case. Callers must add a `notEmptyString` (or equivalent)
 * validation rule to the slug field to prevent empty slugs from passing.
 *
 * Usage in buildRules():
 *
 * ```php
 * $rules->add(new IsNotRouteConflict('slug'), 'routeConflict', [
 *     'errorField' => 'slug',
 *     'message' => 'This slug conflicts with an application route.',
 * ]);
 * ```
 */
class IsNotRouteConflict
{
    /**
     * @var string
     */
    protected string $slugField;

    /**
     * @var \Elastic\SlugGuard\Lib\RoutePathCollector
     */
    protected RoutePathCollector $collector;

    /**
     * Cached route paths. Routes do not change during a request.
     *
     * @var list<string>|null
     */
    private ?array $cachedPaths = null;

    /**
     * Constructor.
     *
     * @param string $slugField The entity field containing the slug.
     * @param \Elastic\SlugGuard\Lib\RoutePathCollector|null $collector Route path collector instance.
     */
    public function __construct(
        string $slugField = 'slug',
        ?RoutePathCollector $collector = null,
    ) {
        $this->slugField = $slugField;
        $this->collector = $collector ?? new RoutePathCollector();
    }

    /**
     * Check if the entity's slug does not conflict with any application route.
     *
     * @param \Cake\Datasource\EntityInterface $entity The entity to check.
     * @param array<string, mixed> $options Rule options.
     * @return bool True if the slug does not conflict, false otherwise.
     */
    public function __invoke(EntityInterface $entity, array $options): bool
    {
        $slug = $entity->get($this->slugField);
        // Skip check for empty values; presence validation should be handled
        // separately by the model's validation rules.
        if ($slug === null || $slug === '') {
            return true;
        }

        $this->cachedPaths ??= $this->collector->collect();

        return !in_array((string)$slug, $this->cachedPaths, true);
    }
}
