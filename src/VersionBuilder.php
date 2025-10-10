<?php

/**
 * This file is part of the browser-detector-version package.
 *
 * Copyright (c) 2016-2025, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace BrowserDetector\Version;

use BrowserDetector\Version\Exception\NotNumericException;
use Override;

use function array_key_exists;
use function assert;
use function is_string;
use function mb_strlen;
use function mb_strtolower;
use function mb_trim;
use function preg_match;
use function str_contains;
use function str_replace;
use function urldecode;

final class VersionBuilder implements VersionBuilderInterface
{
    private string $regex = VersionBuilderInterface::REGEX;

    /** @throws void */
    public function __construct(string | null $regex = null)
    {
        if ($regex === null) {
            return;
        }

        $this->setRegex($regex);
    }

    /** @throws void */
    #[Override]
    public function setRegex(string $regex): void
    {
        $this->regex = $regex;
    }

    /**
     * @throws void
     *
     * @api
     */
    public function getRegex(): string
    {
        return $this->regex;
    }

    /**
     * sets the detected version
     *
     * @throws NotNumericException
     */
    #[Override]
    public function set(string $version): VersionInterface
    {
        $matches = [];
        $numbers = [];

        if (preg_match($this->regex, $version, $matches)) {
            $numbers = $this->mapMatches($matches);
        }

        if ($numbers === []) {
            return new NullVersion();
        }

        $major = (array_key_exists('major', $numbers) ? $numbers['major'] : '0');
        $minor = (array_key_exists('minor', $numbers) ? $numbers['minor'] : '0');

        if (array_key_exists('micro', $numbers)) {
            $micro      = $numbers['micro'];
            $patch      = ($numbers['patch'] ?? null);
            $micropatch = ($numbers['micropatch'] ?? null);
        } else {
            $micro      = '0';
            $patch      = null;
            $micropatch = null;
        }

        $stability = $numbers['stability'] ?? null;

        if ($stability === null || mb_strlen($stability) === 0) {
            $stability = 'stable';
        }

        $stability = mb_strtolower($stability);

        switch ($stability) {
            case 'rc':
                $stability = 'RC';

                break;
            case 'pl':
            case 'p':
                $stability = 'patch';

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

        $build = $numbers['build'] ?? null;

        return new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);
    }

    /**
     * detects the bit count by this browser from the given user agent
     *
     * @param array<int, bool|string|null> $searches
     *
     * @throws NotNumericException
     */
    #[Override]
    public function detectVersion(string $useragent, array $searches = []): VersionInterface
    {
        $regexNumbersAndStability    = '(?P<version>\d+(?![:x])(?:[\d._\-+~ abdehprstv]|l(?!i)|c(?!fnetwork))*)';
        $regexNumbersOnly            = '(?P<version>\d+[\d.]+\(\d+(?![:x]))';
        $regexNumbersAndStabilityNot = '(?P<version>\d+[:x](?:[\d._\-+~ abdehprstv]|l(?!i)|c(?!fnetwork))*)';

        $modifiers = [
            '\/[\d.]+ ?\(',
            '\/',
            '\(',
            ' \(',
            ' ?',
        ];

        if (str_contains($useragent, '%')) {
            $useragent = urldecode($useragent);
        }

        foreach ($searches as $search) {
            if (!is_string($search)) {
                continue;
            }

            if (str_contains($search, '%')) {
                $search = urldecode($search);
            }

            $doMatch = preg_match(
                '/' . $search . '\/' . $regexNumbersOnly . '[;\)]/i',
                $useragent,
                $matches,
            );

            if ($doMatch) {
                $version = mb_strtolower(str_replace('_', '.', mb_trim($matches['version'])));

                return $this->set($version);
            }

            foreach ($modifiers as $modifier) {
                $compareStringNegative = '/' . $search . $modifier . $regexNumbersAndStabilityNot . '/i';
                $compareString         = '/' . $search . $modifier . $regexNumbersAndStability . '/i';
                $matches               = [];
                $doMatchNegative       = preg_match($compareStringNegative, $useragent);
                $doMatchPositive       = preg_match($compareString, $useragent, $matches);

                if (!$doMatchNegative && $doMatchPositive) {
                    $version = mb_strtolower(str_replace('_', '.', mb_trim($matches['version'])));

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
    #[Override]
    public static function fromArray(array $data): VersionInterface
    {
        assert(array_key_exists('major', $data), '"major" property is required');
        assert(array_key_exists('minor', $data), '"minor" property is required');
        assert(array_key_exists('micro', $data), '"micro" property is required');
        assert(array_key_exists('patch', $data), '"patch" property is required');
        assert(array_key_exists('micropatch', $data), '"micropatch" property is required');
        assert(array_key_exists('stability', $data), '"stability" property is required');
        assert(array_key_exists('build', $data), '"build" property is required');

        assert(is_string($data['major']));
        assert(is_string($data['minor']));
        assert(is_string($data['micro']));
        assert(is_string($data['stability']));

        $major      = $data['major'];
        $minor      = $data['minor'];
        $micro      = $data['micro'];
        $patch      = $data['patch'];
        $micropatch = $data['micropatch'];
        $stability  = $data['stability'];
        $build      = $data['build'];

        return new Version($major, $minor, $micro, $patch, $micropatch, $stability, $build);
    }

    /**
     * @param array<string, string> $matches
     *
     * @return array<string, string>
     *
     * @throws void
     */
    private function mapMatches(array $matches): array
    {
        $numbers = [];

        if (array_key_exists('major', $matches)) {
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
}
