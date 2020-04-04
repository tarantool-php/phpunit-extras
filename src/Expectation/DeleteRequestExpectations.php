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

trait DeleteRequestExpectations
{
    public function expectDeleteRequestToBeCalled(int $count) : void
    {
        $context = RequestCountContext::exactly($this->getClient(), $this->getRequestCounter(), 'DELETE', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectDeleteRequestToBeCalledAtLeast(int $count) : void
    {
        $context = RequestCountContext::atLeast($this->getClient(), $this->getRequestCounter(), 'DELETE', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectDeleteRequestToBeCalledAtMost(int $count) : void
    {
        $context = RequestCountContext::atMost($this->getClient(), $this->getRequestCounter(), 'DELETE', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectDeleteRequestToBeCalledOnce() : void
    {
        $this->expectDeleteRequestToBeCalled(1);
    }

    public function expectDeleteRequestToBeNeverCalled() : void
    {
        $this->expectDeleteRequestToBeCalled(0);
    }

    public function expectDeleteRequestToBeCalledAtLeastOnce() : void
    {
        $this->expectDeleteRequestToBeCalledAtLeast(1);
    }

    public function expectDeleteRequestToBeCalledAtMostOnce() : void
    {
        $this->expectDeleteRequestToBeCalledAtMost(1);
    }

    abstract protected function expect(Expectation $expectation) : void;

    abstract protected function getClient() : Client;

    abstract protected function getRequestCounter() : RequestCounter;
}
