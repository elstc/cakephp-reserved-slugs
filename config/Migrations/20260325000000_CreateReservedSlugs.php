<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

// cakephp/migrations 4.x uses Phinx, 5.x uses Migrations\BaseMigration
if (class_exists('Migrations\BaseMigration')) {
    class_alias('Migrations\BaseMigration', 'CreateReservedSlugsBase');
} else {
    class_alias('Phinx\Migration\AbstractMigration', 'CreateReservedSlugsBase');
}

class CreateReservedSlugs extends CreateReservedSlugsBase
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
