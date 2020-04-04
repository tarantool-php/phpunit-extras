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

namespace Tarantool\PhpUnit\Expectation\ExpressionContext;

use Tarantool\Client\Handler\Handler;
use Tarantool\Client\Middleware\Middleware;
use Tarantool\Client\Request\Request;
use Tarantool\Client\Response;

final class RequestCounter implements Middleware
{
    /**
     * @var array<int, int>
     */
    private $requestCount = [];

    public function process(Request $request, Handler $handler) : Response
    {
        $response = $handler->handle($request);

        $type = $request->getType();
        $this->requestCount[$type] = ($this->requestCount[$type] ?? 0) + 1;

        return $response;
    }

    public function getCount(int $requestType) : int
    {
        return $this->requestCount[$requestType] ?? 0;
    }
}
