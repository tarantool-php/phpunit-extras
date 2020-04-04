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

trait AuthRequestExpectations
{
    public function expectAuthRequestToBeCalled(int $count) : void
    {
        $context = RequestCountContext::exactly($this->getClient(), $this->getRequestCounter(), 'AUTH', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectAuthRequestToBeCalledAtLeast(int $count) : void
    {
        $context = RequestCountContext::atLeast($this->getClient(), $this->getRequestCounter(), 'AUTH', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectAuthRequestToBeCalledAtMost(int $count) : void
    {
        $context = RequestCountContext::atMost($this->getClient(), $this->getRequestCounter(), 'AUTH', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectAuthRequestToBeCalledOnce() : void
    {
        $this->expectAuthRequestToBeCalled(1);
    }

    public function expectAuthRequestToBeNeverCalled() : void
    {
        $this->expectAuthRequestToBeCalled(0);
    }

    public function expectAuthRequestToBeCalledAtLeastOnce() : void
    {
        $this->expectAuthRequestToBeCalledAtLeast(1);
    }

    public function expectAuthRequestToBeCalledAtMostOnce() : void
    {
        $this->expectAuthRequestToBeCalledAtMost(1);
    }

    abstract protected function expect(Expectation $expectation) : void;

    abstract protected function getClient() : Client;

    abstract protected function getRequestCounter() : RequestCounter;
}
