<?php

declare(strict_types=1);

/*
 * This file is part of vaibhavpandeyvpz/jweety package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled with this source code in the LICENSE file.
 */

namespace Jweety;

/**
 * JWT signature algorithm enumeration
 *
 * This enum represents the supported HMAC-based signature algorithms for JWT tokens.
 * All algorithms use HMAC (Hash-based Message Authentication Code) with different
 * hash functions as specified in RFC 7518.
 *
 * @author Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * @since 1.0.0
 * @see https://tools.ietf.org/html/rfc7518 JSON Web Algorithms (JWA) specification
 */
enum Algorithm: string
{
    /**
     * HMAC using SHA-256
     *
     * Provides 256-bit security level. Most commonly used algorithm.
     */
    case HS256 = 'HS256';

    /**
     * HMAC using SHA-384
     *
     * Provides 384-bit security level. Higher security than HS256.
     */
    case HS384 = 'HS384';

    /**
     * HMAC using SHA-512
     *
     * Provides 512-bit security level. Highest security among supported algorithms.
     */
    case HS512 = 'HS512';

    /**
     * Get the underlying hash method name for this algorithm
     *
     * Returns the PHP hash function name that corresponds to this algorithm.
     * This is used internally by the Encoder class for HMAC computation.
     *
     * @return string The hash method name (e.g., 'SHA256', 'SHA384', 'SHA512')
     *
     * @example
     * $method = Algorithm::HS256->hashMethod(); // Returns 'SHA256'
     */
    public function hashMethod(): string
    {
        return match ($this) {
            self::HS256 => 'SHA256',
            self::HS384 => 'SHA384',
            self::HS512 => 'SHA512',
        };
    }

    /**
     * Get all algorithm values as an array of strings
     *
     * Returns an array containing all algorithm string values. Useful for
     * validation, iteration, or when you need to work with algorithm strings
     * instead of enum instances.
     *
     * @return array<string> Array of algorithm string values
     *
     * @example
     * $algorithms = Algorithm::values();
     * // Returns: ['HS256', 'HS384', 'HS512']
     */
    public static function values(): array
    {
        return array_map(fn (Algorithm $alg) => $alg->value, self::cases());
    }
}
