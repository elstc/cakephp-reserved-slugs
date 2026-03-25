<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace Elastic\SlugGuard\Lib;

use Elastic\SlugGuard\Model\Table\ReservedSlugsTable;

/**
 * Handles file-based import and sync operations for reserved slugs.
 */
class SlugsSyncService
{
    private SlugsFileLoader $loader;

    /**
     * @param \Elastic\SlugGuard\Model\Table\ReservedSlugsTable $table The reserved slugs table.
     * @param \Elastic\SlugGuard\Lib\SlugsFileLoader|null $loader File loader instance. Defaults to a new SlugsFileLoader.
     */
    public function __construct(
        private ReservedSlugsTable $table,
        ?SlugsFileLoader $loader = null,
    ) {
        $this->loader = $loader ?? new SlugsFileLoader();
    }

    /**
     * Import slugs from a text file.
     *
     * File format: one slug per line, lines starting with `#` are comments, empty lines are ignored.
     *
     * @param string $filePath Path to the text file.
     * @return int Number of slugs added.
     * @throws \InvalidArgumentException If the file does not exist.
     */
    public function importFromFile(string $filePath): int
    {
        $this->loader->resolveAndValidate($filePath);

        $slugs = $this->loader->parse($filePath);
        if (empty($slugs)) {
            return 0;
        }

        // Batch-fetch existing slugs to avoid N+1 queries when importing large files.
        $existingSlugs = $this->table->find()
            ->select(['slug'])
            ->where(['slug IN' => $slugs])
            ->all()
            ->extract('slug')
            ->toArray();

        $toAdd = array_diff($slugs, $existingSlugs);
        if (empty($toAdd)) {
            return 0;
        }

        $entities = $this->table->newEntities(
            array_map(fn(string $slug) => ['slug' => $slug], array_values($toAdd)),
        );
        $saved = $this->table->saveMany($entities);

        return $saved !== false ? count(iterator_to_array($saved)) : 0;
    }

    /**
     * Calculate the diff between a file and the current DB state.
     *
     * @param string|null $filePath Path to the text file. Null uses default seed file.
     * @return array{filePath: string, toAdd: list<string>, toRemove: list<string>}
     * @throws \InvalidArgumentException If the file does not exist.
     */
    public function calculateDiff(?string $filePath = null): array
    {
        $filePath = $this->loader->resolveAndValidate($filePath);

        $fileSlugs = $this->loader->parse($filePath);
        $existingSlugs = $this->table->find()
            ->select(['slug'])
            ->all()
            ->extract('slug')
            ->toArray();

        return [
            'filePath' => $filePath,
            'toAdd' => array_values(array_diff($fileSlugs, $existingSlugs)),
            'toRemove' => array_values(array_diff($existingSlugs, $fileSlugs)),
        ];
    }

    /**
     * Sync the reserved slugs table with a text file.
     *
     * Adds slugs that are in the file but not in the table, and removes slugs
     * that are in the table but not in the file.
     *
     * @param string|null $filePath Path to the text file. If null, uses the default seed file.
     * @return array{added: int, removed: int}
     * @throws \InvalidArgumentException If the file does not exist.
     */
    public function syncFromFile(?string $filePath = null): array
    {
        $diff = $this->calculateDiff($filePath);

        $added = 0;
        $removed = 0;

        $this->table->getConnection()->transactional(
            function () use ($diff, &$added, &$removed): void {
                if (!empty($diff['toAdd'])) {
                    $entities = $this->table->newEntities(
                        array_map(fn(string $slug) => ['slug' => $slug], $diff['toAdd']),
                    );
                    $saved = $this->table->saveMany($entities);
                    $added = $saved !== false ? count(iterator_to_array($saved)) : 0;
                }

                if (!empty($diff['toRemove'])) {
                    $removed = $this->table->deleteAll(['slug IN' => $diff['toRemove']]);
                }
            },
        );

        return ['added' => $added, 'removed' => $removed];
    }
}
