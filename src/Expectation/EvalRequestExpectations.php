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

trait EvalRequestExpectations
{
    public function expectEvalRequestToBeCalled(int $count) : void
    {
        $context = RequestCountContext::exactly($this->getClient(), $this->getRequestCounter(), 'EVAL', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectEvalRequestToBeCalledAtLeast(int $count) : void
    {
        $context = RequestCountContext::atLeast($this->getClient(), $this->getRequestCounter(), 'EVAL', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectEvalRequestToBeCalledAtMost(int $count) : void
    {
        $context = RequestCountContext::atMost($this->getClient(), $this->getRequestCounter(), 'EVAL', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectEvalRequestToBeCalledOnce() : void
    {
        $this->expectEvalRequestToBeCalled(1);
    }

    public function expectEvalRequestToBeNeverCalled() : void
    {
        $this->expectEvalRequestToBeCalled(0);
    }

    public function expectEvalRequestToBeCalledAtLeastOnce() : void
    {
        $this->expectEvalRequestToBeCalledAtLeast(1);
    }

    public function expectEvalRequestToBeCalledAtMostOnce() : void
    {
        $this->expectEvalRequestToBeCalledAtMost(1);
    }

    abstract protected function expect(Expectation $expectation) : void;

    abstract protected function getClient() : Client;

    abstract protected function getRequestCounter() : RequestCounter;
}
