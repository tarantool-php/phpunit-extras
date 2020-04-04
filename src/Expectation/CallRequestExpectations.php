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

namespace Tarantool\PhpUnit\Expectation;

use PHPUnitExtras\Expectation\Expectation;
use PHPUnitExtras\Expectation\ExpressionExpectation;
use Tarantool\Client\Client;
use Tarantool\PhpUnit\Expectation\ExpressionContext\RequestCountContext;
use Tarantool\PhpUnit\Expectation\ExpressionContext\RequestCounter;

trait CallRequestExpectations
{
    public function expectCallRequestToBeCalled(int $count) : void
    {
        $context = RequestCountContext::exactly($this->getClient(), $this->getRequestCounter(), 'CALL', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectCallRequestToBeCalledAtLeast(int $count) : void
    {
        $context = RequestCountContext::atLeast($this->getClient(), $this->getRequestCounter(), 'CALL', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectCallRequestToBeCalledAtMost(int $count) : void
    {
        $context = RequestCountContext::atMost($this->getClient(), $this->getRequestCounter(), 'CALL', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectCallRequestToBeCalledOnce() : void
    {
        $this->expectCallRequestToBeCalled(1);
    }

    public function expectCallRequestToBeNeverCalled() : void
    {
        $this->expectCallRequestToBeCalled(0);
    }

    public function expectCallRequestToBeCalledAtLeastOnce() : void
    {
        $this->expectCallRequestToBeCalledAtLeast(1);
    }

    public function expectCallRequestToBeCalledAtMostOnce() : void
    {
        $this->expectCallRequestToBeCalledAtMost(1);
    }

    abstract protected function expect(Expectation $expectation) : void;

    abstract protected function getClient() : Client;

    abstract protected function getRequestCounter() : RequestCounter;
}
