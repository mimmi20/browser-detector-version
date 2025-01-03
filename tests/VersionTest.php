<?php

/**
 * This file is part of the browser-detector-version package.
 *
 * Copyright (c) 2016-2025, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace BrowserDetectorTest\Version;

use BrowserDetector\Version\Version;
use BrowserDetector\Version\VersionBuilder;
use BrowserDetector\Version\VersionInterface;
use InvalidArgumentException;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

use function sprintf;

final class VersionTest extends TestCase
{
    /** @throws InvalidArgumentException */
    public function testNegativeMajor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Major version must be a non-negative number formatted as string',
        );
        $this->expectExceptionCode(0);

        new Version('-1');
    }

    /** @throws InvalidArgumentException */
    public function testNotNumericMajor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Major version must be a non-negative number formatted as string',
        );
        $this->expectExceptionCode(0);

        new Version('a');
    }

    /** @throws InvalidArgumentException */
    public function testNegativeMinor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Minor version must be a non-negative number formatted as string',
        );
        $this->expectExceptionCode(0);

        new Version('0', '-1');
    }

    /** @throws InvalidArgumentException */
    public function testNotNumericMinor(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Minor version must be a non-negative number formatted as string',
        );
        $this->expectExceptionCode(0);

        new Version('0', 'b');
    }

    /** @throws InvalidArgumentException */
    public function testNegativeMicro(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Micro version must be a non-negative number formatted as string',
        );
        $this->expectExceptionCode(0);

        new Version('0', '0', '-1');
    }

    /** @throws InvalidArgumentException */
    public function testNegativeMicro2(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Micro version must be a non-negative number formatted as string',
        );
        $this->expectExceptionCode(0);

        new Version('0', '0', '-1.dev');
    }

    /** @throws InvalidArgumentException */
    public function testNegativeMicro3(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Micro version must be a non-negative number formatted as string',
        );
        $this->expectExceptionCode(0);

        new Version('0', '0', '.dev');
    }

    /** @throws InvalidArgumentException */
    public function testNotNumericMicro(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Micro version must be a non-negative number formatted as string',
        );
        $this->expectExceptionCode(0);

        new Version('0', '0', 'c');
    }

    /**
     * @throws ExpectationFailedException
     * @throws Exception
     * @throws InvalidArgumentException
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
        self::assertNull($version->getMicropatch());
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

        $object = VersionBuilder::fromArray($array);

        self::assertEquals($version, $object);
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
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

        self::assertSame(
            '4.0',
            $version->getVersion(
                VersionInterface::IGNORE_MAJOR_IF_EMPTY | VersionInterface::IGNORE_MICRO,
            ),
        );

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
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
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
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
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

        self::assertSame(
            '4.0.1.2-beta+8',
            $version->getVersion(VersionInterface::IGNORE_MICRO_IF_EMPTY),
        );

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
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
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

        self::assertSame(
            '4.0.1.1-beta+8',
            $version->getVersion(VersionInterface::IGNORE_MINOR_IF_EMPTY),
        );

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

        self::assertSame(
            '4',
            $version->getVersion(
                VersionInterface::IGNORE_MINOR_IF_EMPTY | VersionInterface::IGNORE_MICRO,
            ),
        );

        $major      = '4';
        $minor      = '1';
        $micro      = '0';
        $patch      = '3';
        $micropatch = null;
        $stability  = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);

        self::assertSame(
            '4.1.0.3-beta+8',
            $version->getVersion(VersionInterface::IGNORE_MINOR_IF_EMPTY),
        );
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
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

        self::assertSame(
            '0.0.1.1-beta+8',
            $version->getVersion(VersionInterface::IGNORE_MAJOR_IF_EMPTY),
        );
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testGetversionWithoutEmptyMajor2(): void
    {
        $major      = '0';
        $minor      = '0';
        $micro      = '0';
        $patch      = '1';
        $micropatch = null;
        $stability  = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);

        self::assertSame('', $version->getVersion(VersionInterface::IGNORE_MAJOR_IF_EMPTY));
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testGetversionWithoutEmptyMajor3(): void
    {
        $major      = '00';
        $minor      = '0';
        $micro      = '0';
        $patch      = '1';
        $micropatch = null;
        $stability  = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);

        self::assertSame('', $version->getVersion(VersionInterface::IGNORE_MAJOR_IF_EMPTY));
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testGetversionWithoutEmptyMajor4(): void
    {
        $major      = '0';
        $minor      = '1';
        $micro      = '0';
        $patch      = '1';
        $micropatch = null;
        $stability  = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);

        self::assertSame(
            '',
            $version->getVersion(
                VersionInterface::IGNORE_MAJOR_IF_EMPTY | VersionInterface::IGNORE_MINOR,
            ),
        );
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testGetversionWithoutEmptyMajor5(): void
    {
        $major      = '0';
        $minor      = '0';
        $micro      = '0';
        $patch      = '1';
        $micropatch = null;
        $stability  = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);

        self::assertSame(
            '0',
            $version->getVersion(
                VersionInterface::IGNORE_MAJOR_IF_EMPTY | VersionInterface::GET_ZERO_IF_EMPTY,
            ),
        );
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testMicrowithDot(): void
    {
        $major = '0';
        $minor = '1';
        $micro = '2';
        $patch = '3';

        $version = new Version($major, $minor, sprintf('%s.%s', $micro, $patch));

        self::assertSame($major, $version->getMajor());
        self::assertSame($minor, $version->getMinor());
        self::assertSame($micro, $version->getMicro());
        self::assertSame($patch, $version->getPatch());
        self::assertNull($version->getMicropatch());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testMicrowithDot2(): void
    {
        $major = '0';
        $minor = '1';
        $micro = '2';
        $patch = '3';

        $version = new Version($major, $minor, sprintf('%s.1', $micro), $patch);

        self::assertSame($major, $version->getMajor());
        self::assertSame($minor, $version->getMinor());
        self::assertSame($micro, $version->getMicro());
        self::assertSame($patch, $version->getPatch());
        self::assertNull($version->getMicropatch());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testMicrowithDot3(): void
    {
        $major      = '0';
        $minor      = '1';
        $micro      = '2';
        $patch      = '3';
        $micropatch = '4';

        $version = new Version($major, $minor, sprintf('%s.%s.%s', $micro, $patch, $micropatch));

        self::assertSame($major, $version->getMajor());
        self::assertSame($minor, $version->getMinor());
        self::assertSame($micro, $version->getMicro());
        self::assertSame($patch, $version->getPatch());
        self::assertSame($micropatch, $version->getMicropatch());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testMicrowithDot4(): void
    {
        $major = '0';
        $minor = '1';
        $micro = '2';
        $patch = '3';

        $version = new Version($major, $minor, sprintf('%s.1.4', $micro), $patch);

        self::assertSame($major, $version->getMajor());
        self::assertSame($minor, $version->getMinor());
        self::assertSame($micro, $version->getMicro());
        self::assertSame($patch, $version->getPatch());
        self::assertNull($version->getMicropatch());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testMicrowithDot5(): void
    {
        $major      = '0';
        $minor      = '1';
        $micro      = '2';
        $patch      = '3';
        $micropatch = '4';

        $version = new Version($major, $minor, sprintf('%s.1.1', $micro), $patch, $micropatch);

        self::assertSame($major, $version->getMajor());
        self::assertSame($minor, $version->getMinor());
        self::assertSame($micro, $version->getMicro());
        self::assertSame($patch, $version->getPatch());
        self::assertSame($micropatch, $version->getMicropatch());
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
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
    }

    /**
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testMicrowithoutDot2(): void
    {
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
