<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */

declare(strict_types=1);

namespace ReservedSlugs\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

class ReservedSlugsFixture extends TestFixture
{
    /**
     * @var string
     */
    public string $table = 'reserved_slugs';

    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'slug' => 'admin',
                'created_at' => '2026-01-01 00:00:00',
            ],
            [
                'slug' => 'api',
                'created_at' => '2026-01-01 00:00:00',
            ],
            [
                'slug' => 'blog',
                'created_at' => '2026-01-01 00:00:00',
            ],
            [
                'slug' => 'dashboard',
                'created_at' => '2026-01-01 00:00:00',
            ],
            [
                'slug' => 'help',
                'created_at' => '2026-01-01 00:00:00',
            ],
        ];
        parent::init();
    }
}
