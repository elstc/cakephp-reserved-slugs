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
            ->setDescription(
                __d('elastic/slug_guard', 'Import reserved slugs from a text file. Existing slugs are preserved.'),
            )
            ->addArgument('file', [
                'help' => __d('elastic/slug_guard', 'Path to the text file containing slugs (one per line).'),
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

        $io->success(__d('elastic/slug_guard', 'Imported {0} new slug(s).', $added));

        return static::CODE_SUCCESS;
    }
}
