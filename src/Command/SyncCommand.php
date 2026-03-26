<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace Elastic\SlugGuard\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Elastic\SlugGuard\Lib\SlugsSyncService;
use InvalidArgumentException;
use function Cake\I18n\__d;

/**
 * Sync reserved slugs with a text file.
 */
class SyncCommand extends Command
{
    /**
     * @inheritDoc
     */
    public static function defaultName(): string
    {
        return 'slug_guard sync';
    }

    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser
            ->setDescription(
                __d(
                    'elastic/slug_guard',
                    'Sync reserved slugs with a text file. Adds missing slugs and removes extra slugs.',
                ),
            )
            ->addOption('file', [
                'short' => 'f',
                'help' => __d(
                    'elastic/slug_guard',
                    'Path to the text file. Defaults to app config or plugin built-in seed file.',
                ),
            ])
            ->addOption('dry-run', [
                'short' => 'd',
                'boolean' => true,
                'help' => __d('elastic/slug_guard', 'Preview changes without applying them.'),
            ]);

        return $parser;
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        /** @var \Elastic\SlugGuard\Model\Table\ReservedSlugsTable $table */
        $table = $this->fetchTable('Elastic/SlugGuard.ReservedSlugs');
        $service = new SlugsSyncService($table);

        $file = $args->getOption('file');
        $filePath = is_string($file) ? $file : null;

        if ($args->getOption('dry-run')) {
            return $this->dryRun($service, $filePath, $io);
        }

        try {
            $result = $service->syncFromFile($filePath);
        } catch (InvalidArgumentException $e) {
            $io->error($e->getMessage());

            return static::CODE_ERROR;
        }

        $io->success(
            __d('elastic/slug_guard', 'Sync complete. Added: {0}, Removed: {1}', $result['added'], $result['removed']),
        );

        return static::CODE_SUCCESS;
    }

    /**
     * Preview sync changes without applying them.
     *
     * @param \Elastic\SlugGuard\Lib\SlugsSyncService $service The sync service.
     * @param string|null $filePath The file path.
     * @param \Cake\Console\ConsoleIo $io The console IO.
     * @return int
     */
    protected function dryRun(
        SlugsSyncService $service,
        ?string $filePath,
        ConsoleIo $io,
    ): int {
        try {
            $diff = $service->calculateDiff($filePath);
        } catch (InvalidArgumentException $e) {
            $io->error($e->getMessage());

            return static::CODE_ERROR;
        }

        $toAdd = $diff['toAdd'];
        $toRemove = $diff['toRemove'];

        if (empty($toAdd) && empty($toRemove)) {
            $io->out(__d('elastic/slug_guard', 'No changes needed.'));

            return static::CODE_SUCCESS;
        }

        if (!empty($toAdd)) {
            $io->out(__d('elastic/slug_guard', 'Slugs to add ({0}):', count($toAdd)));
            foreach ($toAdd as $slug) {
                $io->out(__d('elastic/slug_guard', '  + {0}', $slug));
            }
        }

        if (!empty($toRemove)) {
            $io->out(__d('elastic/slug_guard', 'Slugs to remove ({0}):', count($toRemove)));
            foreach ($toRemove as $slug) {
                $io->out(__d('elastic/slug_guard', '  - {0}', $slug));
            }
        }

        return static::CODE_SUCCESS;
    }
}
