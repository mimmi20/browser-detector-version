<?php
/**
 * This file is part of the browser-detector-version package.
 *
 * Copyright (c) 2016-2020, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);
namespace BrowserDetector\Version;

final class NullVersion implements VersionInterface
{
    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'major' => null,
            'minor' => null,
            'micro' => null,
            'patch' => null,
            'micropatch' => null,
            'stability' => null,
            'build' => null,
        ];
    }

    /**
     * @return string|null
     */
    public function getMajor(): ?string
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getMinor(): ?string
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getMicro(): ?string
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getPatch(): ?string
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getMicropatch(): ?string
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getBuild(): ?string
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getStability(): ?string
    {
        return null;
    }

    /**
     * @return bool|null
     */
    public function isAlpha(): ?bool
    {
        return null;
    }

    /**
     * @return bool|null
     */
    public function isBeta(): ?bool
    {
        return null;
    }

    /**
     * returns the detected version
     *
     * @param int $mode
     *
     * @return string|null
     */
    public function getVersion(int $mode = VersionInterface::COMPLETE): ?string
    {
        return null;
    }
}
