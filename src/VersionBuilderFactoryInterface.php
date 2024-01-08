<?php
/**
 * This file is part of the browser-detector-version package.
 *
 * Copyright (c) 2016-2024, Thomas Mueller <mimmi20@live.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace BrowserDetector\Version;

use Psr\Log\LoggerInterface;

interface VersionBuilderFactoryInterface
{
    /** @throws void */
    public function __invoke(LoggerInterface $logger, string | null $regex = null): VersionBuilderInterface;
}
