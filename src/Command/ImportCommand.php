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

/**
 * Import reserved slugs from a text file.
 */
class ImportCommand extends Command
{
    /**
     * @inheritDoc
     */
    public static function defaultName(): string
    {
        return 'slug_guard import';
    }

    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser
            ->setDescription('Import reserved slugs from a text file. Existing slugs are preserved.')
            ->addArgument('file', [
                'help' => 'Path to the text file containing slugs (one per line).',
                'required' => true,
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

        /** @var string $file */
        $file = $args->getArgument('file');

        try {
            $added = $service->importFromFile($file);
        } catch (InvalidArgumentException $e) {
            $io->error($e->getMessage());

            return static::CODE_ERROR;
        }

        $io->success(sprintf('Imported %d new slug(s).', $added));

        return static::CODE_SUCCESS;
    }
}
