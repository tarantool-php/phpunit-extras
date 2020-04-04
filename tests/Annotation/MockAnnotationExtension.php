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

namespace Tarantool\PhpUnit\Tests\Annotation;

use Tarantool\Client\Client;
use Tarantool\PhpUnit\Annotation\AnnotationExtension;
use Tarantool\PhpUnit\Client\MockClientBuilder;

final class MockAnnotationExtension extends AnnotationExtension
{
    public $resolvedDnsOrOptions;

    protected function getClient() : Client
    {
        $this->resolvedDnsOrOptions = $this->getClientConfig();

        return MockClientBuilder::buildDefault();
    }
}
