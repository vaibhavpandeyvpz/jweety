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
 * Interface for JWT encoding and decoding operations
 *
 * This interface defines the contract for classes that implement JWT (JSON Web Token)
 * encoding and decoding functionality according to RFC 7519.
 *
 * @author Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * @since 1.0.0
 */
interface EncoderInterface
{
    /**
     * Parse and validate a JWT token
     *
     * Decodes a JWT token string, verifies its signature, and optionally validates
     * its claims against standard JWT claim rules.
     *
     * @param  string  $token  The JWT token string to parse
     * @param  bool  $assert  Whether to validate claims (exp, iat, nbf). Defaults to true.
     * @return object The decoded claims object
     *
     * @throws \Jweety\Exception\InvalidTokenException When the token is malformed or invalid
     * @throws \Jweety\Exception\InvalidSignatureException When signature verification fails
     * @throws \Jweety\Exception\TokenExpiredException When the token has expired (if $assert is true)
     */
    public function parse(string $token, bool $assert = true): object;

    /**
     * Create a JWT token from claims
     *
     * Encodes the provided claims into a JWT token string with proper header,
     * payload, and signature components.
     *
     * @param  array|object  $claims  The claims to encode into the token
     * @param  Algorithm|string  $alg  The signature algorithm to use. Defaults to HS256.
     * @param  string  $typ  The token type identifier. Defaults to 'JWT'.
     * @return string The encoded JWT token string
     *
     * @throws \Jweety\Exception\UnsupportedAlgorithmException When the algorithm is not supported
     */
    public function stringify(array|object $claims, Algorithm|string $alg = Algorithm::HS256, string $typ = 'JWT'): string;
}
