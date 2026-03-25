<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace Elastic\SlugGuard\Model\Entity;

use Cake\ORM\Entity;

/**
 * ReservedSlug Entity
 *
 * @property string $slug
 * @property \Cake\I18n\DateTime $created_at
 */
class ReservedSlug extends Entity
{
    /**
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'slug' => true,
        'created_at' => false,
    ];
}
