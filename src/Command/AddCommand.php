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

/**
 * Add reserved slugs.
 *
 * Accepts slugs as command arguments or reads from stdin when piped:
 *
 * ```
 * bin/cake slug_guard add slug-one slug-two
 * bin/cake slug_guard routes | bin/cake slug_guard add
 * ```
 */
class AddCommand extends Command
{
    use StdinReaderTrait;

    /**
     * @inheritDoc
     */
    public static function defaultName(): string
    {
        return 'slug_guard add';
    }

    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser->setDescription(
            'Add one or more reserved slugs. '
            . 'Accepts slugs as arguments, or reads from stdin when piped.',
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
            $io->error('At least one slug is required. Provide arguments or pipe input via stdin.');

            return static::CODE_ERROR;
        }
        $added = 0;

        foreach ($slugs as $slug) {
            if ($table->addSlug($slug)) {
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
