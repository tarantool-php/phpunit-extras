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

trait InsertRequestExpectations
{
    public function expectInsertRequestToBeCalled(int $count) : void
    {
        $context = RequestCountContext::exactly($this->getClient(), $this->getRequestCounter(), 'INSERT', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectInsertRequestToBeCalledAtLeast(int $count) : void
    {
        $context = RequestCountContext::atLeast($this->getClient(), $this->getRequestCounter(), 'INSERT', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectInsertRequestToBeCalledAtMost(int $count) : void
    {
        $context = RequestCountContext::atMost($this->getClient(), $this->getRequestCounter(), 'INSERT', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectInsertRequestToBeCalledOnce() : void
    {
        $this->expectInsertRequestToBeCalled(1);
    }

    public function expectInsertRequestToBeNeverCalled() : void
    {
        $this->expectInsertRequestToBeCalled(0);
    }

    public function expectInsertRequestToBeCalledAtLeastOnce() : void
    {
        $this->expectInsertRequestToBeCalledAtLeast(1);
    }

    public function expectInsertRequestToBeCalledAtMostOnce() : void
    {
        $this->expectInsertRequestToBeCalledAtMost(1);
    }

    abstract protected function expect(Expectation $expectation) : void;

    abstract protected function getClient() : Client;

    abstract protected function getRequestCounter() : RequestCounter;
}
