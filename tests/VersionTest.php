<?php
/**
 * This file is part of the browser-detector-version package.
 *
 * Copyright (c) 2015-2018, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);
namespace BrowserDetectorTest\Version;

use BrowserDetector\Version\Version;
use BrowserDetector\Version\VersionFactory;
use BrowserDetector\Version\VersionInterface;
use PHPUnit\Framework\TestCase;

final class VersionTest extends TestCase
{
    /**
     * @return void
     */
    public function testNegativeMajor(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Major version must be a non-negative number formatted as string');

        new Version('-1');
    }

    /**
     * @return void
     */
    public function testNegativeMinor(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Minor version must be a non-negative number formatted as string');

        new Version('0', '-1');
    }

    /**
     * @return void
     */
    public function testNegativePatch(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Patch version must be a non-negative number formatted as string');

        new Version('0', '0', '-1');
    }

    /**
     * @return void
     */
    public function testToarray(): void
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

    /**
     * @return void
     */
    public function testGetversionWithoutMicro(): void
    {
        $major      = '4';
        $minor      = '0';
        $patch      = '0';
        $preRelease = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $patch, $preRelease, $build);

        self::assertSame('4.0', $version->getVersion(VersionInterface::IGNORE_MICRO));
    }

    /**
     * @return void
     */
    public function testGetversionWithoutMinor(): void
    {
        $major      = '4';
        $minor      = '0';
        $patch      = '0';
        $preRelease = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $patch, $preRelease, $build);

        self::assertSame('4', $version->getVersion(VersionInterface::IGNORE_MINOR));
    }

    /**
     * @return void
     */
    public function testGetversionWithoutEmptyMicro(): void
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

    /**
     * @return void
     */
    public function testGetversionWithoutEmptyMinor(): void
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

    /**
     * @return void
     */
    public function testGetversionWithoutEmptyMajor(): void
    {
        $major      = '0';
        $minor      = '0';
        $patch      = '1';
        $preRelease = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $patch, $preRelease, $build);

        self::assertSame('0.0.1-beta+8', $version->getVersion(VersionInterface::IGNORE_MAJOR_IF_EMPTY));

        $major      = '0';
        $minor      = '0';
        $patch      = '0';
        $preRelease = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $patch, $preRelease, $build);

        self::assertSame('', $version->getVersion(VersionInterface::IGNORE_MAJOR_IF_EMPTY));

        $major      = '0';
        $minor      = '0';
        $patch      = '0';
        $preRelease = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $patch, $preRelease, $build);

        self::assertSame('0', $version->getVersion(VersionInterface::IGNORE_MAJOR_IF_EMPTY | VersionInterface::GET_ZERO_IF_EMPTY));
    }
}
