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
use function Cake\I18n\__d;

/**
 * Remove reserved slugs.
 *
 * Accepts slugs as command arguments or reads from stdin when piped:
 *
 * ```
 * bin/cake slug_guard remove slug-one slug-two
 * cat slugs-to-remove.txt | bin/cake slug_guard remove
 * ```
 */
class RemoveCommand extends Command
{
    use StdinReaderTrait;

    /**
     * @inheritDoc
     */
    public static function defaultName(): string
    {
        return 'slug_guard remove';
    }

    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser->setDescription(
            __d(
                'elastic/slug_guard',
                'Remove one or more reserved slugs. '
                . 'Accepts slugs as arguments, or reads from stdin when piped.',
            ),
        );

        return $parser;
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        /** @var \Elastic\SlugGuard\Model\Table\ReservedSlugsTable $table */
        $table = $this->fetchTable('Elastic/SlugGuard.ReservedSlugs');

        /** @var list<string> $slugs */
        $slugs = $args->getArguments();
        if (count($slugs) === 0) {
            $slugs = $this->readFromStdin();
        }
        if (count($slugs) === 0) {
            $io->error(
                __d('elastic/slug_guard', 'At least one slug is required. Provide arguments or pipe input via stdin.'),
            );

            return static::CODE_ERROR;
        }
        $removed = 0;

        foreach ($slugs as $slug) {
            if ($table->removeSlug($slug)) {
                $io->success(__d('elastic/slug_guard', 'Removed: {0}', $slug));
                $removed++;
            } else {
                $io->warning(__d('elastic/slug_guard', 'Not found: {0}', $slug));
            }
        }

        $io->out(__d('elastic/slug_guard', 'Removed {0} slug(s).', $removed));

        return static::CODE_SUCCESS;
    }
}
