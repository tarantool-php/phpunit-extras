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

namespace Tarantool\PhpUnit\Tests\Expectation;

use PHPUnit\Framework\ExpectationFailedException;
use PHPUnitExtras\TestCase;
use Tarantool\Client\Client;
use Tarantool\Client\RequestTypes;
use Tarantool\PhpUnit\Client\TestDoubleClient;
use Tarantool\PhpUnit\Client\TestDoubleFactory;
use Tarantool\PhpUnit\Expectation\PreparedStatementExpectations;

final class PreparedStatementExpectationsTest extends TestCase
{
    use TestDoubleClient;
    use PreparedStatementExpectations;

    private $oldValue;
    private $newValue;

    protected function getClient() : Client
    {
        return $this->getTestDoubleClientBuilder()
            ->shouldHandle(
                RequestTypes::EVALUATE,
                TestDoubleFactory::createResponseFromData([$this->oldValue]),
                TestDoubleFactory::createResponseFromData([$this->newValue])
            )
            ->build();
    }

    public function testExpectToBeAllocatedSucceeds() : void
    {
        $this->oldValue = 1;
        $this->newValue = 3;
        $this->expectPreparedStatementToBeAllocated(2);
        $this->verifyExpectations();
    }

    public function testExpectToBeAllocatedFails() : void
    {
        $this->oldValue = 1;
        $this->newValue = 4;
        $this->expectPreparedStatementToBeAllocated(2);

        $this->expectException(ExpectationFailedException::class);
        $this->verifyExpectations();
    }

    public function testExpectToBeDeallocatedSucceeds() : void
    {
        $this->oldValue = 3;
        $this->newValue = 1;
        $this->expectPreparedStatementToBeDeallocated(2);
        $this->verifyExpectations();
    }

    public function testExpectToBeDeallocatedFails() : void
    {
        $this->oldValue = 4;
        $this->newValue = 1;
        $this->expectPreparedStatementToBeDeallocated(2);

        $this->expectException(ExpectationFailedException::class);
        $this->verifyExpectations();
    }

    public function testExpectToBeAllocatedAtLeastSucceeds() : void
    {
        $this->oldValue = 1;
        $this->newValue = 4;
        $this->expectPreparedStatementToBeAllocatedAtLeast(2);
        $this->verifyExpectations();
    }

    public function testExpectToBeAllocatedAtLeastFails() : void
    {
        $this->oldValue = 1;
        $this->newValue = 2;
        $this->expectPreparedStatementToBeAllocatedAtLeast(2);

        $this->expectException(ExpectationFailedException::class);
        $this->verifyExpectations();
    }

    public function testExpectToBeDeallocatedAtLeastSucceeds() : void
    {
        $this->oldValue = 4;
        $this->newValue = 1;
        $this->expectPreparedStatementToBeDeallocatedAtLeast(2);
        $this->verifyExpectations();
    }

    public function testExpectToBeDeallocatedAtLeastFails() : void
    {
        $this->oldValue = 2;
        $this->newValue = 1;
        $this->expectPreparedStatementToBeDeallocatedAtLeast(2);

        $this->expectException(ExpectationFailedException::class);
        $this->verifyExpectations();
    }

    public function testExpectToBeAllocatedAtMostSucceeds() : void
    {
        $this->oldValue = 1;
        $this->newValue = 2;
        $this->expectPreparedStatementToBeAllocatedAtMost(2);
        $this->verifyExpectations();
    }

    public function testExpectToBeAllocatedAtMostFails() : void
    {
        $this->oldValue = 1;
        $this->newValue = 4;
        $this->expectPreparedStatementToBeAllocatedAtMost(2);

        $this->expectException(ExpectationFailedException::class);
        $this->verifyExpectations();
    }

    public function testExpectToBeDeallocatedAtMostSucceeds() : void
    {
        $this->oldValue = 1;
        $this->newValue = 2;
        $this->expectPreparedStatementToBeDeallocatedAtMost(2);
        $this->verifyExpectations();
    }

    public function testExpectToBeDeallocatedAtMostFails() : void
    {
        $this->oldValue = 4;
        $this->newValue = 1;
        $this->expectPreparedStatementToBeDeallocatedAtMost(2);

        $this->expectException(ExpectationFailedException::class);
        $this->verifyExpectations();
    }

