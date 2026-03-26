<?php
/*
 * Copyright 2026 ELASTIC Consultants Inc.
 */
declare(strict_types=1);

namespace Elastic\SlugGuard\Validation;

use InvalidArgumentException;
use function Cake\I18n\__d;

/**
 * Slug format validator for URL-safe strings.
 *
 * Ensures that a slug can be safely used as a subdomain label or URL path segment
 * by enforcing the following format constraints:
 * - Starts and ends with an alphanumeric character
 * - Contains only lowercase alphanumeric characters and hyphens
 * - Meets minimum and maximum length requirements
 *
 * Usage as a Validator provider:
 *
 * ```php
 * $validator->setProvider('slugValidator', SlugValidator::class);
 * $validator->add('slug', 'validSlug', [
 *     'rule' => ['isValid'],
 *     'provider' => 'slugValidator',
 *     'message' => 'Invalid slug format.',
 * ]);
 * ```
 */
class SlugValidator
{
    /**
     * Default minimum slug length.
     *
     * @var int
     */
    public const DEFAULT_MIN_LENGTH = 4;

    /**
     * Default maximum slug length.
     *
     * @var int
     */
    public const DEFAULT_MAX_LENGTH = 24;

    /**
     * Validate a slug format.
     *
     * @param mixed $value The value to validate.
     * @param int $minLength Minimum slug length.
     * @param int $maxLength Maximum slug length.
     * @return bool True if the value is a valid slug.
     */
    public static function isValid(
        mixed $value,
        int $minLength = self::DEFAULT_MIN_LENGTH,
        int $maxLength = self::DEFAULT_MAX_LENGTH,
    ): bool {
        if (!is_string($value) || $value === '') {
            return false;
        }

        // Minimum length must be at least 2 because the regex requires
        // a start character and an end character (both alphanumeric).
        if ($minLength < 2) {
            throw new InvalidArgumentException(__d('elastic/slug_guard', 'minLength must be at least 2.'));
        }
        if ($maxLength < $minLength) {
            return false;
        }

        // The regex is split into three parts: start char + middle chars + end char.
        // Start and end must be alphanumeric (no leading/trailing hyphens).
        // Middle section allows hyphens, with length adjusted by -2 for the anchors.
        $innerMin = $minLength - 2;
        $innerMax = $maxLength - 2;

        $pattern = sprintf(
            '/\A[0-9a-z][0-9a-z\-]{%d,%d}[0-9a-z]\z/',
            $innerMin,
            $innerMax,
        );

        return (bool)preg_match($pattern, $value);
    }
}
