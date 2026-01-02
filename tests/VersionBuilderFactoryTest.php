<?php

/**
 * This file is part of the browser-detector-version package.
 *
 * Copyright (c) 2016-2026, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace BrowserDetectorTest\Version;

use BrowserDetector\Version\VersionBuilder;
use BrowserDetector\Version\VersionBuilderFactory;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

final class VersionBuilderFactoryTest extends TestCase
{
    /**
     * @throws ExpectationFailedException
     * @throws Exception
     */
    public function testInvoke(): void
    {
        $result = (new VersionBuilderFactory())();

        self::assertInstanceOf(VersionBuilder::class, $result);
    }
}