    public function testExpectToBeAllocatedOnceSucceeds() : void
    {
        $this->oldValue = 1;
        $this->newValue = 2;
        $this->expectPreparedStatementToBeAllocatedOnce();
        $this->verifyExpectations();
    }

    public function testExpectToBeAllocatedOnceFails() : void
    {
        $this->oldValue = 1;
        $this->newValue = 3;
        $this->expectPreparedStatementToBeAllocatedOnce();

        $this->expectException(ExpectationFailedException::class);
        $this->verifyExpectations();
    }

    public function testExpectToBeDeallocatedOnceSucceeds() : void
    {
        $this->oldValue = 2;
        $this->newValue = 1;
        $this->expectPreparedStatementToBeDeallocatedOnce();
        $this->verifyExpectations();
    }

    public function testExpectToBeDeallocatedOnceFails() : void
    {
        $this->oldValue = 3;
        $this->newValue = 1;
        $this->expectPreparedStatementToBeDeallocatedOnce();

        $this->expectException(ExpectationFailedException::class);
        $this->verifyExpectations();
    }

    public function testExpectToBeNeverAllocatedSucceeds() : void
    {
        $this->oldValue = 1;
        $this->newValue = 1;
        $this->expectPreparedStatementToBeNeverAllocated();
        $this->verifyExpectations();
    }

    public function testExpectToBeNeverAllocatedFails() : void
    {
        $this->oldValue = 1;
        $this->newValue = 2;
        $this->expectPreparedStatementToBeNeverAllocated();

        $this->expectException(ExpectationFailedException::class);
        $this->verifyExpectations();
    }

    public function testExpectToBeNeverDeallocatedSucceeds() : void
    {
        $this->oldValue = 1;
        $this->newValue = 1;
        $this->expectPreparedStatementToBeNeverDeallocated();
        $this->verifyExpectations();
    }

    public function testExpectToBeNeverDeallocatedFails() : void
    {
        $this->oldValue = 2;
        $this->newValue = 1;
        $this->expectPreparedStatementToBeNeverDeallocated();

        $this->expectException(ExpectationFailedException::class);
        $this->verifyExpectations();
    }

    public function testExpectToBeAllocatedAtLeastOnceSucceeds() : void
    {
        $this->oldValue = 1;
        $this->newValue = 3;
        $this->expectPreparedStatementToBeAllocatedAtLeastOnce();
        $this->verifyExpectations();
    }

    public function testExpectToBeAllocatedAtLeastOnceFails() : void
    {
        $this->oldValue = 1;
        $this->newValue = 1;
        $this->expectPreparedStatementToBeAllocatedAtLeastOnce();

        $this->expectException(ExpectationFailedException::class);
        $this->verifyExpectations();
    }

    public function testExpectToBeDeallocatedAtLeastOnceSucceeds() : void
    {
        $this->oldValue = 3;
        $this->newValue = 1;
        $this->expectPreparedStatementToBeDeallocatedAtLeastOnce();
        $this->verifyExpectations();
    }

    public function testExpectToBeDeallocatedAtLeastOnceFails() : void
    {
        $this->oldValue = 1;
        $this->newValue = 1;
        $this->expectPreparedStatementToBeDeallocatedAtLeastOnce();

        $this->expectException(ExpectationFailedException::class);
        $this->verifyExpectations();
    }

    public function testExpectToBeAllocatedAtMostOnceSucceeds() : void
    {
        $this->oldValue = 1;
        $this->newValue = 2;
        $this->expectPreparedStatementToBeAllocatedAtMostOnce();
        $this->verifyExpectations();
    }

    public function testExpectToBeAllocatedAtMostOnceFails() : void
    {
        $this->oldValue = 1;
        $this->newValue = 3;
        $this->expectPreparedStatementToBeAllocatedAtMostOnce();

        $this->expectException(ExpectationFailedException::class);
        $this->verifyExpectations();
    }

    public function testExpectToBeDeallocatedAtMostOnceSucceeds() : void
    {
        $this->oldValue = 2;
        $this->newValue = 1;
        $this->expectPreparedStatementToBeDeallocatedAtMostOnce();
        $this->verifyExpectations();
    }

    public function testExpectToBeDeallocatedAtMostOnceFails() : void
    {
        $this->oldValue = 3;
        $this->newValue = 1;
        $this->expectPreparedStatementToBeDeallocatedAtMostOnce();

        $this->expectException(ExpectationFailedException::class);
        $this->verifyExpectations();
    }
}
