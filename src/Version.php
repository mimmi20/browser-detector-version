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

use function array_key_exists;
use function explode;
use function is_numeric;
use function str_contains;

final class Version implements VersionInterface
{
    /**
     * @param string      $major      the detected major version
     * @param string      $minor      the detected minor version
     * @param string      $micro      the detected micro version
     * @param string|null $patch      the detected patch version
     * @param string|null $micropatch the detected micropatch version
     *
     * @throws NotNumericException
     */
    public function __construct(
        private readonly string $major,
        private readonly string $minor = '0',
        private string $micro = '0',
        private string | null $patch = null,
        private string | null $micropatch = null,
        private readonly string $stability = 'stable',
        private readonly string | null $build = null,
    ) {
        if (!is_numeric($major) || '0' > $major) {
            throw new NotNumericException(
                'Major version must be a non-negative number formatted as string',
            );
        }

        if (!is_numeric($minor) || '0' > $minor) {
            throw new NotNumericException(
                'Minor version must be a non-negative number formatted as string',
            );
        }

        if (str_contains($micro, '.')) {
            $parts       = explode('.', $micro);
            $this->micro = $parts[0];

            if ($patch === null && array_key_exists(1, $parts)) {
                $this->patch      = $parts[1];
                $this->micropatch = $parts[2] ?? null;
            }
        }

        if (!is_numeric($this->micro) || '0' > $this->micro) {
            throw new NotNumericException(
                'Micro version must be a non-negative number formatted as string',
            );
        }
    }

    /**
     * @return array<string, string|null>
     *
     * @throws void
     */
    #[Override]
    public function toArray(): array
    {
        return [
            'major' => $this->major,
            'minor' => $this->minor,
            'micro' => $this->micro,
            'patch' => $this->patch,
            'micropatch' => $this->micropatch,
            'stability' => $this->stability,
            'build' => $this->build,
        ];
    }

    /** @throws void */
    #[Override]
    public function getMajor(): string
    {
        return $this->major;
    }

    /** @throws void */
    #[Override]
    public function getMinor(): string
    {
        return $this->minor;
    }

    /** @throws void */
    #[Override]
    public function getMicro(): string
    {
        return $this->micro;
    }

    /** @throws void */
    #[Override]
    public function getPatch(): string | null
    {
        return $this->patch;
    }

    /** @throws void */
    #[Override]
    public function getMicropatch(): string | null
    {
        return $this->micropatch;
    }

    /** @throws void */
    #[Override]
    public function getBuild(): string | null
    {
        return $this->build;
    }

    /** @throws void */
    #[Override]
    public function getStability(): string
    {
        return $this->stability;
    }

    /** @throws void */
    #[Override]
    public function isAlpha(): bool
    {
        return $this->stability === 'alpha';
    }

    /** @throws void */
    #[Override]
    public function isBeta(): bool
    {
        return $this->stability === 'beta';
    }

    /**
     * returns the detected version
     *
     * @throws void
     */
    #[Override]
    public function getVersion(int $mode = VersionInterface::COMPLETE): string
    {
        $versions     = $this->toArray();
        $microIsEmpty = false;

        if (VersionInterface::IGNORE_MICRO & $mode) {
            unset($versions['micro'], $versions['patch'], $versions['micropatch'], $versions['stability'], $versions['build']);
            $microIsEmpty = true;
        } elseif (
            (VersionInterface::IGNORE_MICRO_IF_EMPTY & $mode)
            || (VersionInterface::IGNORE_MINOR_IF_EMPTY & $mode)
            || (VersionInterface::IGNORE_MAJOR_IF_EMPTY & $mode)
        ) {
            if (empty($versions['micro']) || $versions['micro'] === '00') {
                $microIsEmpty = true;
            }

            if ($microIsEmpty && (VersionInterface::IGNORE_MICRO_IF_EMPTY & $mode)) {
                unset($versions['micro'], $versions['patch'], $versions['micropatch'], $versions['stability'], $versions['build']);
            }
        }

        $minorIsEmpty = false;

        if (VersionInterface::IGNORE_MINOR & $mode) {
            unset($versions['minor'], $versions['micro'], $versions['patch'], $versions['micropatch'], $versions['stability'], $versions['build']);
            $minorIsEmpty = true;
        } elseif (
            (VersionInterface::IGNORE_MINOR_IF_EMPTY & $mode)
            || (VersionInterface::IGNORE_MAJOR_IF_EMPTY & $mode)
        ) {
            if ($microIsEmpty && (empty($versions['minor']) || $versions['minor'] === '00')) {
                $minorIsEmpty = true;
            }

            if ($minorIsEmpty && (VersionInterface::IGNORE_MINOR_IF_EMPTY & $mode)) {
                unset($versions['minor'], $versions['micro'], $versions['patch'], $versions['micropatch'], $versions['stability'], $versions['build']);
            }
        }

        $macroIsEmpty = false;

        if (VersionInterface::IGNORE_MAJOR_IF_EMPTY & $mode) {
            if ($minorIsEmpty && (empty($versions['major']) || $versions['major'] === '00')) {
                $macroIsEmpty = true;
            }

            if ($macroIsEmpty) {
                unset($versions['major'], $versions['minor'], $versions['micro'], $versions['patch'], $versions['micropatch'], $versions['stability'], $versions['build']);
            }
        }

        if (!isset($versions['major'])) {
            if (VersionInterface::GET_ZERO_IF_EMPTY & $mode) {
                return '0';
            }

            return '';
        }

        return $versions['major']
            . (isset($versions['minor']) ? '.' . $versions['minor'] : '')
            . (isset($versions['micro']) ? '.' . $versions['micro'] . (isset($versions['patch']) ? '.' . $versions['patch'] . (isset($versions['micropatch']) ? '.' . $versions['micropatch'] : '') : '') : '')
            . (isset($versions['stability']) && $versions['stability'] !== 'stable' ? '-' . $versions['stability'] : '')
            . (isset($versions['build']) ? '+' . $versions['build'] : '');
    }
}
