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
 * Interface EncoderInterface
 * @package Jweety
 */
interface EncoderInterface
{
    /**
     * @param string $token
     * @param boolean $assert
     * @return object
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function parse($token, $assert = true);

    /**
     * @param array|object $claims
     * @param string $alg
     * @param string $typ
     * @return string
     * @throws \InvalidArgumentException
     */
    public function stringify($claims, $alg = 'HS256', $typ = 'JWT');
}
