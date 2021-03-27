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

use UnexpectedValueException;

interface VersionInterface
{
    public const COMPLETE = 0;

    public const IGNORE_MINOR = 1;

    public const IGNORE_MICRO = 2;

    public const IGNORE_MINOR_IF_EMPTY = 4;

    public const IGNORE_MICRO_IF_EMPTY = 8;

    public const IGNORE_MAJOR_IF_EMPTY = 16;

    public const GET_ZERO_IF_EMPTY = 32;

    /**
     * returns the detected version
     *
     * @throws UnexpectedValueException
     */
    public function getVersion(int $mode = self::COMPLETE): ?string;

    /**
     * @return array<string, string|null>
     */
    public function toArray(): array;

    public function getMajor(): ?string;

    public function getMinor(): ?string;

    public function getMicro(): ?string;

    public function getPatch(): ?string;

    public function getMicropatch(): ?string;

    public function getBuild(): ?string;

    public function getStability(): ?string;

    public function isAlpha(): ?bool;

    public function isBeta(): ?bool;
}
