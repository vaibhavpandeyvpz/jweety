<?php

declare(strict_types=1);

/*
 * This file is part of vaibhavpandeyvpz/jweety package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled with this source code in the LICENSE file.
 */

namespace Jweety\Exception;

use InvalidArgumentException;

/**
 * Exception thrown when a JWT token has expired
 *
 * This exception is thrown when a token's 'exp' (expiration) claim indicates
 * that the token is no longer valid. The expiration time is compared against
 * the current time during token validation.
 *
 * @author Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * @since 1.0.0
 * @see https://tools.ietf.org/html/rfc7519#section-4.1.4 JWT exp claim specification
 */
class TokenExpiredException extends InvalidArgumentException {}
