<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace ReservedSlugs\Test\TestCase\Validation;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReservedSlugs\Validation\SlugValidator;

class SlugValidatorTest extends TestCase
{
    #[DataProvider('validSlugsProvider')]
    public function testIsValidWithValidSlugs(string $slug): void
    {
        $this->assertTrue(SlugValidator::isValid($slug));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function validSlugsProvider(): array
    {
        return [
            'minimum length (4 chars)' => ['abcd'],
            'with hyphens' => ['my-slug'],
            'numbers only' => ['1234'],
            'alphanumeric' => ['abc123'],
            'complex slug' => ['my-blog-post-123'],
            'maximum length (24 chars)' => ['abcdefghijklmnopqrstuvwx'],
        ];
    }

    #[DataProvider('invalidSlugsProvider')]
    public function testIsValidWithInvalidSlugs(mixed $slug): void
    {
        $this->assertFalse(SlugValidator::isValid($slug));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function invalidSlugsProvider(): array
    {
        return [
            'too short (3 chars)' => ['abc'],
            'too long (25 chars)' => ['abcdefghijklmnopqrstuvwxy'],
            'starts with hyphen' => ['-slug'],
            'ends with hyphen' => ['slug-'],
            'uppercase letters' => ['SLUG'],
            'mixed case' => ['MySlug'],
            'contains spaces' => ['my slug'],
            'contains underscore' => ['my_slug'],
            'contains dot' => ['my.slug'],
            'contains at sign' => ['my@slug'],
            'empty string' => [''],
            'single char' => ['a'],
            'two chars' => ['ab'],
            'integer value' => [123],
            'null value' => [null],
            'boolean value' => [true],
        ];
    }

    /**
     * @return array<string, array{string, int, int, bool}>
     */
    public static function customLengthProvider(): array
    {
        return [
            'min length boundary - valid' => ['ab', 2, 24, true],
            'min length boundary - too short' => ['a', 2, 24, false],
            'max length boundary - valid' => ['abcde', 4, 5, true],
            'max length boundary - too long' => ['abcdef', 4, 5, false],
        ];
    }

    #[DataProvider('customLengthProvider')]
    public function testIsValidWithCustomLength(string $slug, int $minLength, int $maxLength, bool $expected): void
    {
        $this->assertSame($expected, SlugValidator::isValid($slug, minLength: $minLength, maxLength: $maxLength));
    }
}
