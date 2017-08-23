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
class Version implements VersionInterface
{
    /**
     * @var string the detected major version
     */
    private $major = null;

    /**
     * @var string the detected minor version
     */
    private $minor = null;

    /**
     * @var string the detected micro version
     */
    private $micro = null;

    /**
     * @var string
     */
    private $stability = 'stable';

    /**
     * @var string
     */
    private $build = null;

    /**
     * @param string      $major
     * @param string      $minor
     * @param string      $patch
     * @param string      $stability
     * @param string|null $build
     */
    public function __construct(string $major = '0', string $minor = '0', string $patch = '0', string $stability = 'stable', ?string $build = null)
    {
        if ((!is_int($major) && !is_string($major)) || $major < 0) {
            throw new \InvalidArgumentException('Major version must be a non-negative integer or a string');
        }
        if ((!is_int($minor) && !is_string($minor)) || $minor < 0) {
            throw new \InvalidArgumentException('Minor version must be a non-negative integer or a string');
        }
        if ((!is_int($patch) && !is_string($patch)) || $patch < 0) {
            throw new \InvalidArgumentException('Patch version must be a non-negative integer or a string');
        }

        $this->major     = (string) $major;
        $this->minor     = (string) $minor;
        $this->micro     = (string) $patch;
        $this->stability = $stability;
        $this->build     = $build;
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return [
            'major'     => $this->major,
            'minor'     => $this->minor,
            'micro'     => $this->micro,
            'stability' => $this->stability,
            'build'     => $this->build,
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
     * @return null|string
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
     * @throws \UnexpectedValueException
     *
     * @return string
     */
    public function getVersion(int $mode = VersionInterface::COMPLETE): string
    {
        $versions = $this->toArray();

        $microIsEmpty = false;

        if (VersionInterface::IGNORE_MICRO & $mode) {
            unset($versions['micro'], $versions['stability'], $versions['build']);
            $microIsEmpty = true;
        } elseif ((VersionInterface::IGNORE_MICRO_IF_EMPTY & $mode)
            || (VersionInterface::IGNORE_MINOR_IF_EMPTY & $mode)
            || (VersionInterface::IGNORE_MACRO_IF_EMPTY & $mode)
        ) {
            if (empty($versions['micro']) || in_array($versions['micro'], ['', '0', '00'])) {
                $microIsEmpty = true;
            }

            if ($microIsEmpty) {
                unset($versions['micro'], $versions['stability'], $versions['build']);
            }
        }

        $minorIsEmpty = false;

        if (VersionInterface::IGNORE_MINOR & $mode) {
            unset($versions['minor'], $versions['micro'], $versions['stability'], $versions['build']);
            $minorIsEmpty = true;
        } elseif ((VersionInterface::IGNORE_MINOR_IF_EMPTY & $mode)
            || (VersionInterface::IGNORE_MACRO_IF_EMPTY & $mode)
        ) {
            if ($microIsEmpty && (empty($versions['minor']) || in_array($versions['minor'], ['', '0', '00']))) {
                $minorIsEmpty = true;
            }

            if ($minorIsEmpty) {
                unset($versions['minor'], $versions['micro'], $versions['stability'], $versions['build']);
            }
        }

        $macroIsEmpty = false;

        if (VersionInterface::IGNORE_MACRO_IF_EMPTY & $mode) {
            if ($minorIsEmpty && (empty($versions['major']) || in_array($versions['major'], ['', '0', '00']))) {
                $macroIsEmpty = true;
            }

            if ($macroIsEmpty) {
                unset($versions['major'], $versions['minor'], $versions['micro'], $versions['stability'], $versions['build']);
            }
        }

        if (!isset($versions['major'])) {
            if (VersionInterface::GET_ZERO_IF_EMPTY & $mode) {
                return '0';
            }

            return '';
        }

        return $versions['major']
            . (isset($versions['minor']) ? '.' . (string) $versions['minor'] : '')
            . (isset($versions['micro']) ? '.' . (string) $versions['micro'] : '')
            . ((isset($versions['stability']) && 'stable' !== $versions['stability']) ? '-' . (string) $versions['stability'] : '')
            . (isset($versions['build']) ? '+' . (string) $versions['build'] : '');
    }
}
