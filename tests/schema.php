<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

return [
    [
        'table' => 'reserved_slugs',
        'columns' => [
            'slug' => [
                'type' => 'string',
                'length' => 128,
                'null' => false,
            ],
            'created_at' => [
                'type' => 'timestampfractional',
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP',
            ],
        ],
        'constraints' => [
            'primary' => [
                'type' => 'primary',
                'columns' => ['slug'],
            ],
        ],
    ],
];
