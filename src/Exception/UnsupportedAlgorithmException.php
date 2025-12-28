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
 * Exception thrown when an unsupported or disallowed algorithm is used
 *
 * This exception is thrown when:
 * - An algorithm is specified that is not in the allowed list
 * - An algorithm is specified that is not supported by the library
 * - An algorithm string cannot be converted to a valid Algorithm enum
 *
 * @author Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * @since 1.0.0
 * @see Algorithm For supported algorithms
 */
class UnsupportedAlgorithmException extends InvalidArgumentException {}
