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
use UnexpectedValueException;

interface VersionFactoryInterface
{
    /**
     * @throws NotNumericException
     * @throws UnexpectedValueException
     */
    public function detectVersion(string $useragent): VersionInterface;
}
