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

use Jweety\Exception\InvalidSignatureException;
use Jweety\Exception\InvalidTokenException;
use Jweety\Exception\TokenExpiredException;
use Jweety\Exception\UnsupportedAlgorithmException;
use Psr\Clock\ClockInterface;

/**
 * JWT Encoder class for encoding, decoding, and validating JSON Web Tokens (RFC 7519)
 *
 * This class provides functionality to create and parse JWT tokens using HMAC-based
 * signature algorithms (HS256, HS384, HS512). It supports both enum and string-based
 * algorithm specification and includes comprehensive claim validation.
 *
 * @author Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * @since 1.0.0
 */
class Encoder implements EncoderInterface
{
    /**
     * Default JWT type identifier
     *
     * @var string
     */
    private const DEFAULT_TYPE = 'JWT';

    /**
     * List of allowed signature algorithms
     *
     * @var array<string> Array of algorithm strings (e.g., ['HS256', 'HS384'])
     */
    private readonly array $algorithms;

    /**
     * Clock interface for time-based claim validation
     *
     * @var ClockInterface|null
     */
    private readonly ?ClockInterface $clock;

    /**
     * Encoder constructor
     *
     * Initializes the encoder with a secret key and optionally configures
     * which algorithms are allowed for token signing and verification.
     *
     * @param  string  $key  The secret key used for HMAC signing and verification.
     *                       This parameter is marked as sensitive to prevent
     *                       accidental exposure in stack traces or logs.
     * @param  array<Algorithm|string>|Algorithm|string  $algorithms  Allowed signature algorithms.
     *                                                                Can be a single algorithm (enum or string), or an array
     *                                                                of algorithms. Defaults to all supported algorithms
     *                                                                (HS256, HS384, HS512).
     * @param  ClockInterface|null  $clock  Optional PSR-20 clock implementation for time-based
     *                                       claim validation. If not provided, uses system time via time().
     *                                       Useful for testing with controllable time.
     *
     * @example
     * $encoder = new Encoder('my-secret-key');
     * $encoder = new Encoder('my-secret-key', Algorithm::HS256);
     * $encoder = new Encoder('my-secret-key', ['HS256', 'HS384'], $clock);
     */
    public function __construct(
        #[\SensitiveParameter]
        private readonly string $key,
        array|Algorithm|string $algorithms = [Algorithm::HS256, Algorithm::HS384, Algorithm::HS512],
        ?ClockInterface $clock = null
    ) {
        $this->algorithms = $this->normalizeAlgorithms($algorithms);
        $this->clock = $clock;
    }

    /**
     * Normalize algorithms input to a consistent array of string values
     *
     * Converts various algorithm input formats (single enum, single string,
     * array of enums/strings, or mixed) into a uniform array of string values.
     *
     * @param  array<Algorithm|string>|Algorithm|string  $algorithms  The algorithms to normalize
     * @return array<string> Array of algorithm string values
     *
     * @internal This method is used internally during object construction
     */
    private function normalizeAlgorithms(array|Algorithm|string $algorithms): array
    {
        return match (true) {
            is_string($algorithms) => [$algorithms],
            $algorithms instanceof Algorithm => [$algorithms->value],
            default => array_map(
                fn (Algorithm|string $alg): string => $alg instanceof Algorithm ? $alg->value : $alg,
                $algorithms
            ),
        };
    }

    /**
     * Assert JWT claims validity
     *
     * Validates standard JWT claims (exp, iat, nbf) against the current time.
     * This method checks:
     * - exp (expiration): Token must not be expired
     * - iat (issued at): Token must not be issued in the future
     * - nbf (not before): Token must be usable at the current time
     *
     * Uses the configured PSR-20 clock if provided, otherwise falls back to system time.
     *
     * @param  object  $claims  The decoded JWT claims object
     *
     * @throws TokenExpiredException When the token has expired (exp claim is in the past)
     * @throws InvalidTokenException When the token was issued in the future (iat > now)
     *                               or cannot be used yet (nbf > now)
     *
     * @example
     * $claims = (object)['sub' => 'user123', 'exp' => time() + 3600];
     * $encoder->assert($claims); // Valid, no exception
     */
    public function assert(object $claims): void
    {
        $now = $this->getCurrentTime();

        if (isset($claims->exp) && $now >= $claims->exp) {
            throw new TokenExpiredException(
                sprintf("Provided JWT has expired on '%s'.", date(DATE_ISO8601, $claims->exp))
            );
        }

        if (isset($claims->iat) && $now < $claims->iat) {
            throw new InvalidTokenException(
                sprintf('Provided JWT was issued in future (%s).', date(DATE_ISO8601, $claims->iat))
            );
        }

        if (isset($claims->nbf) && $now < $claims->nbf) {
            throw new InvalidTokenException(
                sprintf("Provided JWT cannot be used before '%s'.", date(DATE_ISO8601, $claims->nbf))
            );
        }
    }

    /**
     * Get the current timestamp
     *
     * Uses the configured PSR-20 clock if available, otherwise falls back to system time.
     *
     * @return int Current Unix timestamp
     *
     * @internal This method is used internally for time-based claim validation
     */
    private function getCurrentTime(): int
    {
        return $this->clock?->now()->getTimestamp() ?? time();
    }

