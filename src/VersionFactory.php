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
use function assert;
use function is_string;
use function mb_strlen;
use function mb_strpos;
use function mb_strtolower;
use function preg_match;
use function str_replace;
use function urldecode;

final class VersionFactory implements VersionFactoryInterface
{
    private const MAJOR                       = 'major';
    private const MINOR                       = 'minor';
    private const MICRO                       = 'micro';
    private const PATCH                       = 'patch';
    private const MICROPATCH                  = 'micropatch';
    private const STABILITY                   = 'stability';
    private const BUILD                       = 'build';
    private const REGEX_NUMBERS_AND_STABILITY = '(?P<version>\d+(?!:)[\d._\-+~ abcdehlprstv]*)';
    private const REGEX_NUMBERS_ONLY          = '(?P<version>\d+[\d.]+\(\d+)';
    private string $regex                     = VersionFactoryInterface::REGEX;

    public function __construct(?string $regex = null)
    {
        if (null === $regex) {
            return;
        }

        $this->setRegex($regex);
    }

    public function setRegex(string $regex): void
    {
        $this->regex = $regex;
    }

    public function getRegex(): string
    {
        return $this->regex;
    }

    /**
     * sets the detected version
     *
     * @throws NotNumericException
     */
    public function set(string $version): VersionInterface
    {
        $matches = [];
        $numbers = [];

        if (preg_match($this->regex, $version, $matches)) {
            $numbers = $this->mapMatches($matches);
        }

        if ([] === $numbers) {
            return new NullVersion();
        }

        $major = (array_key_exists(self::MAJOR, $numbers) ? $numbers[self::MAJOR] : '0');
        $minor = (array_key_exists(self::MINOR, $numbers) ? $numbers[self::MINOR] : '0');

        if (array_key_exists(self::MICRO, $numbers)) {
            $micro      = $numbers[self::MICRO];
            $patch      = ($numbers[self::PATCH] ?? null);
            $micropatch = ($numbers[self::MICROPATCH] ?? null);
        } else {
            $micro      = '0';
            $patch      = null;
            $micropatch = null;
        }

        $stability = $numbers[self::STABILITY] ?? null;

        if (null === $stability || 0 === mb_strlen($stability)) {
            $stability = 'stable';
        }

        $stability = mb_strtolower($stability);
        switch ($stability) {
            case 'rc':
                $stability = 'RC';

                break;
            case 'pl':
            case 'p':
                $stability = self::PATCH;

                break;
            case 'b':
                $stability = 'beta';

                break;
            case 'a':
                $stability = 'alpha';

                break;
            case 'd':
            case 'pre':
                $stability = 'dev';

                break;
        }

        $build = $numbers[self::BUILD] ?? null;

        return new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);
    }

    /**
     * detects the bit count by this browser from the given user agent
     *
     * @param array<int, bool|string|null> $searches
     *
     * @throws NotNumericException
     */
    public function detectVersion(string $useragent, array $searches = []): VersionInterface
    {
        $modifiers = [
            '\/' . self::REGEX_NUMBERS_ONLY . '[;\)]',
            '\/[\d.]+ ?\(' . self::REGEX_NUMBERS_AND_STABILITY,
            '\/' . self::REGEX_NUMBERS_AND_STABILITY,
            '\(' . self::REGEX_NUMBERS_AND_STABILITY,
            ' \(' . self::REGEX_NUMBERS_AND_STABILITY,
            ' ?' . self::REGEX_NUMBERS_AND_STABILITY,
        ];

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
                $compareString = '/' . $search . $modifier . '/i';
                $matches       = [];
                $doMatch       = preg_match($compareString, $useragent, $matches);

                if ($doMatch) {
                    $version = mb_strtolower(str_replace('_', '.', $matches['version']));

                    return $this->set($version);
                }
            }
        }

        return new NullVersion();
    }

    /**
     * @param array<string, string|null> $data
     *
     * @throws NotNumericException
     */
    public static function fromArray(array $data): VersionInterface
    {
        assert(array_key_exists(self::MAJOR, $data), '"major" property is required');
        assert(array_key_exists(self::MINOR, $data), '"minor" property is required');
        assert(array_key_exists(self::MICRO, $data), '"micro" property is required');
        assert(array_key_exists(self::PATCH, $data), '"patch" property is required');
        assert(array_key_exists(self::MICROPATCH, $data), '"micropatch" property is required');
        assert(array_key_exists(self::STABILITY, $data), '"stability" property is required');
        assert(array_key_exists(self::BUILD, $data), '"build" property is required');

        assert(is_string($data[self::MAJOR]));
        assert(is_string($data[self::MINOR]));
        assert(is_string($data[self::MICRO]));
        assert(is_string($data[self::STABILITY]));

        $major      = $data[self::MAJOR];
        $minor      = $data[self::MINOR];
        $micro      = $data[self::MICRO];
        $patch      = $data[self::PATCH];
        $micropatch = $data[self::MICROPATCH];
        $stability  = $data[self::STABILITY];
        $build      = $data[self::BUILD];

        return new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);
    }

    /**
     * @param array<string, string> $matches
     *
     * @return array<string, string>
     */
    private function mapMatches(array $matches): array
    {
        $numbers = [];

        if (array_key_exists(self::MAJOR, $matches)) {
            $numbers[self::MAJOR] = $matches[self::MAJOR];
        }

        if (array_key_exists(self::MINOR, $matches) && mb_strlen($matches[self::MINOR])) {
            $numbers[self::MINOR] = $matches[self::MINOR];
        }

        if (array_key_exists(self::MICRO, $matches) && mb_strlen($matches[self::MICRO])) {
            $numbers[self::MICRO] = $matches[self::MICRO];
        }

        if (array_key_exists(self::PATCH, $matches) && mb_strlen($matches[self::PATCH])) {
            $numbers[self::PATCH] = $matches[self::PATCH];
        }

        if (array_key_exists(self::MICROPATCH, $matches) && mb_strlen($matches[self::MICROPATCH])) {
            $numbers[self::MICROPATCH] = $matches[self::MICROPATCH];
        }

        if (array_key_exists(self::STABILITY, $matches) && mb_strlen($matches[self::STABILITY])) {
            $numbers[self::STABILITY] = $matches[self::STABILITY];
        }

        if (array_key_exists(self::BUILD, $matches) && mb_strlen($matches[self::BUILD])) {
            $numbers[self::BUILD] = $matches[self::BUILD];
        }

        return $numbers;
    }
}
