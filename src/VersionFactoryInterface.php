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
namespace BrowserDetector\Version;

/**
 * a general version detector factory
 *
 * @category  BrowserDetector
 *
 * @copyright 2012-2016 Thomas Mueller
 * @license   http://www.opensource.org/licenses/MIT MIT License
 */
interface VersionFactoryInterface
{
    /**
     * sets the detected version
     *
     * @param string $version
     *
     * @throws \UnexpectedValueException
     *
     * @return \BrowserDetector\Version\VersionInterface
     */
    public static function set(string $version): VersionInterface;

    /**
     * detects the bit count by this browser from the given user agent
     *
     * @param string $useragent
     * @param array  $searches
     * @param string $default
     *
     * @return \BrowserDetector\Version\Version
     */
    public static function detectVersion(string $useragent, array $searches = [], string $default = '0'): VersionInterface;

    /**
     * @param array $data
     *
     * @return \BrowserDetector\Version\VersionInterface
     */
    public static function fromArray(array $data): VersionInterface;

    /**
     * @param string $json
     *
     * @return \BrowserDetector\Version\VersionInterface
     */
    public static function fromJson(string $json): VersionInterface;
}
