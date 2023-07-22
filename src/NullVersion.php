<?php
/**
 * This file is part of the browser-detector-version package.
 *
 * Copyright (c) 2016-2023, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace BrowserDetector\Version;

final class NullVersion implements VersionInterface
{
    /**
     * @return array<string, string|null>
     *
     * @throws void
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

    /** @throws void */
    public function getMajor(): string | null
    {
        return null;
    }

    /** @throws void */
    public function getMinor(): string | null
    {
        return null;
    }

    /** @throws void */
    public function getMicro(): string | null
    {
        return null;
    }

    /** @throws void */
    public function getPatch(): string | null
    {
        return null;
    }

    /** @throws void */
    public function getMicropatch(): string | null
    {
        return null;
    }

    /** @throws void */
    public function getBuild(): string | null
    {
        return null;
    }

    /** @throws void */
    public function getStability(): string | null
    {
        return null;
    }

    /** @throws void */
    public function isAlpha(): bool | null
    {
        return null;
    }

    /** @throws void */
    public function isBeta(): bool | null
    {
        return null;
    }

    /**
     * returns the detected version
     *
     * @throws void
     *
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
     */
    public function getVersion(int $mode = VersionInterface::COMPLETE): string | null
    {
        return null;
    }
}