    /**
     * Decode a base64url-encoded string
     *
     * Converts a base64url-encoded string back to its original binary representation.
     * This method handles padding automatically and validates the encoding.
     *
     * @param  string  $payload  The base64url-encoded string to decode
     * @return string The decoded binary string
     *
     * @throws InvalidTokenException When the payload contains invalid base64 characters
     *                               or cannot be decoded
     *
     * @see https://tools.ietf.org/html/rfc4648#section-5 Base64URL encoding specification
     */
    public static function decode(string $payload): string
    {
        $remainder = strlen($payload) % 4;
        if ($remainder !== 0) {
            $padding = 4 - $remainder;
            $payload = $payload.str_repeat('=', $padding);
        }
        $decoded = base64_decode(strtr($payload, '-_', '+/'), true);
        if ($decoded === false) {
            throw new InvalidTokenException('Invalid base64 encoding.');
        }

        return $decoded;
    }

    /**
     * Encode a string to base64url format
     *
     * Converts a binary string to base64url encoding, which is URL-safe and
     * suitable for use in JWT tokens. This encoding uses '-' and '_' instead
     * of '+' and '/', and omits padding characters.
     *
     * @param  string  $payload  The binary string to encode
     * @return string The base64url-encoded string
     *
     * @see https://tools.ietf.org/html/rfc4648#section-5 Base64URL encoding specification
     */
    public static function encode(string $payload): string
    {
        return str_replace('=', '', strtr(base64_encode($payload), '+/', '-_'));
    }

    /**
     * Parse and validate a JWT token
     *
     * Decodes a JWT token string, verifies its signature, and optionally validates
     * its claims. The token must be in the format: header.payload.signature
     *
     * @param  string  $token  The JWT token string to parse
     * @param  bool  $assert  Whether to validate claims (exp, iat, nbf). Set to false
     *                        to skip claim validation and only verify the signature.
     * @return object The decoded claims object
     *
     * @throws InvalidTokenException When the token is malformed, has invalid encoding,
     *                               missing required fields, or fails claim validation
     * @throws InvalidSignatureException When the token signature verification fails
     * @throws TokenExpiredException When the token has expired (only if $assert is true)
     *
     * @example
     * $token = 'eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiIxMjM0NTY3ODkwIn0...';
     * $claims = $encoder->parse($token);
     * echo $claims->sub; // '1234567890'
     */
    public function parse(string $token, bool $assert = true): object
    {
        $parts = explode('.', $token, 3);
        if (count($parts) !== 3) {
            throw new InvalidTokenException('Invalid or malformed JWT supplied.');
        }

        [$headerPart, $payloadPart, $signaturePart] = $parts;

        $header = json_decode(self::decode($headerPart), true);
        if (! is_array($header) || ! isset($header['alg'])) {
            throw new InvalidTokenException('Invalid JWT header.');
        }

        $algorithm = $header['alg'];
        $expectedSignature = $this->sign("$headerPart.$payloadPart", $algorithm);
        $providedSignature = self::decode($signaturePart);

        if (! hash_equals($expectedSignature, $providedSignature)) {
            throw new InvalidSignatureException('Signature verification failed for supplied token.');
        }

        $claims = json_decode(self::decode($payloadPart));
        if (! is_object($claims)) {
            throw new InvalidTokenException('Invalid JWT payload.');
        }

        if ($assert) {
            $this->assert($claims);
        }

        return $claims;
    }

    /**
     * Sign a payload using the specified algorithm
     *
     * Creates an HMAC signature for the given payload using the configured secret key
     * and the specified algorithm. The algorithm must be in the allowed list and
     * must be a supported algorithm.
     *
     * @param  string  $payload  The payload to sign (typically "header.payload")
     * @param  string  $algorithm  The algorithm identifier (e.g., 'HS256', 'HS384', 'HS512')
     * @return string The binary HMAC signature
     *
     * @throws UnsupportedAlgorithmException When the algorithm is not in the allowed list
     *                                       or is not a supported algorithm type
     *
     * @internal This method is used internally during token creation and verification
     */
    protected function sign(string $payload, string $algorithm): string
    {
        if (! in_array($algorithm, $this->algorithms, true)) {
            throw new UnsupportedAlgorithmException("Signature algorithm '$algorithm' is not allowed.");
        }

        $algorithmEnum = Algorithm::tryFrom($algorithm);
        if ($algorithmEnum === null) {
            throw new UnsupportedAlgorithmException("Signature algorithm '$algorithm' is not supported.");
        }

        return hash_hmac($algorithmEnum->hashMethod(), $payload, $this->key, true);
    }

    /**
     * Create a JWT token from claims
     *
     * Encodes the provided claims into a JWT token string with the format:
     * base64url(header).base64url(payload).base64url(signature)
     *
     * @param  array|object  $claims  The claims to encode (e.g., ['sub' => 'user123', 'exp' => time() + 3600])
     * @param  Algorithm|string  $alg  The signature algorithm to use. Can be an Algorithm enum
     *                                 or a string. Defaults to HS256.
     * @param  string  $typ  The token type identifier. Defaults to 'JWT'.
     * @return string The encoded JWT token string
     *
     * @throws UnsupportedAlgorithmException When the specified algorithm is not allowed
     *                                       or not supported
     * @throws \JsonException When the claims cannot be encoded as JSON
     *
     * @example
     * $claims = ['sub' => 'user123', 'iat' => time(), 'exp' => time() + 3600];
     * $token = $encoder->stringify($claims);
     * // Returns: 'eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1c2VyMTIzIn0...'
     */
    public function stringify(array|object $claims, Algorithm|string $alg = Algorithm::HS256, string $typ = self::DEFAULT_TYPE): string
    {
        $algorithm = $alg instanceof Algorithm ? $alg->value : $alg;
        $header = self::encode(json_encode(['alg' => $algorithm, 'typ' => $typ], JSON_THROW_ON_ERROR));
        $payload = self::encode(json_encode($claims, JSON_THROW_ON_ERROR));
        $signature = self::encode($this->sign("$header.$payload", $algorithm));

        return "$header.$payload.$signature";
    }
}
