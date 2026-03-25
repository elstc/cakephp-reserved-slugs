<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace ReservedSlugs\Model\Rule;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * Application rule that prevents slugs from conflicting with system URLs.
 *
 * Compares the entity's slug against a list of reserved strings to ensure
 * it does not collide with existing system routes or well-known paths.
 *
 * NOTE: This rule skips the check for empty/null values — it always returns
 * true in that case. Callers must add a `notEmptyString` (or equivalent)
 * validation rule to the slug field to prevent empty slugs from passing.
 *
 * Usage in buildRules():
 *
 * ```php
 * $rules->add(new IsNotReservedSlug('slug'), 'reservedSlug', [
 *     'errorField' => 'slug',
 *     'message' => 'This slug is reserved.',
 * ]);
 * ```
 */
class IsNotReservedSlug
{
    use LocatorAwareTrait;

    /**
     * @var string
     */
    protected string $slugField;

    /**
     * @var string
     */
    protected string $tableName;

    /**
     * Constructor.
     *
     * @param string $slugField The entity field containing the slug.
     * @param string $tableName The table registry alias for the reserved slugs table.
     */
    public function __construct(
        string $slugField = 'slug',
        string $tableName = 'ReservedSlugs.ReservedSlugs',
    ) {
        $this->slugField = $slugField;
        $this->tableName = $tableName;
    }

    /**
     * Check if the entity's slug is not reserved.
     *
     * @param \Cake\Datasource\EntityInterface $entity The entity to check.
     * @param array<string, mixed> $options Rule options.
     * @return bool True if the slug is not reserved, false otherwise.
     */
    public function __invoke(EntityInterface $entity, array $options): bool
    {
        $slug = $entity->get($this->slugField);
        // Skip check for empty values; presence validation should be handled
        // separately by the model's validation rules.
        if ($slug === null || $slug === '') {
            return true;
        }

        /** @var \ReservedSlugs\Model\Table\ReservedSlugsTable $table */
        $table = $this->fetchTable($this->tableName);

        return !$table->slugExists((string)$slug);
    }
}
