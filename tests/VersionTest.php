<?php

namespace BrowserDetectorTest\Version;

use BrowserDetector\Version\Version;
use BrowserDetector\Version\VersionFactory;

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

    public function testSerialize()
    {
        $major      = '4';
        $minor      = '0';
        $patch      = '0';
        $preRelease = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $patch, $preRelease, $build);

        $serialized = serialize($version);

        self::assertEquals($version, unserialize($serialized));
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

    public function testTojson()
    {
        $major      = '4';
        $minor      = '0';
        $patch      = '0';
        $preRelease = 'beta';
        $build      = '8';

        $version = new Version($major, $minor, $patch, $preRelease, $build);

        $json   = $version->toJson();
        $object = VersionFactory::fromJson($json);

        self::assertEquals($version, $object);
    }
}
