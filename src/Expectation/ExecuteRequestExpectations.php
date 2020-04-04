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

trait ExecuteRequestExpectations
{
    public function expectExecuteRequestToBeCalled(int $count) : void
    {
        $context = RequestCountContext::exactly($this->getClient(), $this->getRequestCounter(), 'EXECUTE', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectExecuteRequestToBeCalledAtLeast(int $count) : void
    {
        $context = RequestCountContext::atLeast($this->getClient(), $this->getRequestCounter(), 'EXECUTE', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectExecuteRequestToBeCalledAtMost(int $count) : void
    {
        $context = RequestCountContext::atMost($this->getClient(), $this->getRequestCounter(), 'EXECUTE', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectExecuteRequestToBeCalledOnce() : void
    {
        $this->expectExecuteRequestToBeCalled(1);
    }

    public function expectExecuteRequestToBeNeverCalled() : void
    {
        $this->expectExecuteRequestToBeCalled(0);
    }

    public function expectExecuteRequestToBeCalledAtLeastOnce() : void
    {
        $this->expectExecuteRequestToBeCalledAtLeast(1);
    }

    public function expectExecuteRequestToBeCalledAtMostOnce() : void
    {
        $this->expectExecuteRequestToBeCalledAtMost(1);
    }

    abstract protected function expect(Expectation $expectation) : void;

    abstract protected function getClient() : Client;

    abstract protected function getRequestCounter() : RequestCounter;
}
