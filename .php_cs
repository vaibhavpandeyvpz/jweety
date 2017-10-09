<?php

$header = <<<EOF
This file is part of vaibhavpandeyvpz/jweety package.

(c) Vaibhav Pandey <contact@vaibhavpandey.com>

This source file is subject to the MIT license that is bundled with this source code in the LICENSE file.
EOF;

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

return Config::create()
    ->setFinder(
        Finder::create()
            ->in(__DIR__ . '/src')
            ->in(__DIR__ . '/tests')
    )
    ->setRules([
        '@PSR1' => true,
        '@PSR2' => true,
        'header_comment' => ['header' => $header],
        'array_syntax' => ['syntax' => 'long'],
    ])
    ->setUsingCache(true);
