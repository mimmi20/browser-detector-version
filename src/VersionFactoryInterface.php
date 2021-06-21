<?php
/**
 * This file is part of the browser-detector-version package.
 *
 * Copyright (c) 2016-2021, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace BrowserDetector\Version;

interface VersionFactoryInterface
{
    public const REGEX = '/^v?(?P<major>\d+)(?:[-\.](?P<minor>\d+))?(?:[-\.](?P<micro>\d+))?(?:[-\.\(](?P<patch>\d+))?(?:[-\.](?P<micropatch>\d+))?(?:(?:[-_+~]?(?P<stability>rc|alpha|a|beta|b|patch|pre|pl?|stable|dev|d)[-_.+ \(]?| build |\+|[_\.]r)(?P<build>\d*))?.*$/i';

    public function setRegex(string $regex): void;

    /**
     * sets the detected version
     *
     * @throws NotNumericException
     */
    public function set(string $version): VersionInterface;

    /**
     * detects the bit count by this browser from the given user agent
     *
     * @param array<int, bool|string|null> $searches
     *
     * @throws NotNumericException
     */
    public function detectVersion(string $useragent, array $searches = []): VersionInterface;

    /**
     * @param array<string, string|null> $data
     *
     * @throws NotNumericException
     */
    public static function fromArray(array $data): VersionInterface;
}
