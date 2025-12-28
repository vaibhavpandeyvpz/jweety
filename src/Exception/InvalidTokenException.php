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
 * Exception thrown when a JWT token is invalid or malformed
 *
 * This exception is thrown when:
 * - The token structure is invalid (wrong number of parts)
 * - The token encoding is invalid (invalid base64)
 * - The token header is malformed or missing required fields
 * - The token payload is invalid (not an object)
 * - The token claims are invalid (issued in future, not yet valid)
 *
 * @author Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * @since 1.0.0
 */
class InvalidTokenException extends InvalidArgumentException {}
