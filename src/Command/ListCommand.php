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
use Elastic\SlugGuard\Model\Entity\ReservedSlug;
use function Cake\I18n\__d;

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
        return 'slug_guard list';
    }

    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser
            ->setDescription(__d('elastic/slug_guard', 'List all reserved slugs.'))
            ->addOption('count', [
                'short' => 'c',
                'boolean' => true,
                'help' => __d('elastic/slug_guard', 'Display only the count of reserved slugs.'),
            ])
            ->addOption('search', [
                'short' => 's',
                'help' => __d('elastic/slug_guard', 'Filter slugs by partial match.'),
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
            $io->out(__d('elastic/slug_guard', 'No reserved slugs found.'));

            return static::CODE_SUCCESS;
        }

        foreach ($slugs as $entity) {
            assert($entity instanceof ReservedSlug);
            $io->out($entity->slug);
        }

        $io->out('');
        $io->out(__d('elastic/slug_guard', 'Total: {0}', $slugs->count()));

        return static::CODE_SUCCESS;
    }
}
