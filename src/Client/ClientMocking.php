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

use Prophecy\Prophecy\ObjectProphecy;
use Tarantool\Client\Client;

trait ClientMocking
{
    protected function getMockClientBuilder() : MockClientBuilder
    {
        return new MockClientBuilder(\Closure::fromCallable([$this, 'prophesize']));
    }

    protected function createMockClient() : Client
    {
        return $this->getMockClientBuilder()->build();
    }

    abstract protected function prophesize(?string $classOrInterface = null) : ObjectProphecy;
}
