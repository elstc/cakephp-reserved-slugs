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
use ReservedSlugs\Model\Entity\ReservedSlug;

/**
 * List reserved slugs.
 */
class ListCommand extends Command
{
    /**
     * @inheritDoc
     */
    public static function defaultName(): string
    {
        return 'reserved_slugs list';
    }

    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser
            ->setDescription('List all reserved slugs.')
            ->addOption('count', [
                'short' => 'c',
                'boolean' => true,
                'help' => 'Display only the count of reserved slugs.',
            ])
            ->addOption('search', [
                'short' => 's',
                'help' => 'Filter slugs by partial match.',
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

        $query = $table->find();

        $search = $args->getOption('search');
        if ($search && is_string($search)) {
            // Escape LIKE wildcards to treat user input as a literal substring.
            $escaped = addcslashes($search, '%_\\');
            $query->where(['slug LIKE' => '%' . $escaped . '%']);
        }

        if ($args->getOption('count')) {
            $io->out((string)$query->count());

            return static::CODE_SUCCESS;
        }

        $slugs = $query->orderBy(['slug' => 'ASC'])->all();

        if ($slugs->isEmpty()) {
            $io->out('No reserved slugs found.');

            return static::CODE_SUCCESS;
        }

        foreach ($slugs as $entity) {
            assert($entity instanceof ReservedSlug);
            $io->out($entity->slug);
        }

        $io->out('');
        $io->out(sprintf('Total: %d', $slugs->count()));

        return static::CODE_SUCCESS;
    }
}
