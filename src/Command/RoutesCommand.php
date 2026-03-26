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
use Elastic\SlugGuard\Lib\RoutePathCollector;
use function Cake\I18n\__d;

/**
 * Display first-level URL path segments extracted from application routes.
 *
 * Output is pipe-friendly (one path per line, no decoration) so it can
 * be combined with other commands:
 *
 * ```
 * bin/cake slug_guard routes | bin/cake slug_guard add
 * bin/cake slug_guard routes >> config/reserved-slugs.txt
 * ```
 */
class RoutesCommand extends Command
{
    /**
     * @inheritDoc
     */
    public static function defaultName(): string
    {
        return 'slug_guard routes';
    }

    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser
            ->setDescription(
                __d(
                    'elastic/slug_guard',
                    'List first-level URL path segments from application routes. '
                    . 'Output is pipe-friendly (one path per line).',
                ),
            )
            ->addOption('count', [
                'short' => 'c',
                'boolean' => true,
                'help' => __d('elastic/slug_guard', 'Display only the count of route paths.'),
            ]);

        return $parser;
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $collector = new RoutePathCollector();
        $paths = $collector->collect();

        if ($args->getOption('count')) {
            $io->out((string)count($paths));

            return static::CODE_SUCCESS;
        }

        if (count($paths) === 0) {
            // Use stderr so that stdout remains empty for pipe consumers.
            $io->err(__d('elastic/slug_guard', 'No route paths detected.'));

            return static::CODE_SUCCESS;
        }

        foreach ($paths as $path) {
            $io->out($path);
        }

        return static::CODE_SUCCESS;
    }
}
