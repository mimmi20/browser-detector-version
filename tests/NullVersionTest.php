<?php
/**
 * This file is part of the browser-detector-version package.
 *
 * Copyright (c) 2016-2021, Thomas Mueller <mimmi20@live.de>
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
     * @throws \UnexpectedValueException
     *
     * @return void
     */
    public function testToarray(): void
    {
        $version = new NullVersion();

        self::assertNull($version->getMajor(), 'major is wrong');
        self::assertNull($version->getMinor(), 'minor is wrong');
        self::assertNull($version->getMicro(), 'micro is wrong');
        self::assertNull($version->getPatch(), 'patch is wrong');
        self::assertNull($version->getMicropatch(), 'micropatch is wrong');
        self::assertNull($version->getStability(), 'stability is wrong');
        self::assertNull($version->getBuild(), 'build is wrong');
        self::assertNull($version->isBeta(), 'beta is wrong');
        self::assertNull($version->isAlpha(), 'alpha is wrong');
        self::assertNull($version->getVersion(), 'complete is wrong');

        $array = $version->toArray();

        self::assertArrayHasKey('major', $array);
        self::assertNull($array['major']);
        self::assertArrayHasKey('minor', $array);
        self::assertNull($array['minor']);
        self::assertArrayHasKey('micro', $array);
        self::assertNull($array['micro']);
        self::assertArrayHasKey('patch', $array);
        self::assertNull($array['patch']);
        self::assertArrayHasKey('micropatch', $array);
        self::assertNull($array['micropatch']);
        self::assertArrayHasKey('stability', $array);
        self::assertNull($array['stability']);
        self::assertArrayHasKey('build', $array);
        self::assertNull($array['build']);
    }
}
