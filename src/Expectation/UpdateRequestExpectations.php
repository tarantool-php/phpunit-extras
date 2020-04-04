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

trait UpdateRequestExpectations
{
    public function expectUpdateRequestToBeCalled(int $count) : void
    {
        $context = RequestCountContext::exactly($this->getClient(), $this->getRequestCounter(), 'UPDATE', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectUpdateRequestToBeCalledAtLeast(int $count) : void
    {
        $context = RequestCountContext::atLeast($this->getClient(), $this->getRequestCounter(), 'UPDATE', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectUpdateRequestToBeCalledAtMost(int $count) : void
    {
        $context = RequestCountContext::atMost($this->getClient(), $this->getRequestCounter(), 'UPDATE', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectUpdateRequestToBeCalledOnce() : void
    {
        $this->expectUpdateRequestToBeCalled(1);
    }

    public function expectUpdateRequestToBeNeverCalled() : void
    {
        $this->expectUpdateRequestToBeCalled(0);
    }

    public function expectUpdateRequestToBeCalledAtLeastOnce() : void
    {
        $this->expectUpdateRequestToBeCalledAtLeast(1);
    }

    public function expectUpdateRequestToBeCalledAtMostOnce() : void
    {
        $this->expectUpdateRequestToBeCalledAtMost(1);
    }

    abstract protected function expect(Expectation $expectation) : void;

    abstract protected function getClient() : Client;

    abstract protected function getRequestCounter() : RequestCounter;
}
