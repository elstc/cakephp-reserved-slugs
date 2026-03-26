<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace Elastic\SlugGuard\Command;

use Cake\Console\ConsoleInput;

/**
 * Provides stdin reading capability for commands that accept piped input.
 *
 * Reads one item per line; blank lines and `#` comment lines are skipped.
 * Uses CakePHP's ConsoleInput for stdin access.
 */
trait StdinReaderTrait
{
    /**
     * Get the ConsoleInput instance for reading stdin.
     *
     * Override in subclasses or tests to inject a custom ConsoleInput.
     *
     * @return \Cake\Console\ConsoleInput
     */
    protected function getStdinInput(): ConsoleInput
    {
        return new ConsoleInput();
    }

    /**
     * Read lines from stdin for pipe support.
     *
     * Only reads when stdin has data available (i.e. data is being piped).
     * Blank lines and lines starting with `#` are skipped.
     *
     * @return list<string>
     */
    protected function readFromStdin(): array
    {
        $input = $this->getStdinInput();

        if (!$input->dataAvailable()) {
            return [];
        }

        $lines = [];
        while (($line = $input->read()) !== null) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            $lines[] = $line;
        }

        return $lines;
    }
}
