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
    public function testNotNumericMajor(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Major version must be a non-negative number formatted as string');

        new Version('a');
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
    public function testNotNumericMinor(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Minor version must be a non-negative number formatted as string');

        new Version('0', 'b');
    }

    /**
     * @return void
     */
    public function testNegativeMicro(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Patch version must be a non-negative number formatted as string');

        new Version('0', '0', '-1');
    }

    /**
     * @return void
     */
    public function testNotNumericMicro(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Patch version must be a non-negative number formatted as string');

        new Version('0', '0', 'c');
    }

    /**
     * @return void
     */
    public function testToarray(): void
    {
        $major      = '4';
        $minor      = '0';
        $micro      = '0';
        $patch      = '1';
        $micropatch = null;
        $stability  = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);

        self::assertSame($major, $version->getMajor());
        self::assertSame($minor, $version->getMinor());
        self::assertSame($micro, $version->getMicro());
        self::assertSame($patch, $version->getPatch());
        self::assertSame($micropatch, $version->getMicropatch());
        self::assertSame($stability, $version->getStability());
        self::assertSame($build, $version->getBuild());

        $array = $version->toArray();

        self::assertArrayHasKey('major', $array);
        self::assertIsString($array['major']);
        self::assertArrayHasKey('minor', $array);
        self::assertIsString($array['minor']);
        self::assertArrayHasKey('micro', $array);
        self::assertIsString($array['micro']);
        self::assertArrayHasKey('patch', $array);
        self::assertIsString($array['patch']);
        self::assertArrayHasKey('micropatch', $array);
        self::assertArrayHasKey('stability', $array);
        self::assertIsString($array['stability']);
        self::assertArrayHasKey('build', $array);

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
        $micro      = '0';
        $patch      = '1';
        $micropatch = null;
        $stability  = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);

        self::assertSame('4.0', $version->getVersion(VersionInterface::IGNORE_MAJOR_IF_EMPTY | VersionInterface::IGNORE_MICRO));

        $major      = '4';
        $minor      = '0';
        $micro      = '1';
        $patch      = '1';
        $micropatch = null;
        $stability  = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);

        self::assertSame('4.0', $version->getVersion(VersionInterface::IGNORE_MICRO));
    }

    /**
     * @return void
     */
    public function testGetversionWithoutMinor(): void
    {
        $major      = '4';
        $minor      = '0';
        $micro      = '0';
        $patch      = '1';
        $micropatch = null;
        $stability  = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);

        self::assertSame('4', $version->getVersion(VersionInterface::IGNORE_MINOR));

        $major      = '4';
        $minor      = '1';
        $micro      = '1';
        $patch      = '1';
        $micropatch = null;
        $stability  = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);

        self::assertSame('4', $version->getVersion(VersionInterface::IGNORE_MINOR));
    }

    /**
     * @return void
     */
    public function testGetversionWithoutEmptyMicro(): void
    {
        $major      = '4';
        $minor      = '0';
        $micro      = '1';
        $patch      = '2';
        $micropatch = null;
        $stability  = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);

        self::assertSame('4.0.1.2-beta+8', $version->getVersion(VersionInterface::IGNORE_MICRO_IF_EMPTY));

        $major      = '4';
        $minor      = '0';
        $micro      = '0';
        $patch      = '1';
        $micropatch = null;
        $stability  = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);

        self::assertSame('4.0', $version->getVersion(VersionInterface::IGNORE_MICRO_IF_EMPTY));

        $major      = '4';
        $minor      = '0';
        $micro      = '00';
        $patch      = '1';
        $micropatch = null;
        $stability  = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);

        self::assertSame('4.0', $version->getVersion(VersionInterface::IGNORE_MICRO_IF_EMPTY));
    }

    /**
     * @return void
     */
    public function testGetversionWithoutEmptyMinor(): void
    {
        $major      = '4';
        $minor      = '0';
        $micro      = '1';
        $patch      = '1';
        $micropatch = null;
        $stability  = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);

        self::assertSame('4.0.1.1-beta+8', $version->getVersion(VersionInterface::IGNORE_MINOR_IF_EMPTY));

        $major      = '4';
        $minor      = '0';
        $micro      = '0';
        $patch      = '1';
        $micropatch = null;
        $stability  = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);

        self::assertSame('4', $version->getVersion(VersionInterface::IGNORE_MINOR_IF_EMPTY));

        $major      = '4';
        $minor      = '0';
        $micro      = '0';
        $patch      = '1';
        $micropatch = null;
        $stability  = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);

        self::assertSame('4', $version->getVersion(VersionInterface::IGNORE_MINOR_IF_EMPTY));

        $major      = '4';
        $minor      = '0';
        $micro      = '1';
        $patch      = '1';
        $micropatch = null;
        $stability  = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);

        self::assertSame('4', $version->getVersion(VersionInterface::IGNORE_MINOR_IF_EMPTY | VersionInterface::IGNORE_MICRO));

        $major      = '4';
        $minor      = '1';
        $micro      = '0';
        $patch      = '3';
        $micropatch = null;
        $stability  = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);

        self::assertSame('4.1.0.3-beta+8', $version->getVersion(VersionInterface::IGNORE_MINOR_IF_EMPTY));
    }

    /**
     * @return void
     */
    public function testGetversionWithoutEmptyMajor(): void
    {
        $major      = '0';
        $minor      = '0';
        $micro      = '1';
        $patch      = '1';
        $micropatch = null;
        $stability  = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);

        self::assertSame('0.0.1.1-beta+8', $version->getVersion(VersionInterface::IGNORE_MAJOR_IF_EMPTY));

        $major      = '0';
        $minor      = '0';
        $micro      = '0';
        $patch      = '1';
        $micropatch = null;
        $stability  = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);

        self::assertSame('', $version->getVersion(VersionInterface::IGNORE_MAJOR_IF_EMPTY));

        $major      = '00';
        $minor      = '0';
        $micro      = '0';
        $patch      = '1';
        $micropatch = null;
        $stability  = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);

        self::assertSame('', $version->getVersion(VersionInterface::IGNORE_MAJOR_IF_EMPTY));

        $major      = '0';
        $minor      = '1';
        $micro      = '0';
        $patch      = '1';
        $micropatch = null;
        $stability  = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);

        self::assertSame('', $version->getVersion(VersionInterface::IGNORE_MAJOR_IF_EMPTY | VersionInterface::IGNORE_MINOR));

        $major      = '0';
        $minor      = '0';
        $micro      = '0';
        $patch      = '1';
        $micropatch = null;
        $stability  = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);

        self::assertSame('0', $version->getVersion(VersionInterface::IGNORE_MAJOR_IF_EMPTY | VersionInterface::GET_ZERO_IF_EMPTY));
    }

    /**
     * @return void
     */
    public function testMicrowithDot(): void
    {
        $major = '0';
        $minor = '1';
        $micro = '2';
        $patch = '3';

        $version = new Version($major, $minor, "${micro}.${patch}");

        self::assertSame($major, $version->getMajor());
        self::assertSame($minor, $version->getMinor());
        self::assertSame($micro, $version->getMicro());
        self::assertSame($patch, $version->getPatch());
        self::assertNull($version->getMicropatch());

        $major = '0';
        $minor = '1';
        $micro = '2';
        $patch = '3';

        $version = new Version($major, $minor, "${micro}.1", $patch);

        self::assertSame($major, $version->getMajor());
        self::assertSame($minor, $version->getMinor());
        self::assertSame($micro, $version->getMicro());
        self::assertSame($patch, $version->getPatch());
        self::assertNull($version->getMicropatch());

        $major      = '0';
        $minor      = '1';
        $micro      = '2';
        $patch      = '3';
        $micropatch = '4';

        $version = new Version($major, $minor, "${micro}.${patch}.${micropatch}");

        self::assertSame($major, $version->getMajor());
        self::assertSame($minor, $version->getMinor());
        self::assertSame($micro, $version->getMicro());
        self::assertSame($patch, $version->getPatch());
        self::assertSame($micropatch, $version->getMicropatch());

        $major = '0';
        $minor = '1';
        $micro = '2';
        $patch = '3';

        $version = new Version($major, $minor, "${micro}.1.4", $patch);

        self::assertSame($major, $version->getMajor());
        self::assertSame($minor, $version->getMinor());
        self::assertSame($micro, $version->getMicro());
        self::assertSame($patch, $version->getPatch());
        self::assertNull($version->getMicropatch());

        $major      = '0';
        $minor      = '1';
        $micro      = '2';
        $patch      = '3';
        $micropatch = '4';

        $version = new Version($major, $minor, "${micro}.1.1", $patch, $micropatch);

        self::assertSame($major, $version->getMajor());
        self::assertSame($minor, $version->getMinor());
        self::assertSame($micro, $version->getMicro());
        self::assertSame($patch, $version->getPatch());
        self::assertSame($micropatch, $version->getMicropatch());
    }

    /**
     * @return void
     */
    public function testMicrowithoutDot(): void
    {
        $major = '0';
        $minor = '1';
        $micro = '2';
        $patch = '3';

        $version = new Version($major, $minor, $micro, $patch);

        self::assertSame($major, $version->getMajor());
        self::assertSame($minor, $version->getMinor());
        self::assertSame($micro, $version->getMicro());
        self::assertSame($patch, $version->getPatch());
        self::assertNull($version->getMicropatch());

        $major      = '0';
        $minor      = '1';
        $micro      = '2';
        $patch      = '3';
        $micropatch = '4';

        $version = new Version($major, $minor, $micro, $patch, $micropatch);

        self::assertSame($major, $version->getMajor());
        self::assertSame($minor, $version->getMinor());
        self::assertSame($micro, $version->getMicro());
        self::assertSame($patch, $version->getPatch());
        self::assertSame($micropatch, $version->getMicropatch());
    }
}
