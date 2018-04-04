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

/**
 * a general version detector factory
 *
 * @category  BrowserDetector
 *
 * @copyright 2012-2016 Thomas Mueller
 * @license   http://www.opensource.org/licenses/MIT MIT License
 */
class VersionFactory implements VersionFactoryInterface
{
    /**
     * sets the detected version
     *
     * @param string $version
     *
     * @throws \UnexpectedValueException
     *
     * @return \BrowserDetector\Version\VersionInterface
     */
    public static function set(string $version): VersionInterface
    {
        $stringMatches = [];

        if (preg_match('/^(xp|vista|nt|me)$/i', $version, $stringMatches)) {
            return new Version($stringMatches[1]);
        }

        $matches = [];
        $numbers = [];

        $regex = '/^' .
            'v?' .
            '(?<major>\d+)' .
            '(?:[-|\.](?<minor>\d+))?' .
            '(?:[-|\.](?<micro>\d+))?' .
            '(?:[-|\.](?<patch>\d+))?' .
            '(?:[-|\.](?<micropatch>\d+))?' .
            '(?:' . VersionInterface::REGEX . ')?' .
            '.*$/i';

        if (preg_match($regex, $version, $matches)) {
            $numbers = self::mapMatches($matches);
        }

        if (empty($numbers)) {
            return new Version();
        }

        $major = (isset($numbers['major']) ? $numbers['major'] : '0');
        $minor = (isset($numbers['minor']) ? $numbers['minor'] : '0');

        if (isset($numbers['micro'])) {
            $patch = $numbers['micro'];
            $patch .= (isset($numbers['patch']) ? '.' . $numbers['patch'] . (isset($numbers['micropatch']) ? '.' . $numbers['micropatch'] : '') : '');
        } else {
            $patch = '0';
        }

        $stability = (!empty($numbers['stability'])) ? $numbers['stability'] : null;

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

        $build = (!empty($numbers['build'])) ? $numbers['build'] : null;

        return new Version($major, $minor, $patch, $stability, $build);
    }

    /**
     * detects the bit count by this browser from the given user agent
     *
     * @param string $useragent
     * @param array  $searches
     * @param string $default
     *
     * @return \BrowserDetector\Version\VersionInterface
     */
    public static function detectVersion(string $useragent, array $searches = [], string $default = '0'): VersionInterface
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
                $compareString = '/' . $search . $modifier[0] . '(\d+[\d._\-+ abcdehlprstv]*)' . $modifier[1] . '/i';

                $doMatch = preg_match($compareString, $useragent, $matches);

                if ($doMatch) {
                    $version = mb_strtolower(str_replace('_', '.', $matches[1]));

                    break 2;
                }
            }
        }

        return self::set($version);
    }

    /**
     * @param array $matches
     *
     * @return array
     */
    private static function mapMatches(array $matches): array
    {
        $numbers = [];

        if (isset($matches['major']) && 0 < mb_strlen($matches['major'])) {
            $numbers['major'] = $matches['major'];
        }
        if (isset($matches['minor']) && 0 < mb_strlen($matches['minor'])) {
            $numbers['minor'] = $matches['minor'];
        }
        if (isset($matches['micro']) && 0 < mb_strlen($matches['micro'])) {
            $numbers['micro'] = $matches['micro'];
        }
        if (isset($matches['patch']) && 0 < mb_strlen($matches['patch'])) {
            $numbers['patch'] = $matches['patch'];
        }
        if (isset($matches['micropatch']) && 0 < mb_strlen($matches['micropatch'])) {
            $numbers['micropatch'] = $matches['micropatch'];
        }
        if (isset($matches['stability']) && 0 < mb_strlen($matches['stability'])) {
            $numbers['stability'] = $matches['stability'];
        }
        if (isset($matches['build']) && 0 < mb_strlen($matches['build'])) {
            $numbers['build'] = $matches['build'];
        }

        return $numbers;
    }

    /**
     * @param array $data
     *
     * @return \BrowserDetector\Version\VersionInterface
     */
    public static function fromArray(array $data): VersionInterface
    {
        $major     = isset($data['major']) ? $data['major'] : '0';
        $minor     = isset($data['minor']) ? $data['minor'] : '0';
        $micro     = isset($data['micro']) ? $data['micro'] : '0';
        $stability = isset($data['stability']) ? $data['stability'] : 'stable';
        $build     = isset($data['build']) ? $data['build'] : null;

        return new Version($major, $minor, $micro, $stability, $build);
    }

    /**
     * @param string $json
     *
     * @return \BrowserDetector\Version\VersionInterface
     */
    public static function fromJson(string $json): VersionInterface
    {
        return self::fromArray((array) json_decode($json));
    }
}
