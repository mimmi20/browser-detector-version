<?php
/**
 * This file is part of the browser-detector-version package.
 *
 * Copyright (c) 2016-2024, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace BrowserDetectorTest\Version;

use BrowserDetector\Version\NullVersion;
use BrowserDetector\Version\Version;
use BrowserDetector\Version\VersionBuilder;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

final class VersionBuilderTest extends TestCase
{
    /**
     * @throws ExpectationFailedException
     * @throws Exception
     * @throws InvalidArgumentException
     */
    #[DataProvider('providerSet')]
    public function testVersionSet(
        string $version,
        string $major,
        string $minor,
        string $micro,
        string $stability,
        string | null $build,
        string $complete,
    ): void {
        $object = (new VersionBuilder(new NullLogger()))->set($version);

        self::assertInstanceOf(Version::class, $object);

        self::assertSame($major, $object->getMajor(), 'major is wrong');
        self::assertSame($minor, $object->getMinor(), 'minor is wrong');
        self::assertSame($micro, $object->getMicro(), 'patch is wrong');
        self::assertSame($stability, $object->getStability(), 'stability is wrong');
        self::assertSame($build, $object->getBuild(), 'build is wrong');
        self::assertSame($complete, $object->getVersion(), 'complete is wrong');
    }

    /**
     * @return array<int, array<int, string|null>>
     *
     * @throws void
     */
    public static function providerSet(): array
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
            ['0.0.0', '0', '0', '0', 'stable', null, '0.0.0'],
            ['2.0p12', '2', '0', '0', 'patch', '12', '2.0.0-patch+12'],
            ['2.0.0-patch+12', '2', '0', '0', 'patch', '12', '2.0.0-patch+12'],
            ['2.0.0-pl+12', '2', '0', '0', 'patch', '12', '2.0.0-patch+12'],
            ['2.0.0-p+12', '2', '0', '0', 'patch', '12', '2.0.0-patch+12'],
            ['4.0.0-beta+8', '4', '0', '0', 'beta', '8', '4.0.0-beta+8'],
            ['4.0.0-b+8', '4', '0', '0', 'beta', '8', '4.0.0-beta+8'],
            ['4.0.0-alpha+1', '4', '0', '0', 'alpha', '1', '4.0.0-alpha+1'],
            ['4.0.0-a+1', '4', '0', '0', 'alpha', '1', '4.0.0-alpha+1'],
            ['3.4.3-dev-1191', '3', '4', '3', 'dev', '1191', '3.4.3-dev+1191'],
            ['3.4.3-d-1191', '3', '4', '3', 'dev', '1191', '3.4.3-dev+1191'],
            ['3.4.3-dev+1191', '3', '4', '3', 'dev', '1191', '3.4.3-dev+1191'],
            ['1.4 build 2', '1', '4', '0', 'stable', '2', '1.4.0+2'],
            ['1.4.0+2', '1', '4', '0', 'stable', '2', '1.4.0+2'],
            ['2.3.1_r747', '2', '3', '1', 'stable', '747', '2.3.1+747'],
            ['6~b1', '6', '0', '0', 'beta', '1', '6.0.0-beta+1'],
        ];
    }

    /**
     * @throws ExpectationFailedException
     * @throws Exception
     * @throws InvalidArgumentException
     */
    #[DataProvider('providerSetNull')]
    public function testNullVersionSet(string $version): void
    {
        $object = (new VersionBuilder(new NullLogger()))->set($version);

        self::assertInstanceOf(NullVersion::class, $object);

        self::assertNull($object->getMajor(), 'major is wrong');
        self::assertNull($object->getMinor(), 'minor is wrong');
        self::assertNull($object->getMicro(), 'micro is wrong');
        self::assertNull($object->getPatch(), 'patch is wrong');
        self::assertNull($object->getMicropatch(), 'micropatch is wrong');
        self::assertNull($object->getStability(), 'stability is wrong');
        self::assertNull($object->getBuild(), 'build is wrong');
        self::assertNull($object->getVersion(), 'complete is wrong');
        self::assertNull($object->isBeta(), 'beta is wrong');
        self::assertNull($object->isAlpha(), 'alpha is wrong');
    }

    /**
     * @return array<int, array<int, string|null>>
     *
     * @throws void
     */
    public static function providerSetNull(): array
    {
        return [
            ['abc'],
            ['x6~b1'],
            [''],
        ];
    }

    /**
     * @throws ExpectationFailedException
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testVersionSetXp(): void
    {
        $object = (new VersionBuilder(new NullLogger()))->set('XP');

        self::assertInstanceOf(NullVersion::class, $object);
        self::assertNull($object->getMajor(), 'major is wrong');
    }

    /**
     * @param array<int, (bool|string|null)> $searches
     *
     * @throws ExpectationFailedException
     * @throws Exception
     * @throws InvalidArgumentException
     */
    #[DataProvider('providerDetectVersion')]
    public function testVersionDetectVersion(
        string $uapart,
        array $searches,
        string $major,
        string $minor,
        string $micro,
        string $stability,
        string | null $build,
        string $complete,
    ): void {
        $object = (new VersionBuilder(new NullLogger()))->detectVersion($uapart, $searches);

        self::assertInstanceOf(Version::class, $object);

        self::assertSame($major, $object->getMajor(), 'major is wrong');
        self::assertSame($minor, $object->getMinor(), 'minor is wrong');
        self::assertSame($micro, $object->getMicro(), 'patch is wrong');
        self::assertSame($stability, $object->getStability(), 'stability is wrong');
        self::assertSame($build, $object->getBuild(), 'build is wrong');
        self::assertSame($complete, $object->getVersion(), 'complete is wrong');
    }

    /**
     * @return array<int, array<int, (array<int, (bool|string|null)>|string|null)>>
     *
     * @throws void
     */
    public static function providerDetectVersion(): array
    {
        return [
            ['Chrome/34.0.1760.0', ['Chrome'], '34', '0', '1760', 'stable', null, '34.0.1760.0'],
            ['Firefox/4.0b8', ['Firefox'], '4', '0', '0', 'beta', '8', '4.0.0-beta+8'],
            ['Firefox%20/4.0b8', ['Firefox%20'], '4', '0', '0', 'beta', '8', '4.0.0-beta+8'],
            ['Firefox/4.0b8', [null, false, 'Firefox'], '4', '0', '0', 'beta', '8', '4.0.0-beta+8'],
            ['Presto/2.8.119 320', ['Presto'], '2', '8', '119', 'stable', null, '2.8.119'],
            ['Mobicip/2.3.1_r747', ['Mobicip'], '2', '3', '1', 'stable', '747', '2.3.1+747'],
            ['BlackBerry9000/5.0.0.1079 Profile/MIDP-2.1 Configuration/CLDC-1.1 VendorID/114', ['BlackBerry[0-9a-z]+'], '5', '0', '0', 'stable', null, '5.0.0.1079'],
            ['Opera%20Coast/4.03.89212 CFNetwork/711.1.16 Darwin/14.0.0', ['OperaCoast', 'Opera%20Coast', 'Coast'], '4', '03', '89212', 'stable', null, '4.03.89212'],
            ['Firefox/4.0beta8', [null, false, 'Firefox'], '4', '0', '0', 'beta', '8', '4.0.0-beta+8'],
            ['Firefox/4.0a8', [null, false, 'Firefox'], '4', '0', '0', 'alpha', '8', '4.0.0-alpha+8'],
            ['Firefox/4.0alpha8', [null, false, 'Firefox'], '4', '0', '0', 'alpha', '8', '4.0.0-alpha+8'],
            ['Firefox/4.0d8', [null, false, 'Firefox'], '4', '0', '0', 'dev', '8', '4.0.0-dev+8'],
            ['Firefox/4.0dev8', [null, false, 'Firefox'], '4', '0', '0', 'dev', '8', '4.0.0-dev+8'],
            ['Firefox/4.0rc8', [null, false, 'Firefox'], '4', '0', '0', 'RC', '8', '4.0.0-RC+8'],
            ['Firefox/4.0p8', [null, false, 'Firefox'], '4', '0', '0', 'patch', '8', '4.0.0-patch+8'],
            ['Firefox/4.0pl8', [null, false, 'Firefox'], '4', '0', '0', 'patch', '8', '4.0.0-patch+8'],
            ['Firefox/4.0patch8', [null, false, 'Firefox'], '4', '0', '0', 'patch', '8', '4.0.0-patch+8'],
            ['Links (2.1pre23; Linux 3.5.0 i686; 237x63)', ['Links'], '2', '1', '0', 'dev', '23', '2.1.0-dev+23'],
            ['Outlook/15.0 (15.0.4691.1000; MSI; x86)', ['Outlook'], '15', '0', '4691', 'stable', null, '15.0.4691.1000'],
            ['Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1.20) Gecko/20081217 Firefox(2.0.0.20)', ['Firefox'], '2', '0', '0', 'stable', null, '2.0.0.20'],
            ['Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/1.1.1.0(29.0.1547.62) Safari/537.36', ['Chrome'], '29', '0', '1547', 'stable', null, '29.0.1547.62'],
            ['Dolphin http client/11.3.2(396) (Android)', ['Dolphin http client'], '11', '3', '2', 'stable', null, '11.3.2.396'],
            ['Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_0_1 like Mac OS X; en-us) AppleWebKit/537.4 (KHTML, like Gecko; Google Page Speed Insights) Version/4.0.5 Mobile/8A306 Safari/6531.22.7', ['CPU iPhone OS'], '4', '0', '1', 'stable', null, '4.0.1'],
            ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/600.2.5 (KHTML, like Gecko) Version/8.0.2 Safari/600.2.5 (Applebot/0.1)', ['Mac OS X'], '10', '10', '1', 'stable', null, '10.10.1'],
            ['Mozilla/5.0 (Macintosh; ARM Mac OS X) AppleWebKit/538.15 (KHTML, like Gecko) Safari/538.15 Version/6.0 Debian/8.0 (1:3.8.2.0-0) Epiphany/3.8.2', ['Debian'], '8', '0', '0', 'stable', null, '8.0.0'],
            ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/107.0.0.0 Safari/537.36 NetType/WIFI MicroMessenger/7.0.20.1781(0x6700143B) WindowsWechat(0x63090621)XWEB/8379 Flue', ['MicroMessenger'], '7', '0', '20', 'stable', null, '7.0.20.1781'],
            ['browseripad/42012 CFNetwork/711.3.18 Darwin/14.0.0', ['ipad'], '42012', '0', '0', 'stable', null, '42012.0.0'],
        ];
    }

    /**
     * @throws ExpectationFailedException
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testVersionDetectNullVersion(): void
    {
        $object = (new VersionBuilder(new NullLogger()))->detectVersion('Firefox/4.0b8', ['Chrome']);

        self::assertInstanceOf(NullVersion::class, $object);

        self::assertNull($object->getMajor(), 'major is wrong');
        self::assertNull($object->getMinor(), 'minor is wrong');
        self::assertNull($object->getMicro(), 'micro is wrong');
        self::assertNull($object->getPatch(), 'patch is wrong');
        self::assertNull($object->getMicropatch(), 'micropatch is wrong');
        self::assertNull($object->getStability(), 'stability is wrong');
        self::assertNull($object->getBuild(), 'build is wrong');
        self::assertNull($object->getVersion(), 'complete is wrong');
        self::assertNull($object->isBeta(), 'beta is wrong');
        self::assertNull($object->isAlpha(), 'alpha is wrong');
    }

    /**
     * @throws ExpectationFailedException
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testFromArray(): void
    {
        $major      = '4';
        $minor      = '0';
        $micro      = '0';
        $patch      = '0';
        $micropatch = null;
        $stability  = 'beta';
        $build      = '8';

        $data   = [
            'major' => $major,
            'minor' => $minor,
            'micro' => $micro,
            'patch' => $patch,
            'micropatch' => $micropatch,
            'stability' => $stability,
            'build' => $build,
        ];
        $object = VersionBuilder::fromArray($data);

        self::assertInstanceOf(Version::class, $object);

        self::assertSame($major, $object->getMajor(), 'major is wrong');
        self::assertSame($minor, $object->getMinor(), 'minor is wrong');
        self::assertSame($micro, $object->getMicro(), 'micro is wrong');
        self::assertSame($patch, $object->getPatch(), 'patch is wrong');
        self::assertNull($object->getMicropatch(), 'micropatch is wrong');
        self::assertSame($stability, $object->getStability(), 'stability is wrong');
        self::assertSame($build, $object->getBuild(), 'build is wrong');
        self::assertTrue($object->isBeta());
        self::assertFalse($object->isAlpha());
    }

    /**
     * @throws ExpectationFailedException
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testWithParameter(): void
    {
        $regex     = '/^v?(?<major>\d+)(?:[-|\.](?<minor>\d+))?(?:[-|\.](?<micro>\d+))?(?:[-|\.](?<patch>\d+))?(?:[-|\.](?<micropatch>\d+))?(?:[-_.+ ]?(?<stability>rc|alpha|a|beta|b|patch|pl?|stable|dev|d)[-_.+ ]?(?<build>\d*))?.*$/i';
        $object    = new VersionBuilder(new NullLogger(), $regex);
        $useragent = 'Mozilla/4.0 (compatible; MSIE 10.0; Trident/6.0; Windows 98; MyIE2)';

        $result = $object->detectVersion($useragent, ['MyIE']);

        self::assertInstanceOf(Version::class, $result);
        self::assertSame('2', $result->getMajor(), 'major is wrong');
        self::assertSame($regex, $object->getRegex());
    }

    /**
     * @throws ExpectationFailedException
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function testSetRegex(): void
    {
        $regex  = '/^v?(?<major>\d+)(?:[-|\.](?<minor>\d+))?(?:[-|\.](?<micro>\d+))?(?:[-|\.](?<patch>\d+))?(?:[-|\.](?<micropatch>\d+))?(?:[-_.+ ]?(?<stability>rc|alpha|a|beta|b|patch|pl?|stable|dev|d)[-_.+ ]?(?<build>\d*))?.*$/i';
        $object = new VersionBuilder(new NullLogger());
        self::assertNotSame($regex, $object->getRegex());
        $object->setRegex($regex);
        $useragent = 'Mozilla/4.0 (compatible; MSIE 10.0; Trident/6.0; Windows 98; MyIE2)';

        $result = $object->detectVersion($useragent, ['MyIE']);

        self::assertInstanceOf(Version::class, $result);
        self::assertSame('2', $result->getMajor(), 'major is wrong');
        self::assertSame($regex, $object->getRegex());
    }
}
