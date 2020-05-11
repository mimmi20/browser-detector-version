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

final class VersionFactory implements VersionFactoryInterface
{
    /**
     * @var string
     */
    private $regex = VersionFactoryInterface::REGEX;

    /**
     * @param string|null $regex
     */
    public function __construct(?string $regex = null)
    {
        if (null !== $regex) {
            $this->setRegex($regex);
        }
    }

    /**
     * @param string $regex
     *
     * @return void
     */
    public function setRegex(string $regex): void
    {
        $this->regex = $regex;
    }

    /**
     * sets the detected version
     *
     * @param string $version
     *
     * @throws NotNumericException
     *
     * @return \BrowserDetector\Version\VersionInterface
     */
    public function set(string $version): VersionInterface
    {
        $matches = [];
        $numbers = [];

        if (0 < preg_match($this->regex, $version, $matches)) {
            $numbers = $this->mapMatches($matches);
        }

        if ([] === $numbers) {
            return new NullVersion();
        }

        $major = (array_key_exists('major', $numbers) ? $numbers['major'] : '0');
        $minor = (array_key_exists('minor', $numbers) ? $numbers['minor'] : '0');

        if (array_key_exists('micro', $numbers)) {
            $micro      = $numbers['micro'];
            $patch      = (array_key_exists('patch', $numbers) ? $numbers['patch'] : null);
            $micropatch = (array_key_exists('micropatch', $numbers) ? $numbers['micropatch'] : null);
        } else {
            $micro      = '0';
            $patch      = null;
            $micropatch = null;
        }

        $stability = (array_key_exists('stability', $numbers)) ? $numbers['stability'] : null;

        if (null === $stability || 0 === mb_strlen($stability)) {
            $stability = 'stable';
        }

        $stability = mb_strtolower($stability);
        switch ($stability) {
            case 'rc':
                $stability = 'RC';

                break;
            case 'patch':
            case 'pl':
            case 'p':
                $stability = 'patch';

                break;
            case 'beta':
            case 'b':
                $stability = 'beta';

                break;
            case 'alpha':
            case 'a':
                $stability = 'alpha';

                break;
            case 'dev':
            case 'd':
                $stability = 'dev';

                break;
        }

        $build = (array_key_exists('build', $numbers)) ? $numbers['build'] : null;

        return new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);
    }

    /**
     * detects the bit count by this browser from the given user agent
     *
     * @param string $useragent
     * @param array  $searches
     * @param string $default
     *
     * @throws NotNumericException
     *
     * @return \BrowserDetector\Version\VersionInterface
     */
    public function detectVersion(string $useragent, array $searches = [], string $default = '0'): VersionInterface
    {
        $modifiers = [
            ['\/', ''],
            ['\(', '\)'],
            [' ', ';'],
            [' ', ''],
            ['', ''],
            [' \(', ';'],
        ];

        $version = $default;

        if (false !== mb_strpos($useragent, '%')) {
            $useragent = urldecode($useragent);
        }

        foreach ($searches as $search) {
            if (!is_string($search)) {
                continue;
            }

            if (false !== mb_strpos($search, '%')) {
                $search = urldecode($search);
            }

            foreach ($modifiers as $modifier) {
                $compareString = '/' . $search . $modifier[0] . '(?P<version>\d+[\d._\-+~ abcdehlprstv]*)' . $modifier[1] . '/i';
                $matches       = [];
                $doMatch       = preg_match($compareString, $useragent, $matches);

                if (0 < $doMatch) {
                    $version = mb_strtolower(str_replace('_', '.', $matches['version']));

                    return $this->set($version);
                }
            }
        }

        return new NullVersion();
    }

    /**
     * @param array $matches
     *
     * @return array
     */
    private function mapMatches(array $matches): array
    {
        $numbers = [];

        if (array_key_exists('major', $matches) && mb_strlen($matches['major'])) {
            $numbers['major'] = $matches['major'];
        }
        if (array_key_exists('minor', $matches) && mb_strlen($matches['minor'])) {
            $numbers['minor'] = $matches['minor'];
        }
        if (array_key_exists('micro', $matches) && mb_strlen($matches['micro'])) {
            $numbers['micro'] = $matches['micro'];
        }
        if (array_key_exists('patch', $matches) && mb_strlen($matches['patch'])) {
            $numbers['patch'] = $matches['patch'];
        }
        if (array_key_exists('micropatch', $matches) && mb_strlen($matches['micropatch'])) {
            $numbers['micropatch'] = $matches['micropatch'];
        }
        if (array_key_exists('stability', $matches) && mb_strlen($matches['stability'])) {
            $numbers['stability'] = $matches['stability'];
        }
        if (array_key_exists('build', $matches) && mb_strlen($matches['build'])) {
            $numbers['build'] = $matches['build'];
        }

        return $numbers;
    }

    /**
     * @param array $data
     *
     * @throws NotNumericException
     *
     * @return \BrowserDetector\Version\VersionInterface
     */
    public static function fromArray(array $data): VersionInterface
    {
        assert(array_key_exists('major', $data), '"major" property is required');
        assert(array_key_exists('minor', $data), '"minor" property is required');
        assert(array_key_exists('micro', $data), '"micro" property is required');
        assert(array_key_exists('patch', $data), '"patch" property is required');
        assert(array_key_exists('micropatch', $data), '"micropatch" property is required');
        assert(array_key_exists('stability', $data), '"stability" property is required');
        assert(array_key_exists('build', $data), '"build" property is required');

        $major      = $data['major'];
        $minor      = $data['minor'];
        $micro      = $data['micro'];
        $patch      = $data['patch'];
        $micropatch = $data['micropatch'];
        $stability  = $data['stability'];
        $build      = $data['build'];

        return new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);
    }
}
