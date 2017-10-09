<?php

/*
 * This file is part of vaibhavpandeyvpz/jweety package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled with this source code in the LICENSE file.
 */

namespace Jweety;

/**
 * Class Encoder
 * @package Jweety
 */
class Encoder implements EncoderInterface
{
    /**
     * @var array
     */
    protected $algorithms;

    /**
     * @var string
     */
    protected $key;

    /**
     * Encoder constructor.
     * @param string $key
     * @param string|array $algorithms
     */
    public function __construct($key, $algorithms = array('HS256', 'HS384', 'HS512'))
    {
        $this->key = $key;
        $this->algorithms = (array)$algorithms;
    }

    /**
     * @param object $claims
     * @throws \InvalidArgumentException
     */
    public function assert($claims)
    {
        if (isset($claims->exp) && (time() >= $claims->exp)) {
            throw new \InvalidArgumentException(
                sprintf("Provided JWT has expired on '%s'.", date(DATE_ISO8601, $claims->exp))
            );
        }
        if (isset($claims->iat) && (time() < $claims->iat)) {
            throw new \InvalidArgumentException(
                sprintf('Provided JWT was issued in future (%s).', date(DATE_ISO8601, $claims->iat))
            );
        }
        if (isset($claims->nbf) && (time() < $claims->nbf)) {
            throw new \InvalidArgumentException(
                sprintf("Provided JWT cannot be used before '%s'.", date(DATE_ISO8601, $claims->nbf))
            );
        }
    }

    /**
     * @param string $payload
     * @return string
     */
    protected static function decode($payload)
    {
        $remainder = strlen($payload) % 4;
        if ($remainder) {
            $padding = 4 - $remainder;
            $payload .= str_repeat('=', $padding);
        }
        return base64_decode(strtr($payload, '-_', '+/'));
    }

    /**
     * @param string $payload
     * @return string
     */
    public static function encode($payload)
    {
        return str_replace('=', '', strtr(base64_encode($payload), '+/', '-_'));
    }

    /**
     * {@inheritdoc}
     */
    public function parse($token, $assert = true)
    {
        $parts = explode('.', $token);
        if (3 !== count($parts)) {
            throw new \InvalidArgumentException('Invalid or malformed JWT supplied.');
        }
        $header = json_decode(self::decode($parts[0]), true);
        $signature = $this->sign("$parts[0].$parts[1]", $header['alg']);
        if (hash_equals($signature, (string)self::decode($parts[2]))) {
            $claims = json_decode(self::decode($parts[1]));
            if ($assert) {
                $this->assert($claims);
            }
            return $claims;
        }
        throw new \RuntimeException('Signature verification failed for supplied token.');
    }

    /**
     * @param string $payload
     * @param string $algorithm
     * @return string
     */
    protected function sign($payload, $algorithm)
    {
        if (in_array($algorithm, $this->algorithms)) {
            static $methods = array(
                'HS256' => 'SHA256',
                'HS384' => 'SHA384',
                'HS512' => 'SHA512',
            );
            if (isset($methods[$algorithm])) {
                return hash_hmac($methods[$algorithm], $payload, $this->key, true);
            }
            throw new \InvalidArgumentException("Signature algorithm '$algorithm' is not supported.");
        }
        throw new \InvalidArgumentException("Signature algorithm '$algorithm' is not allowed.");
    }

    /**
     * {@inheritdoc}
     */
    public function stringify($claims, $alg = 'HS256', $typ = 'JWT')
    {
        $header = self::encode(json_encode(compact('alg', 'typ')));
        $payload = self::encode(json_encode($claims));
        $signature = self::encode($this->sign("$header.$payload", $alg));
        return "$header.$payload.$signature";
    }
}
