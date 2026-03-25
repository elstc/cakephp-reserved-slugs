<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace ReservedSlugs\Lib;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use InvalidArgumentException;

/**
 * Handles slugs file parsing and path resolution.
 */
class SlugsFileLoader
{
    /**
     * Parse a slugs text file.
     *
     * File format: one slug per line, `#` comments, empty lines ignored.
     *
     * @param string $filePath Path to the text file.
     * @return list<string>
     */
    public function parse(string $filePath): array
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return [];
        }

        $slugs = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }
            $slugs[] = $line;
        }

        return $slugs;
    }

    /**
     * Get the plugin's built-in seed file path.
     *
     * @return string
     */
    public function getDefaultSeedFile(): string
    {
        return Plugin::configPath('ReservedSlugs') . 'reserved-slugs.txt';
    }

    /**
     * Resolve the seed file path using a priority-based lookup.
     *
     * Priority:
     * 1. Application config (Configure key: `ReservedSlugs.syncFile`, default: CONFIG/reserved-slugs.txt)
     * 2. Plugin built-in seed file
     *
     * @return string First existing file path.
     */
    public function resolveSeedFile(): string
    {
        $appFile = Configure::read('ReservedSlugs.syncFile', CONFIG . 'reserved-slugs.txt');
        if (is_file($appFile)) {
            return $appFile;
        }

        return $this->getDefaultSeedFile();
    }

    /**
     * Resolve and validate a file path for use.
     *
     * @param string|null $filePath Explicit path, or null to resolve automatically.
     * @return string Validated file path.
     * @throws \InvalidArgumentException If the resolved file does not exist.
     */
    public function resolveAndValidate(?string $filePath = null): string
    {
        $filePath ??= $this->resolveSeedFile();
        if (!is_file($filePath)) {
            throw new InvalidArgumentException(sprintf('File not found: %s', $filePath));
        }

        return $filePath;
    }
}
