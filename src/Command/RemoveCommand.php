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
 * Remove reserved slugs.
 */
class RemoveCommand extends Command
{
    /**
     * @inheritDoc
     */
    public static function defaultName(): string
    {
        return 'reserved_slugs remove';
    }

    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser->setDescription('Remove one or more reserved slugs.');

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
        $removed = 0;

        foreach ($slugs as $slug) {
            if ($table->removeSlug($slug)) {
                $io->success(sprintf('Removed: %s', $slug));
                $removed++;
            } else {
                $io->warning(sprintf('Not found: %s', $slug));
            }
        }

        $io->out(sprintf('Removed %d slug(s).', $removed));

        return static::CODE_SUCCESS;
    }
}
