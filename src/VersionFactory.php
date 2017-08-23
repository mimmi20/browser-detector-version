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

        $regex = '/^' .
            'v?' .
            '(?:(\d+)[-|\.])?' .
            '(?:(\d+)[-|\.])?' .
            '(?:(\d+)[-|\.])?' .
            '(?:(\d+)\.)?' .
            '(?:(\d+))?' .
            '(?:' . VersionInterface::REGEX . ')?' .
            '$/i';

        if (preg_match($regex, $version, $matches)) {
            $numbers = self::mapMatches($matches);
        } else {
            // fallback - version string may include characters which are not allowed in the stability regex
            // -> use only the numbers
            $secondMatches = [];
            $secondRegex   = '/^' .
                'v?' .
                '(?:(\d+)[-|\.])?' .
                '(?:(\d+)[-|\.])?' .
                '(?:(\d+)[-|\.])?' .
                '(?:(\d+)\.)?' .
                '(?:(\d+))?' .
                '.*$/';

            if (!preg_match($secondRegex, $version, $secondMatches)) {
                return new Version();
            }

            $numbers = self::mapMatches($secondMatches);
        }

        if (empty($numbers)) {
            return new Version();
        }

        $major = (isset($numbers[0]) ? $numbers[0] : '0');
        $minor = (isset($numbers[1]) ? $numbers[1] : '0');

        if (isset($numbers[2])) {
            $patch = $numbers[2] . (isset($numbers[3]) ? '.' . $numbers[3] . (isset($numbers[4]) ? '.' . $numbers[4] : '') : '');
        } else {
            $patch = '0';
        }

        $stability = (!empty($matches['6'])) ? $matches['6'] : null;

        if (null === $stability || mb_strlen($stability) === 0) {
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

        $build = (!empty($matches['7'])) ? $matches['7'] : null;

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
        if (!is_array($searches) && !is_string($searches)) {
            throw new \UnexpectedValueException(
                'a string or an array of strings is expected as parameter'
            );
        }

        if (!is_array($searches)) {
            $searches = [$searches];
        }

        $modifiers = [
            ['\/', ''],
            ['\(', '\)'],
            [' ', ''],
            ['', ''],
            [' \(', '\;'],
        ];

        /** @var $version string */
        $version   = $default;

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
                $compareString = '/' . $search . $modifier[0] . '(\d+[\d\.\_\-\+abcdehlprstv]*)' . $modifier[1] . '/i';

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

        if (isset($matches[1]) && mb_strlen($matches[1]) > 0) {
            $numbers[] = $matches[1];
        }
        if (isset($matches[2]) && mb_strlen($matches[2]) > 0) {
            $numbers[] = $matches[2];
        }
        if (isset($matches[3]) && mb_strlen($matches[3]) > 0) {
            $numbers[] = $matches[3];
        }
        if (isset($matches[4]) && mb_strlen($matches[4]) > 0) {
            $numbers[] = $matches[4];
        }
        if (isset($matches[5]) && mb_strlen($matches[5]) > 0) {
            $numbers[] = $matches[5];
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
