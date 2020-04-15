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
use Tarantool\PhpUnit\Expectation\ExpressionContext\PreparedStatementCountContext;

trait PreparedStatementExpectations
{
    public function expectPreparedStatementToBeAllocated(int $count) : void
    {
        $context = PreparedStatementCountContext::exactly($this->getClient(), $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectPreparedStatementToBeDeallocated(int $count) : void
    {
        $context = PreparedStatementCountContext::exactly($this->getClient(), -$count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectPreparedStatementToBeAllocatedAtLeast(int $count) : void
    {
        $context = PreparedStatementCountContext::atLeast($this->getClient(), $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectPreparedStatementToBeDeallocatedAtLeast(int $count) : void
    {
        $context = PreparedStatementCountContext::atMost($this->getClient(), -$count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectPreparedStatementToBeAllocatedAtMost(int $count) : void
    {
        $context = PreparedStatementCountContext::atMost($this->getClient(), $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectPreparedStatementToBeDeallocatedAtMost(int $count) : void
    {
        $context = PreparedStatementCountContext::atLeast($this->getClient(), -$count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectPreparedStatementToBeAllocatedOnce() : void
    {
        $this->expectPreparedStatementToBeAllocated(1);
    }

    public function expectPreparedStatementToBeDeallocatedOnce() : void
    {
        $this->expectPreparedStatementToBeDeallocated(1);
    }

    public function expectPreparedStatementToBeNeverAllocated() : void
    {
        $this->expectPreparedStatementToBeAllocated(0);
    }

    public function expectPreparedStatementToBeNeverDeallocated() : void
    {
        $this->expectPreparedStatementToBeDeallocated(0);
    }

    public function expectPreparedStatementToBeAllocatedAtLeastOnce() : void
    {
        $this->expectPreparedStatementToBeAllocatedAtLeast(1);
    }

    public function expectPreparedStatementToBeDeallocatedAtLeastOnce() : void
    {
        $this->expectPreparedStatementToBeDeallocatedAtLeast(1);
    }

    public function expectPreparedStatementToBeAllocatedAtMostOnce() : void
    {
        $this->expectPreparedStatementToBeAllocatedAtMost(1);
    }

    public function expectPreparedStatementToBeDeallocatedAtMostOnce() : void
    {
        $this->expectPreparedStatementToBeDeallocatedAtMost(1);
    }

    abstract protected function expect(Expectation $expectation) : void;

    abstract protected function getClient() : Client;
}
