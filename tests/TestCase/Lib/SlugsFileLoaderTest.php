<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace ReservedSlugs\Test\TestCase\Lib;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use InvalidArgumentException;
use ReservedSlugs\Lib\SlugsFileLoader;

class SlugsFileLoaderTest extends TestCase
{
    protected SlugsFileLoader $loader;

    public function setUp(): void
    {
        parent::setUp();
        $this->loader = new SlugsFileLoader();
    }

    public function tearDown(): void
    {
        Configure::delete('ReservedSlugs.syncFile');
        unset($this->loader);
        parent::tearDown();
    }

    public function testParse(): void
    {
        // Arrange
        $file = TMP . 'test_parse_slugs.txt';
        file_put_contents($file, implode("\n", [
            '# comment line',
            'admin',
            'api',
            '',
            '# another comment',
            'blog',
        ]));

        try {
            // Act
            $result = $this->loader->parse($file);

            // Assert
            $this->assertSame(['admin', 'api', 'blog'], $result);
        } finally {
            unlink($file);
        }
    }

    public function testParseEmptyFile(): void
    {
        // Arrange
        $file = TMP . 'test_parse_empty.txt';
        file_put_contents($file, '');

        try {
            // Act
            $result = $this->loader->parse($file);

            // Assert
            $this->assertSame([], $result);
        } finally {
            unlink($file);
        }
    }

    public function testGetDefaultSeedFile(): void
    {
        // Act
        $path = $this->loader->getDefaultSeedFile();

        // Assert
        $this->assertStringEndsWith('config' . DIRECTORY_SEPARATOR . 'reserved-slugs.txt', $path);
    }

    public function testResolveSeedFileWithConfiguredFile(): void
    {
        // Arrange
        $customFile = TMP . 'custom-reserved-slugs.txt';
        file_put_contents($customFile, "test-slug\n");
        Configure::write('ReservedSlugs.syncFile', $customFile);

        try {
            // Act
            $result = $this->loader->resolveSeedFile();

            // Assert
            $this->assertSame($customFile, $result);
        } finally {
            unlink($customFile);
        }
    }

    public function testResolveSeedFileWithNonExistentConfiguredFile(): void
    {
        // Arrange
        Configure::write('ReservedSlugs.syncFile', '/nonexistent/custom-slugs.txt');

        // Act
        $result = $this->loader->resolveSeedFile();

        // Assert
        $this->assertSame($this->loader->getDefaultSeedFile(), $result);
    }

    public function testResolveSeedFileWithAppConfigFile(): void
    {
        // Arrange
        $appConfigFile = CONFIG . 'reserved-slugs.txt';
        file_put_contents($appConfigFile, "app-slug\n");

        try {
            // Act
            $result = $this->loader->resolveSeedFile();

            // Assert
            $this->assertSame($appConfigFile, $result);
        } finally {
            unlink($appConfigFile);
        }
    }

    public function testResolveSeedFileWithoutAnyAppFile(): void
    {
        // Act
        $result = $this->loader->resolveSeedFile();

        // Assert
        $this->assertSame($this->loader->getDefaultSeedFile(), $result);
    }

    public function testResolveAndValidateWithExplicitFile(): void
    {
        // Arrange
        $file = TMP . 'test_resolve_validate.txt';
        file_put_contents($file, "slug\n");

        try {
            // Act
            $result = $this->loader->resolveAndValidate($file);

            // Assert
            $this->assertSame($file, $result);
        } finally {
            unlink($file);
        }
    }

    public function testResolveAndValidateWithNonExistentFile(): void
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('File not found');

        // Act
        $this->loader->resolveAndValidate('/nonexistent/file.txt');
    }

    public function testResolveAndValidateWithNullResolvesAutomatically(): void
    {
        // Arrange
        $appConfigFile = CONFIG . 'reserved-slugs.txt';
        file_put_contents($appConfigFile, "slug\n");

        try {
            // Act
            $result = $this->loader->resolveAndValidate(null);

            // Assert
            $this->assertSame($appConfigFile, $result);
        } finally {
            unlink($appConfigFile);
        }
    }
}
