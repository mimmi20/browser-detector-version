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
use PHPUnit\Framework\TestCase;

final class VersionFactoryTest extends TestCase
{
    /**
     * @dataProvider providerSet
     * @group        version
     *
     * @param string      $version
     * @param string      $major
     * @param string      $minor
     * @param string      $micro
     * @param string      $stability
     * @param string|null $build
     * @param string      $complete
     *
     * @return void
     */
    public function testVersionSet(
        string $version,
        string $major,
        string $minor,
        string $micro,
        string $stability,
        ?string $build,
        string $complete
    ): void {
        $object = (new VersionFactory())->set($version);

        self::assertInstanceOf(Version::class, $object);

        self::assertSame($major, $object->getMajor(), 'major is wrong');
        self::assertSame($minor, $object->getMinor(), 'minor is wrong');
        self::assertSame($micro, $object->getMicro(), 'patch is wrong');
        self::assertSame($stability, $object->getStability(), 'stability is wrong');
        self::assertSame($build, $object->getBuild(), 'build is wrong');
        self::assertSame($complete, $object->getVersion(), 'complete is wrong');
    }

    /**
     * @return array[]
     */
    public function providerSet(): array
    {
        return [
            ['34.0.1760.0', '34', '0', '1760', 'stable', null, '34.0.1760.0'],
            ['3.9.0.0.22', '3', '9', '0', 'stable', null, '3.9.0.0.22'],
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
            ['2.0.0-patch+12', '2', '0', '0', 'patch', '12', '2.0.0-patch+12'],
            ['4.0.0-beta+8', '4', '0', '0', 'beta', '8', '4.0.0-beta+8'],
            ['4.0.0-alpha+1', '4', '0', '0', 'alpha', '1', '4.0.0-alpha+1'],
            ['3.4.3-dev-1191', '3', '4', '3', 'dev', '1191', '3.4.3-dev+1191'],
            ['3.4.3-dev+1191', '3', '4', '3', 'dev', '1191', '3.4.3-dev+1191'],
            ['1.4 build 2', '1', '4', '0', 'stable', '2', '1.4.0+2'],
            ['1.4.0+2', '1', '4', '0', 'stable', '2', '1.4.0+2'],
            ['2.3.1_r747', '2', '3', '1', 'stable', '747', '2.3.1+747'],
        ];
    }

    /**
     * @group version
     *
     * @return void
     */
    public function testVersionSetXp(): void
    {
        $object = (new VersionFactory())->set('XP');

        self::assertInstanceOf(Version::class, $object);

        self::assertSame('0', $object->getMajor(), 'major is wrong');
    }

    /**
     * @dataProvider providerDetectVersion
     * @group        version
     *
     * @param string      $uapart
     * @param array       $searches
     * @param string      $major
     * @param string      $minor
     * @param string      $patch
     * @param string      $preRelease
     * @param string|null $build
     * @param string      $complete
     *
     * @return void
     */
    public function testVersionDetectVersion(
        string $uapart,
        array $searches,
        string $major,
        string $minor,
        string $patch,
        string $preRelease,
        ?string $build,
        string $complete
    ): void {
        $object = (new VersionFactory())->detectVersion($uapart, $searches);

        self::assertInstanceOf(Version::class, $object);

        self::assertSame($major, $object->getMajor(), 'major is wrong');
        self::assertSame($minor, $object->getMinor(), 'minor is wrong');
        self::assertSame($patch, $object->getMicro(), 'patch is wrong');
        self::assertSame($preRelease, $object->getStability(), 'stability is wrong');
        self::assertSame($build, $object->getBuild(), 'build is wrong');
        self::assertSame($complete, $object->getVersion(), 'complete is wrong');
    }

    /**
     * @return array[]
     */
    public function providerDetectVersion(): array
    {
        return [
            ['Chrome/34.0.1760.0', ['Chrome'], '34', '0', '1760', 'stable', null, '34.0.1760.0'],
            ['Firefox/4.0b8', ['Firefox'], '4', '0', '0', 'beta', '8', '4.0.0-beta+8'],
            ['Firefox%20/4.0b8', ['Firefox%20'], '4', '0', '0', 'beta', '8', '4.0.0-beta+8'],
            ['Firefox/4.0b8', [null, false, 'Firefox'], '4', '0', '0', 'beta', '8', '4.0.0-beta+8'],
            ['Presto/2.8.119 320', ['Presto'], '2', '8', '119', 'stable', null, '2.8.119'],
            ['Mobicip/2.3.1_r747', ['Mobicip'], '2', '3', '1', 'stable', '747', '2.3.1+747'],
        ];
    }

    /**
     * @return void
     */
    public function testFromArray(): void
    {
        $major     = '4';
        $minor     = '0';
        $patch     = '0';
        $stability = 'beta';
        $build     = '8';

        $data = [
            'major' => $major,
            'minor' => $minor,
            'micro' => $patch,
            'stability' => $stability,
            'build' => $build,
        ];
        $object = VersionFactory::fromArray($data);

        self::assertInstanceOf(Version::class, $object);

        self::assertSame($major, $object->getMajor(), 'major is wrong');
        self::assertSame($minor, $object->getMinor(), 'minor is wrong');
        self::assertSame($patch, $object->getMicro(), 'patch is wrong');
        self::assertSame($stability, $object->getStability(), 'stability is wrong');
        self::assertSame($build, $object->getBuild(), 'build is wrong');
        self::assertTrue($object->isBeta());
        self::assertFalse($object->isAlpha());
    }

    /**
     * @return void
     */
    public function testWithParameter(): void
    {
        $regex     = '/^v?(?<major>\d+)(?:[-|\.](?<minor>\d+))?(?:[-|\.](?<micro>\d+))?(?:[-|\.](?<patch>\d+))?(?:[-|\.](?<micropatch>\d+))?(?:[-_.+ ]?(?<stability>rc|alpha|a|beta|b|patch|pl?|stable|dev|d)[-_.+ ]?(?<build>\d*))?.*$/i';
        $object    = new VersionFactory($regex);
        $useragent = 'Mozilla/4.0 (compatible; MSIE 10.0; Trident/6.0; Windows 98; MyIE2)';
        $result    = $object->detectVersion($useragent, ['MyIE']);
        self::assertInstanceOf(Version::class, $result);
        self::assertSame('2', $result->getMajor(), 'major is wrong');
    }
}
