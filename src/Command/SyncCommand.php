<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace ReservedSlugs\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use InvalidArgumentException;
use ReservedSlugs\Lib\SlugsSyncService;

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
        return 'reserved_slugs sync';
    }

    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser
            ->setDescription('Sync reserved slugs with a text file. Adds missing slugs and removes extra slugs.')
            ->addOption('file', [
                'short' => 'f',
                'help' => 'Path to the text file. '
                    . 'Defaults to app config or plugin built-in seed file.',
            ])
            ->addOption('dry-run', [
                'short' => 'd',
                'boolean' => true,
                'help' => 'Preview changes without applying them.',
            ]);

        return $parser;
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        /** @var \ReservedSlugs\Model\Table\ReservedSlugsTable $table */
        $table = $this->fetchTable('ReservedSlugs.ReservedSlugs');
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

        $io->success(sprintf('Sync complete. Added: %d, Removed: %d', $result['added'], $result['removed']));

        return static::CODE_SUCCESS;
    }

    /**
     * Preview sync changes without applying them.
     *
     * @param \ReservedSlugs\Lib\SlugsSyncService $service The sync service.
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
            $io->out('No changes needed.');

            return static::CODE_SUCCESS;
        }

        if (!empty($toAdd)) {
            $io->out(sprintf('Slugs to add (%d):', count($toAdd)));
            foreach ($toAdd as $slug) {
                $io->out(sprintf('  + %s', $slug));
            }
        }

        if (!empty($toRemove)) {
            $io->out(sprintf('Slugs to remove (%d):', count($toRemove)));
            foreach ($toRemove as $slug) {
                $io->out(sprintf('  - %s', $slug));
            }
        }

        return static::CODE_SUCCESS;
    }
}
