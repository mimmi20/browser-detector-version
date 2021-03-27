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

final class NullVersion implements VersionInterface
{
    /**
     * @return array<string, int|string|null>
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

    public function getMajor(): ?string
    {
        return null;
    }

    public function getMinor(): ?string
    {
        return null;
    }

    public function getMicro(): ?string
    {
        return null;
    }

    public function getPatch(): ?string
    {
        return null;
    }

    public function getMicropatch(): ?string
    {
        return null;
    }

    public function getBuild(): ?string
    {
        return null;
    }

    public function getStability(): ?string
    {
        return null;
    }

    public function isAlpha(): ?bool
    {
        return null;
    }

    public function isBeta(): ?bool
    {
        return null;
    }

    /**
     * returns the detected version
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    public function getVersion(int $mode = VersionInterface::COMPLETE): ?string
    {
        return null;
    }
}
