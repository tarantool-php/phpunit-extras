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

trait SelectRequestExpectations
{
    public function expectSelectRequestToBeCalled(int $count) : void
    {
        $context = RequestCountContext::exactly($this->getClient(), $this->getRequestCounter(), 'SELECT', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectSelectRequestToBeCalledAtLeast(int $count) : void
    {
        $context = RequestCountContext::atLeast($this->getClient(), $this->getRequestCounter(), 'SELECT', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectSelectRequestToBeCalledAtMost(int $count) : void
    {
        $context = RequestCountContext::atMost($this->getClient(), $this->getRequestCounter(), 'SELECT', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectSelectRequestToBeCalledOnce() : void
    {
        $this->expectSelectRequestToBeCalled(1);
    }

    public function expectSelectRequestToBeNeverCalled() : void
    {
        $this->expectSelectRequestToBeCalled(0);
    }

    public function expectSelectRequestToBeCalledAtLeastOnce() : void
    {
        $this->expectSelectRequestToBeCalledAtLeast(1);
    }

    public function expectSelectRequestToBeCalledAtMostOnce() : void
    {
        $this->expectSelectRequestToBeCalledAtMost(1);
    }

    abstract protected function expect(Expectation $expectation) : void;

    abstract protected function getClient() : Client;

    abstract protected function getRequestCounter() : RequestCounter;
}
