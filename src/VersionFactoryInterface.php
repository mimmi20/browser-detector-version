<?php
/**
 * This file is part of the browser-detector-version package.
 *
 * Copyright (c) 2016-2019, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);
namespace BrowserDetector\Version;

interface VersionFactoryInterface
{
    /**
     * @var string
     */
    public const REGEX = '/^v?(?<major>\d+)(?:[-\.](?<minor>\d+))?(?:[-\.](?<micro>\d+))?(?:[-\.](?<patch>\d+))?(?:[-\.](?<micropatch>\d+))?(?:(?:[-_+]?(?<stability>rc|alpha|a|beta|b|patch|pl?|stable|dev|d)[-_.+ ]?| build |\+|[_\.]r)(?<build>\d*))?.*$/i';

    /**
     * sets the detected version
     *
     * @param string $version
     *
     * @throws \UnexpectedValueException
     *
     * @return \BrowserDetector\Version\VersionInterface
     */
    public function set(string $version): VersionInterface;

    /**
     * detects the bit count by this browser from the given user agent
     *
     * @param string $useragent
     * @param array  $searches
     * @param string $default
     *
     * @return \BrowserDetector\Version\Version
     */
    public function detectVersion(string $useragent, array $searches = [], string $default = '0'): VersionInterface;

    /**
     * @param array $data
     *
     * @return \BrowserDetector\Version\VersionInterface
     */
    public static function fromArray(array $data): VersionInterface;
}
