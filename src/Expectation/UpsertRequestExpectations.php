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

trait UpsertRequestExpectations
{
    public function expectUpsertRequestToBeCalled(int $count) : void
    {
        $context = RequestCountContext::exactly($this->getClient(), $this->getRequestCounter(), 'UPSERT', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectUpsertRequestToBeCalledAtLeast(int $count) : void
    {
        $context = RequestCountContext::atLeast($this->getClient(), $this->getRequestCounter(), 'UPSERT', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectUpsertRequestToBeCalledAtMost(int $count) : void
    {
        $context = RequestCountContext::atMost($this->getClient(), $this->getRequestCounter(), 'UPSERT', $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectUpsertRequestToBeCalledOnce() : void
    {
        $this->expectUpsertRequestToBeCalled(1);
    }

    public function expectUpsertRequestToBeNeverCalled() : void
    {
        $this->expectUpsertRequestToBeCalled(0);
    }

    public function expectUpsertRequestToBeCalledAtLeastOnce() : void
    {
        $this->expectUpsertRequestToBeCalledAtLeast(1);
    }

    public function expectUpsertRequestToBeCalledAtMostOnce() : void
    {
        $this->expectUpsertRequestToBeCalledAtMost(1);
    }

    abstract protected function expect(Expectation $expectation) : void;

    abstract protected function getClient() : Client;

    abstract protected function getRequestCounter() : RequestCounter;
}
