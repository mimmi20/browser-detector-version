<?php

/**
 * This file is part of the browser-detector-version package.
 *
 * Copyright (c) 2016-2026, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace BrowserDetector\Version;

use BrowserDetector\Version\Exception\NotNumericException;
use Override;

interface VersionBuilderInterface extends VersionFactoryInterface
{
    public const string REGEX = '/^v?(?P<major>\d+)(?:[-\.](?P<minor>\d+))?(?:[-\.](?P<micro>\d+))?(?:[-\.\(](?P<patch>\d+))?(?:[-\.](?P<micropatch>\d+))?(?:(?:[-_+~]?(?P<stability>rc|alpha|a|beta|b|patch|pre|pl?|stable|dev|d)[-_.+ \(]?| build |\+|[_\.]r)(?P<build>\d*))?.*$/i';

    /** @throws void */
    public function setRegex(string $regex): void;

    /**
     * sets the detected version
     *
     * @throws NotNumericException
     */
    public function set(string $version): VersionInterface;

    /**
     * @param array<int, bool|string|null> $searches
     *
     * @throws NotNumericException
     */
    #[Override]
    public function detectVersion(string $useragent, array $searches = []): VersionInterface;

    /**
     * @param array<string, string|null> $data
     *
     * @throws NotNumericException
     */
    public static function fromArray(array $data): VersionInterface;
}
