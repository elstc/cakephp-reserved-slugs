<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class CreateReservedSlugs extends AbstractMigration
{
    /**
     * Change Method.
     *
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('reserved_slugs', [
            'id' => false,
            'primary_key' => ['slug'],
        ]);
        $table->addColumn('slug', 'string', [
            'limit' => 128,
            'null' => false,
        ]);
        $table->addColumn('created_at', 'timestamp', [
            'default' => 'CURRENT_TIMESTAMP',
            'null' => false,
        ]);
        $table->create();
    }
}
