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

use function array_key_exists;
use function explode;
use function is_numeric;
use function mb_strpos;

final class Version implements VersionInterface
{
    private const STABLE     = 'stable';
    private const MAJOR      = 'major';
    private const MINOR      = 'minor';
    private const MICRO      = 'micro';
    private const PATCH      = 'patch';
    private const MICROPATCH = 'micropatch';
    private const STABILITY  = 'stability';
    private const BUILD      = 'build';
    private const CONST_00_  = '00';
    /** @var string the detected major version */
    private string $major;

    /** @var string the detected minor version */
    private string $minor;

    /** @var string the detected micro version */
    private string $micro;

    /** @var string|null the detected patch version */
    private ?string $patch = null;

    /** @var string|null the detected micropatch version */
    private ?string $micropatch = null;

    private string $stability = 'stable';

    private ?string $build = null;

    /**
     * @throws NotNumericException
     */
    public function __construct(string $major, string $minor = '0', string $micro = '0', ?string $patch = null, ?string $micropatch = null, string $stability = self::STABLE, ?string $build = null)
    {
        if (!is_numeric($major) || '0' > $major) {
            throw new NotNumericException('Major version must be a non-negative number formatted as string');
        }

        if (!is_numeric($minor) || '0' > $minor) {
            throw new NotNumericException('Minor version must be a non-negative number formatted as string');
        }

        if (false !== mb_strpos($micro, '.')) {
            $parts = explode('.', $micro);
            $micro = $parts[0];

            if (null === $patch && array_key_exists(1, $parts)) {
                $patch      = $parts[1];
                $micropatch = $parts[2] ?? null;
            }
        }

        if (!is_numeric($micro) || '0' > $micro) {
            throw new NotNumericException('Patch version must be a non-negative number formatted as string');
        }

        $this->major      = $major;
        $this->minor      = $minor;
        $this->micro      = $micro;
        $this->patch      = $patch;
        $this->micropatch = $micropatch;
        $this->stability  = $stability;
        $this->build      = $build;
    }

    /**
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            self::MAJOR => $this->major,
            self::MINOR => $this->minor,
            self::MICRO => $this->micro,
            self::PATCH => $this->patch,
            self::MICROPATCH => $this->micropatch,
            self::STABILITY => $this->stability,
            self::BUILD => $this->build,
        ];
    }

    public function getMajor(): string
    {
        return $this->major;
    }

    public function getMinor(): string
    {
        return $this->minor;
    }

    public function getMicro(): string
    {
        return $this->micro;
    }

    public function getPatch(): ?string
    {
        return $this->patch;
    }

    public function getMicropatch(): ?string
    {
        return $this->micropatch;
    }

    public function getBuild(): ?string
    {
        return $this->build;
    }

    public function getStability(): string
    {
        return $this->stability;
    }

    public function isAlpha(): bool
    {
        return 'alpha' === $this->stability;
    }

    public function isBeta(): bool
    {
        return 'beta' === $this->stability;
    }

    /**
     * returns the detected version
     */
    public function getVersion(int $mode = VersionInterface::COMPLETE): string
    {
        $versions     = $this->toArray();
        $microIsEmpty = false;

        if (0 !== (VersionInterface::IGNORE_MICRO & $mode)) {
            unset($versions[self::MICRO], $versions[self::PATCH], $versions[self::MICROPATCH], $versions[self::STABILITY], $versions[self::BUILD]);
            $microIsEmpty = true;
        } elseif (
            (VersionInterface::IGNORE_MICRO_IF_EMPTY & $mode)
            || (VersionInterface::IGNORE_MINOR_IF_EMPTY & $mode)
            || (VersionInterface::IGNORE_MAJOR_IF_EMPTY & $mode)
        ) {
            if (empty($versions[self::MICRO]) || self::CONST_00_ === $versions[self::MICRO]) {
                $microIsEmpty = true;
            }

            if ($microIsEmpty && (VersionInterface::IGNORE_MICRO_IF_EMPTY & $mode)) {
                unset($versions[self::MICRO], $versions[self::PATCH], $versions[self::MICROPATCH], $versions[self::STABILITY], $versions[self::BUILD]);
            }
        }

        $minorIsEmpty = false;

        if (0 !== (VersionInterface::IGNORE_MINOR & $mode)) {
            unset($versions[self::MINOR], $versions[self::MICRO], $versions[self::PATCH], $versions[self::MICROPATCH], $versions[self::STABILITY], $versions[self::BUILD]);
            $minorIsEmpty = true;
        } elseif (
            (VersionInterface::IGNORE_MINOR_IF_EMPTY & $mode)
            || (VersionInterface::IGNORE_MAJOR_IF_EMPTY & $mode)
        ) {
            if ($microIsEmpty && (empty($versions[self::MINOR]) || self::CONST_00_ === $versions[self::MINOR])) {
                $minorIsEmpty = true;
            }

            if ($minorIsEmpty && (VersionInterface::IGNORE_MINOR_IF_EMPTY & $mode)) {
                unset($versions[self::MINOR], $versions[self::MICRO], $versions[self::PATCH], $versions[self::MICROPATCH], $versions[self::STABILITY], $versions[self::BUILD]);
            }
        }

        $macroIsEmpty = false;

        if (0 !== (VersionInterface::IGNORE_MAJOR_IF_EMPTY & $mode)) {
            if ($minorIsEmpty && (empty($versions[self::MAJOR]) || self::CONST_00_ === $versions[self::MAJOR])) {
                $macroIsEmpty = true;
            }

            if ($macroIsEmpty) {
                unset($versions[self::MAJOR], $versions[self::MINOR], $versions[self::MICRO], $versions[self::PATCH], $versions[self::MICROPATCH], $versions[self::STABILITY], $versions[self::BUILD]);
            }
        }

        if (!isset($versions[self::MAJOR])) {
            if (0 !== (VersionInterface::GET_ZERO_IF_EMPTY & $mode)) {
                return '0';
            }

            return '';
        }

        return $versions[self::MAJOR]
            . (isset($versions[self::MINOR]) ? '.' . $versions[self::MINOR] : '')
            . (isset($versions[self::MICRO]) ? '.' . $versions[self::MICRO] . (isset($versions[self::PATCH]) ? '.' . $versions[self::PATCH] . (isset($versions[self::MICROPATCH]) ? '.' . $versions[self::MICROPATCH] : '') : '') : '')
            . (isset($versions[self::STABILITY]) && self::STABLE !== $versions[self::STABILITY] ? '-' . $versions[self::STABILITY] : '')
            . (isset($versions[self::BUILD]) ? '+' . $versions[self::BUILD] : '');
    }
}
