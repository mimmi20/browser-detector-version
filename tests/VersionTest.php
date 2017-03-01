<?php
/**
 * This file is part of the browser-detector-version package.
 *
 * Copyright (c) 2015-2017, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);
namespace BrowserDetectorTest\Version;

use BrowserDetector\Version\Version;
use BrowserDetector\Version\VersionFactory;
use BrowserDetector\Version\VersionInterface;

/**
 * Test class for Version
 */
class VersionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Major version must be a non-negative integer or a string
     */
    public function testNegativeMajor()
    {
        new Version(-1);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Minor version must be a non-negative integer or a string
     */
    public function testNegativeMinor()
    {
        new Version(0, -1);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Patch version must be a non-negative integer or a string
     */
    public function testNegativePatch()
    {
        new Version(0, 0, -1);
    }

    public function testToarray()
    {
        $major      = '4';
        $minor      = '0';
        $patch      = '0';
        $preRelease = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $patch, $preRelease, $build);

        $array  = $version->toArray();
        $object = VersionFactory::fromArray($array);

        self::assertEquals($version, $object);
    }

    public function testGetversionWithoutMicro()
    {
        $major      = '4';
        $minor      = '0';
        $patch      = '0';
        $preRelease = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $patch, $preRelease, $build);

        self::assertSame('4.0', $version->getVersion(VersionInterface::IGNORE_MICRO));
    }

    public function testGetversionWithoutMinor()
    {
        $major      = '4';
        $minor      = '0';
        $patch      = '0';
        $preRelease = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $patch, $preRelease, $build);

        self::assertSame('4', $version->getVersion(VersionInterface::IGNORE_MINOR));
    }

    public function testGetversionWithoutEmptyMicro()
    {
        $major      = '4';
        $minor      = '0';
        $patch      = '1';
        $preRelease = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $patch, $preRelease, $build);

        self::assertSame('4.0.1-beta+8', $version->getVersion(VersionInterface::IGNORE_MICRO_IF_EMPTY));

        $major      = '4';
        $minor      = '0';
        $patch      = '0';
        $preRelease = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $patch, $preRelease, $build);

        self::assertSame('4.0', $version->getVersion(VersionInterface::IGNORE_MICRO_IF_EMPTY));
    }

    public function testGetversionWithoutEmptyMinor()
    {
        $major      = '4';
        $minor      = '0';
        $patch      = '1';
        $preRelease = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $patch, $preRelease, $build);

        self::assertSame('4.0.1-beta+8', $version->getVersion(VersionInterface::IGNORE_MINOR_IF_EMPTY));

        $major      = '4';
        $minor      = '0';
        $patch      = '0';
        $preRelease = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $patch, $preRelease, $build);

        self::assertSame('4', $version->getVersion(VersionInterface::IGNORE_MINOR_IF_EMPTY));
    }

    public function testGetversionWithoutEmptyMajor()
    {
        $major      = '0';
        $minor      = '0';
        $patch      = '1';
        $preRelease = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $patch, $preRelease, $build);

        self::assertSame('0.0.1-beta+8', $version->getVersion(VersionInterface::IGNORE_MACRO_IF_EMPTY));

        $major      = '0';
        $minor      = '0';
        $patch      = '0';
        $preRelease = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $patch, $preRelease, $build);

        self::assertSame('', $version->getVersion(VersionInterface::IGNORE_MACRO_IF_EMPTY));

        $major      = '0';
        $minor      = '0';
        $patch      = '0';
        $preRelease = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $patch, $preRelease, $build);

        self::assertSame('0', $version->getVersion(VersionInterface::IGNORE_MACRO_IF_EMPTY | VersionInterface::GET_ZERO_IF_EMPTY));
    }
}
