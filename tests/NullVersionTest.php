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
     * @throws \PHPUnit\Framework\Exception
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function testToarray(): void
    {
        $version = new NullVersion();

        static::assertNull($version->getMajor(), 'major is wrong');
        static::assertNull($version->getMinor(), 'minor is wrong');
        static::assertNull($version->getMicro(), 'micro is wrong');
        static::assertNull($version->getPatch(), 'patch is wrong');
        static::assertNull($version->getMicropatch(), 'micropatch is wrong');
        static::assertNull($version->getStability(), 'stability is wrong');
        static::assertNull($version->getBuild(), 'build is wrong');
        static::assertNull($version->isBeta(), 'beta is wrong');
        static::assertNull($version->isAlpha(), 'alpha is wrong');
        static::assertNull($version->getVersion(), 'complete is wrong');

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
