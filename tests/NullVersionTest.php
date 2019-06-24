<?php
/**
 * This file is part of the browser-detector-version package.
 *
 * Copyright (c) 2016-2019, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);
namespace BrowserDetectorTest\Version;

use BrowserDetector\Version\NullVersion;
use PHPUnit\Framework\TestCase;

final class NullVersionTest extends TestCase
{
    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function testToarray(): void
    {
        $version = new NullVersion();

        static::assertNull($version->getMajor());
        static::assertNull($version->getMinor());
        static::assertNull($version->getMicro());
        static::assertNull($version->getPatch());
        static::assertNull($version->getMicropatch());
        static::assertNull($version->getStability());
        static::assertNull($version->getBuild());

        $array = $version->toArray();

        static::assertArrayHasKey('major', $array);
        static::assertNull($array['major']);
        static::assertArrayHasKey('minor', $array);
        static::assertNull($array['minor']);
        static::assertArrayHasKey('micro', $array);
        static::assertNull($array['micro']);
        static::assertArrayHasKey('patch', $array);
        static::assertNull($array['patch']);
        static::assertArrayHasKey('micropatch', $array);
        static::assertNull($array['micropatch']);
        static::assertArrayHasKey('stability', $array);
        static::assertNull($array['stability']);
        static::assertArrayHasKey('build', $array);
        static::assertNull($array['build']);
    }
}
