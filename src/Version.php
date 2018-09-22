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

class Version implements VersionInterface
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
     * @param string      $patch
     * @param string      $stability
     * @param string|null $build
     *
     * @throws \UnexpectedValueException
     */
    public function __construct(string $major = '0', string $minor = '0', string $patch = '0', string $stability = 'stable', ?string $build = null)
    {
        if (0 > (int) $major) {
            throw new \InvalidArgumentException('Major version must be a non-negative number formatted as string');
        }
        if (0 > (int) $minor) {
            throw new \InvalidArgumentException('Minor version must be a non-negative number formatted as string');
        }
        if (0 > (int) $patch) {
            throw new \InvalidArgumentException('Patch version must be a non-negative number formatted as string');
        }

        $this->major     = $major;
        $this->minor     = $minor;
        $this->micro     = $patch;
        $this->stability = $stability;
        $this->build     = $build;
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
            unset($versions['micro'], $versions['stability'], $versions['build']);
            $microIsEmpty = true;
        } elseif ((VersionInterface::IGNORE_MICRO_IF_EMPTY & $mode)
            || (VersionInterface::IGNORE_MINOR_IF_EMPTY & $mode)
            || (VersionInterface::IGNORE_MAJOR_IF_EMPTY & $mode)
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
            || (VersionInterface::IGNORE_MAJOR_IF_EMPTY & $mode)
        ) {
            if ($microIsEmpty && (empty($versions['minor']) || in_array($versions['minor'], ['', '0', '00']))) {
                $minorIsEmpty = true;
            }

            if ($minorIsEmpty) {
                unset($versions['minor'], $versions['micro'], $versions['stability'], $versions['build']);
            }
        }

        $macroIsEmpty = false;

        if (VersionInterface::IGNORE_MAJOR_IF_EMPTY & $mode) {
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
            . (isset($versions['minor']) ? '.' . $versions['minor'] : '')
            . (isset($versions['micro']) ? '.' . $versions['micro'] : '')
            . ((isset($versions['stability']) && 'stable' !== $versions['stability']) ? '-' . $versions['stability'] : '')
            . (isset($versions['build']) ? '+' . $versions['build'] : '');
    }
}
