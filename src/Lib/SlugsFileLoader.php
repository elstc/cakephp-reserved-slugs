<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace Elastic\SlugGuard\Lib;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use InvalidArgumentException;
use RuntimeException;
use function Cake\I18n\__d;

/**
 * Handles slugs file parsing and path resolution.
 */
class SlugsFileLoader
{
    /**
     * Maximum allowed file size in bytes (1 MB).
     */
    private const MAX_FILE_SIZE = 1 * 1024 * 1024;

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
        $size = filesize($filePath);
        if ($size === false || $size > self::MAX_FILE_SIZE) {
            throw new RuntimeException(
                __d('elastic/slug_guard', 'File too large or unreadable: {0}', $filePath),
            );
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            throw new RuntimeException(__d('elastic/slug_guard', 'Failed to read file: {0}', $filePath));
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
        return Plugin::configPath('Elastic/SlugGuard') . 'reserved-slugs.txt';
    }

    /**
     * Resolve the seed file path using a priority-based lookup.
     *
     * Priority:
     * 1. Application config (Configure key: `SlugGuard.syncFile`, default: CONFIG/reserved-slugs.txt)
     * 2. Plugin built-in seed file
     *
     * @return string First existing file path.
     */
    public function resolveSeedFile(): string
    {
        $appFile = Configure::read('SlugGuard.syncFile', CONFIG . 'reserved-slugs.txt');
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
            throw new InvalidArgumentException(__d('elastic/slug_guard', 'File not found: {0}', $filePath));
        }

        return $filePath;
    }
}
