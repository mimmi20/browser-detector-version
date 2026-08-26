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

use BrowserDetector\Version\NullVersion;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

final class NullVersionTest extends TestCase
{
    /**
     * @throws ExpectationFailedException
     * @throws Exception
     */
    public function testToArray(): void
    {
        $nullVersion = new NullVersion();

        self::assertNull($nullVersion->getMajor(), 'major is wrong');
        self::assertNull($nullVersion->getMinor(), 'minor is wrong');
        self::assertNull($nullVersion->getMicro(), 'micro is wrong');
        self::assertNull($nullVersion->getPatch(), 'patch is wrong');
        self::assertNull($nullVersion->getMicropatch(), 'micropatch is wrong');
        self::assertNull($nullVersion->getStability(), 'stability is wrong');
        self::assertNull($nullVersion->getBuild(), 'build is wrong');
        self::assertNull($nullVersion->isBeta(), 'beta is wrong');
        self::assertNull($nullVersion->isAlpha(), 'alpha is wrong');
        self::assertNull($nullVersion->getVersion(), 'complete is wrong');

        $array = $nullVersion->toArray();

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
