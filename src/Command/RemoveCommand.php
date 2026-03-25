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
        $parser
            ->setDescription('Remove one or more reserved slugs.')
            ->addArgument('slugs', [
                'help' => 'The slug(s) to remove. Use commas to separate multiple slugs.',
                'required' => true,
                'separator' => ',',
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

        /** @var list<string> $slugs */
        $slugs = $args->getArrayArgument('slugs') ?? [];
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
