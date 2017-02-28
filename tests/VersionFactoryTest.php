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

use BrowserDetector\Version\VersionFactory;

/**
 * Test class for VersionFactory
 */
class VersionFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider providerSet
     * @group        version
     *
     * @param string $version
     * @param string $major
     * @param string $minor
     * @param string $patch
     * @param string $preRelease
     * @param string $build
     * @param string $complete
     */
    public function testVersionSet($version, $major, $minor, $patch, $preRelease, $build, $complete)
    {
        $object = VersionFactory::set($version);

        self::assertInstanceOf('\BrowserDetector\Version\Version', $object);

        self::assertSame($major, $object->getMajor(), 'major is wrong');
        self::assertSame($minor, $object->getMinor(), 'minor is wrong');
        self::assertSame($patch, $object->getMicro(), 'patch is wrong');
        self::assertSame($preRelease, $object->getStability(), 'stability is wrong');
        self::assertSame($build, $object->getBuild(), 'build is wrong');
        self::assertSame($complete, $object->getVersion(), 'complete is wrong');
    }

    public function providerSet()
    {
        return [
            ['34.0.1760.0', '34', '0', '1760.0', 'stable', null, '34.0.1760.0'],
            ['3.9.0.0.22', '3', '9', '0.0.22', 'stable', null, '3.9.0.0.22'],
            ['4.1.1', '4', '1', '1', 'stable', null, '4.1.1'],
            ['7.0', '7', '0', '0', 'stable', null, '7.0.0'],
            ['1.17.0-rc', '1', '17', '0', 'RC', null, '1.17.0-RC'],
            ['4.3.2f1', '4', '3', '2', 'stable', null, '4.3.2'],
            ['v0.1.4', '0', '1', '4', 'stable', null, '0.1.4'],
            ['2.0b8', '2', '0', '0', 'beta', '8', '2.0.0-beta+8'],
            ['4.0b8', '4', '0', '0', 'beta', '8', '4.0.0-beta+8'],
            ['4.0a1', '4', '0', '0', 'alpha', '1', '4.0.0-alpha+1'],
            ['4.0dev2', '4', '0', '0', 'dev', '2', '4.0.0-dev+2'],
            ['abc', '0', '0', '0', 'stable', null, '0.0.0'],
            ['0.0.0', '0', '0', '0', 'stable', null, '0.0.0'],
            ['2.0p12', '2', '0', '0', 'patch', '12', '2.0.0-patch+12'],
        ];
    }

    /**
     * @group version
     */
    public function testVersionSetXp()
    {
        $object = VersionFactory::set('XP');

        self::assertInstanceOf('\BrowserDetector\Version\Version', $object);

        self::assertSame('XP', $object->getMajor(), 'major is wrong');
    }

    /**
     * @dataProvider providerDetectVersion
     * @group        version
     *
     * @param string $uapart
     * @param string $search
     * @param string $major
     * @param string $minor
     * @param string $patch
     * @param string $preRelease
     * @param string $build
     * @param string $complete
     */
    public function testVersionDetectVersion($uapart, $search, $major, $minor, $patch, $preRelease, $build, $complete)
    {
        $object = VersionFactory::detectVersion($uapart, $search);

        self::assertInstanceOf('\BrowserDetector\Version\Version', $object);

        self::assertSame($major, $object->getMajor(), 'major is wrong');
        self::assertSame($minor, $object->getMinor(), 'minor is wrong');
        self::assertSame($patch, $object->getMicro(), 'patch is wrong');
        self::assertSame($preRelease, $object->getStability(), 'stability is wrong');
        self::assertSame($build, $object->getBuild(), 'build is wrong');
        self::assertSame($complete, $object->getVersion(), 'complete is wrong');
    }

    public function providerDetectVersion()
    {
        return [
            ['Chrome/34.0.1760.0', 'Chrome', '34', '0', '1760.0', 'stable', null, '34.0.1760.0'],
            ['Firefox/4.0b8', 'Firefox', '4', '0', '0', 'beta', '8', '4.0.0-beta+8'],
            ['Firefox%20/4.0b8', 'Firefox%20', '4', '0', '0', 'beta', '8', '4.0.0-beta+8'],
            ['Firefox/4.0b8', [null, false, 'Firefox'], '4', '0', '0', 'beta', '8', '4.0.0-beta+8'],
        ];
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage a string or an array of strings is expected as parameter
     * @group        version
     */
    public function testThrowExcepton()
    {
        VersionFactory::detectVersion('abc', 1);
    }

    public function testFromArray()
    {
        $major      = '4';
        $minor      = '0';
        $patch      = '0';
        $preRelease = 'beta';
        $build      = '8';

        $data = [
            'major'     => $major,
            'minor'     => $minor,
            'micro'     => $patch,
            'stability' => $preRelease,
            'build'     => $build,
        ];
        $object = VersionFactory::fromArray($data);

        self::assertInstanceOf('\BrowserDetector\Version\Version', $object);

        self::assertSame($major, $object->getMajor(), 'major is wrong');
        self::assertSame($minor, $object->getMinor(), 'minor is wrong');
        self::assertSame($patch, $object->getMicro(), 'patch is wrong');
        self::assertSame($preRelease, $object->getStability(), 'stability is wrong');
        self::assertSame($build, $object->getBuild(), 'build is wrong');
        self::assertTrue($object->isBeta());
        self::assertFalse($object->isAlpha());
    }

    public function testFromJson()
    {
        $major      = '4';
        $minor      = '0';
        $patch      = '0';
        $preRelease = 'beta';
        $build      = '8';

        $data = [
            'major'     => $major,
            'minor'     => $minor,
            'micro'     => $patch,
            'stability' => $preRelease,
            'build'     => $build,
        ];
        $object = VersionFactory::fromJson(json_encode($data));

        self::assertInstanceOf('\BrowserDetector\Version\Version', $object);

        self::assertSame($major, $object->getMajor(), 'major is wrong');
        self::assertSame($minor, $object->getMinor(), 'minor is wrong');
        self::assertSame($patch, $object->getMicro(), 'patch is wrong');
        self::assertSame($preRelease, $object->getStability(), 'stability is wrong');
        self::assertSame($build, $object->getBuild(), 'build is wrong');
        self::assertTrue($object->isBeta());
        self::assertFalse($object->isAlpha());
    }
}
