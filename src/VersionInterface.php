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
namespace BrowserDetector\Version;

interface VersionInterface
{
    /**
     * @var int
     */
    public const COMPLETE = 0;

    /**
     * @var int
     */
    public const IGNORE_MINOR = 1;

    /**
     * @var int
     */
    public const IGNORE_MICRO = 2;

    /**
     * @var int
     */
    public const IGNORE_MINOR_IF_EMPTY = 4;

    /**
     * @var int
     */
    public const IGNORE_MICRO_IF_EMPTY = 8;

    /**
     * @var int
     */
    public const IGNORE_MAJOR_IF_EMPTY = 16;

    /**
     * @var int
     */
    public const GET_ZERO_IF_EMPTY = 32;

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
     * @return array
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
    public function getPatch(): ?string;

    /**
     * @return string|null
     */
    public function getMicropatch(): ?string;

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
