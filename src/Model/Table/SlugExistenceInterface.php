<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace Elastic\SlugGuard\Model\Table;

/**
 * Interface for tables that can check whether a slug is reserved.
 *
 * Implement this interface on any CakePHP Table class to use it
 * as a custom slug existence backend for the IsNotReservedSlug rule.
 */
interface SlugExistenceInterface
{
    /**
     * Check if a slug exists in the reserved list.
     *
     * @param string $slug The slug to check.
     * @return bool True if the slug exists (is reserved), false otherwise.
     */
    public function slugExists(string $slug): bool;
}
