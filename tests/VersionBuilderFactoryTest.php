<?php
/**
 * This file is part of the browser-detector-version package.
 *
 * Copyright (c) 2016-2023, Thomas Mueller <mimmi20@live.de>
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
use Psr\Log\NullLogger;

final class VersionBuilderFactoryTest extends TestCase
{
    private VersionBuilderFactory $object;

    /** @throws void */
    protected function setUp(): void
    {
        $this->object = new VersionBuilderFactory();
    }

    /**
     * @throws ExpectationFailedException
     * @throws Exception
     */
    public function testInvoke(): void
    {
        $result = ($this->object)(new NullLogger());

        self::assertInstanceOf(VersionBuilder::class, $result);
    }
}