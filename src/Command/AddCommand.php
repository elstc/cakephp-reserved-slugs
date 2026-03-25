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

/**
 * Add reserved slugs.
 */
class AddCommand extends Command
{
    /**
     * @inheritDoc
     */
    public static function defaultName(): string
    {
        return 'reserved_slugs add';
    }

    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser->setDescription('Add one or more reserved slugs.');

        return $parser;
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        /** @var \ReservedSlugs\Model\Table\ReservedSlugsTable $table */
        $table = $this->fetchTable('ReservedSlugs.ReservedSlugs');

        /** @var list<string> $slugs */
        $slugs = $args->getArguments();
        if (count($slugs) === 0) {
            $io->error('At least one slug is required.');

            return static::CODE_ERROR;
        }
        $added = 0;

        foreach ($slugs as $slug) {
            $result = $table->addSlug($slug);
            if ($result !== false) {
                $io->success(sprintf('Added: %s', $slug));
                $added++;
            } else {
                $io->warning(sprintf('Already exists: %s', $slug));
            }
        }

        $io->out(sprintf('Added %d slug(s).', $added));

        return static::CODE_SUCCESS;
    }
}
