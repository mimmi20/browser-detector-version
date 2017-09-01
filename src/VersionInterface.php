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
 * a general version detector
 *
 * @category  BrowserDetector
 *
 * @copyright 2012-2016 Thomas Mueller
 * @license   http://www.opensource.org/licenses/MIT MIT License
 */
interface VersionInterface
{
    /**
     * @var int
     */
    const COMPLETE = 0;

    /**
     * @var int
     */
    const IGNORE_MINOR = 1;

    /**
     * @var int
     */
    const IGNORE_MICRO = 2;

    /**
     * @var int
     */
    const IGNORE_MINOR_IF_EMPTY = 4;

    /**
     * @var int
     */
    const IGNORE_MICRO_IF_EMPTY = 8;

    /**
     * @var int
     */
    const IGNORE_MACRO_IF_EMPTY = 16;

    /**
     * @var int
     */
    const GET_ZERO_IF_EMPTY = 32;

    /**
     * @var string
     */
    const REGEX = '[-_\.]{0,1}(rc|a|alpha|b|beta|p|pl|patch|stable|dev|d)[\.+]{0,1}(\d*)';

    /**
     * returns the detected version
     *
     * @param int $mode
     *
     * @throws \UnexpectedValueException
     *
     * @return string
     */
    public function getVersion(int $mode = self::COMPLETE): string;

    /**
     * @return string[]
     */
    public function toArray(): array;

    /**
     * @return string
     */
    public function getMajor(): string;

    /**
     * @return string
     */
    public function getMinor(): string;

    /**
     * @return string
     */
    public function getMicro(): string;

    /**
     * @return string|null
     */
    public function getBuild(): ?string;

    /**
     * @return string
     */
    public function getStability(): string;

    /**
     * @return bool
     */
    public function isAlpha(): bool;

    /**
     * @return bool
     */
    public function isBeta(): bool;
}
