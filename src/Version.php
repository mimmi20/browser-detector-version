<?php
/**
 * This file is part of the browser-detector-version package.
 *
 * Copyright (c) 2016-2019, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);
namespace BrowserDetector\Version;

final class Version implements VersionInterface
{
    /**
     * @var string the detected major version
     */
    private $major;

    /**
     * @var string the detected minor version
     */
    private $minor;

    /**
     * @var string the detected micro version
     */
    private $micro;

    /**
     * @var string|null the detected patch version
     */
    private $patch;

    /**
     * @var string|null the detected micropatch version
     */
    private $micropatch;

    /**
     * @var string
     */
    private $stability = 'stable';

    /**
     * @var string|null
     */
    private $build;

    /**
     * @param string      $major
     * @param string      $minor
     * @param string      $micro
     * @param string|null $patch
     * @param string|null $micropatch
     * @param string      $stability
     * @param string|null $build
     */
    public function __construct(string $major, string $minor = '0', string $micro = '0', ?string $patch = null, ?string $micropatch = null, string $stability = 'stable', ?string $build = null)
    {
        if (!is_numeric($major) || '0' > $major) {
            throw new \InvalidArgumentException('Major version must be a non-negative number formatted as string');
        }

        if (!is_numeric($minor) || '0' > $minor) {
            throw new \InvalidArgumentException('Minor version must be a non-negative number formatted as string');
        }

        if (false !== mb_strpos($micro, '.')) {
            $parts = explode('.', $micro);
            $micro = $parts[0];

            if (null === $patch && array_key_exists(1, $parts)) {
                $patch      = $parts[1];
                $micropatch = array_key_exists(2, $parts) ? $parts[2] : null;
            }
        }

        if (0 > (int) $micro || !is_numeric(str_replace('.', '', $micro))) {
            throw new \InvalidArgumentException('Patch version must be a non-negative number formatted as string');
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
     * @return array
     */
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

    /**
     * @return string
     */
    public function getMajor(): string
    {
        return $this->major;
    }

    /**
     * @return string
     */
    public function getMinor(): string
    {
        return $this->minor;
    }

    /**
     * @return string
     */
    public function getMicro(): string
    {
        return $this->micro;
    }

    /**
     * @return string|null
     */
    public function getPatch(): ?string
    {
        return $this->patch;
    }

    /**
     * @return string|null
     */
    public function getMicropatch(): ?string
    {
        return $this->micropatch;
    }

    /**
     * @return string|null
     */
    public function getBuild(): ?string
    {
        return $this->build;
    }

    /**
     * @return string
     */
    public function getStability(): string
    {
        return $this->stability;
    }

    /**
     * @return bool
     */
    public function isAlpha(): bool
    {
        return 'alpha' === $this->stability;
    }

    /**
     * @return bool
     */
    public function isBeta(): bool
    {
        return 'beta' === $this->stability;
    }

    /**
     * returns the detected version
     *
     * @param int $mode
     *
     * @return string
     */
    public function getVersion(int $mode = VersionInterface::COMPLETE): string
    {
        $versions     = $this->toArray();
        $microIsEmpty = false;

        if (VersionInterface::IGNORE_MICRO & $mode) {
            unset($versions['micro'], $versions['patch'], $versions['micropatch'], $versions['stability'], $versions['build']);
            $microIsEmpty = true;
        } elseif ((VersionInterface::IGNORE_MICRO_IF_EMPTY & $mode)
            || (VersionInterface::IGNORE_MINOR_IF_EMPTY & $mode)
            || (VersionInterface::IGNORE_MAJOR_IF_EMPTY & $mode)
        ) {
            if (empty($versions['micro']) || '00' === $versions['micro']) {
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
        } elseif ((VersionInterface::IGNORE_MINOR_IF_EMPTY & $mode)
            || (VersionInterface::IGNORE_MAJOR_IF_EMPTY & $mode)
        ) {
            if ($microIsEmpty && (empty($versions['minor']) || '00' === $versions['minor'])) {
                $minorIsEmpty = true;
            }

            if ($minorIsEmpty && (VersionInterface::IGNORE_MINOR_IF_EMPTY & $mode)) {
                unset($versions['minor'], $versions['micro'], $versions['patch'], $versions['micropatch'], $versions['stability'], $versions['build']);
            }
        }

        $macroIsEmpty = false;

        if (VersionInterface::IGNORE_MAJOR_IF_EMPTY & $mode) {
            if ($minorIsEmpty && (empty($versions['major']) || '00' === $versions['major'])) {
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
            . ((isset($versions['stability']) && 'stable' !== $versions['stability']) ? '-' . $versions['stability'] : '')
            . (isset($versions['build']) ? '+' . $versions['build'] : '');
    }
}
