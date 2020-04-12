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

use PHPUnitExtras\Expectation\ExpressionExpectation;
use Tarantool\Client\Client;
use Tarantool\PhpUnit\Expectation\ExpressionContext\SqlStatementCountContext;

trait SqlStatementExpectations
{
    public function expectSqlStatementToBeExecuted(int $count) : void
    {
        $context = SqlStatementCountContext::exactly($this->getClient(), $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectSqlStatementToBeExecutedAtLeast(int $count) : void
    {
        $context = SqlStatementCountContext::atLeast($this->getClient(), $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectSqlStatementToBeExecutedAtMost(int $count) : void
    {
        $context = SqlStatementCountContext::atMost($this->getClient(), $count);
        $this->expect(new ExpressionExpectation($context));
    }

    public function expectSqlStatementToBeExecutedOnce() : void
    {
        $this->expectSqlStatementToBeExecuted(1);
    }

    public function expectSqlStatementToBeNeverCalled() : void
    {
        $this->expectSqlStatementToBeExecuted(0);
    }

    public function expectSqlStatementToBeExecutedAtLeastOnce() : void
    {
        $this->expectSqlStatementToBeExecutedAtLeast(1);
    }

    public function expectSqlStatementToBeExecutedAtMostOnce() : void
    {
        $this->expectSqlStatementToBeExecutedAtMost(1);
    }

    abstract protected function getClient() : Client;
}
