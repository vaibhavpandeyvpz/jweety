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

use RuntimeException;

/**
 * Exception thrown when JWT signature verification fails
 *
 * This exception is thrown when the signature verification process fails,
 * indicating that the token may have been tampered with or was signed with
 * a different key than expected. This is a security-critical exception.
 *
 * @author Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * @since 1.0.0
 * @see https://tools.ietf.org/html/rfc7519#section-6 JWT signature verification
 */
class InvalidSignatureException extends RuntimeException {}
