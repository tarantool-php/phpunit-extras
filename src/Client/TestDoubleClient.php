<?php

/**
 * This file is part of the tarantool/phpunit-extras package.
 *
 * (c) Eugene Leonovich <gen.work@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tarantool\PhpUnit\Client;

use Tarantool\Client\Client;

trait TestDoubleClient
{
    protected function getTestDoubleClientBuilder() : TestDoubleClientBuilder
    {
        return new TestDoubleClientBuilder($this);
    }

    protected function createDummyClient() : Client
    {
        return $this->getTestDoubleClientBuilder()->build();
    }
}
