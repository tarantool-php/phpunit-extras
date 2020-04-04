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

trait PrepareRequestExpectations
{
    public function expectPrepareRequestToBeCalled(int $count) : void
    {
        $context = RequestCountContext::exactly($this->getClient(), $this->getRequestCounter(), 'PREPARE', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectPrepareRequestToBeCalledAtLeast(int $count) : void
    {
        $context = RequestCountContext::atLeast($this->getClient(), $this->getRequestCounter(), 'PREPARE', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectPrepareRequestToBeCalledAtMost(int $count) : void
    {
        $context = RequestCountContext::atMost($this->getClient(), $this->getRequestCounter(), 'PREPARE', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectPrepareRequestToBeCalledOnce() : void
    {
        $this->expectPrepareRequestToBeCalled(1);
    }

    public function expectPrepareRequestToBeNeverCalled() : void
    {
        $this->expectPrepareRequestToBeCalled(0);
    }

    public function expectPrepareRequestToBeCalledAtLeastOnce() : void
    {
        $this->expectPrepareRequestToBeCalledAtLeast(1);
    }

    public function expectPrepareRequestToBeCalledAtMostOnce() : void
    {
        $this->expectPrepareRequestToBeCalledAtMost(1);
    }

    abstract protected function expect(Expectation $expectation) : void;

    abstract protected function getClient() : Client;

    abstract protected function getRequestCounter() : RequestCounter;
}
