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
use Tarantool\PhpUnit\Client\ClientMocking;
use Tarantool\PhpUnit\Client\DummyFactory;
use Tarantool\PhpUnit\Expectation\RequestExpectations;

final class RequestExpectationsTest extends TestCase
{
    use ClientMocking;
    use RequestExpectations;

    private $oldValue;
    private $newValue;

    protected function getClient() : Client
    {
        // increase values for eval requests
        // to eliminate RequestCounter's count adjustments
        if ('Eval' === $this->getProvidedData()[0]) {
            ++$this->oldValue;
            $this->newValue += 2;
        }

        return $this->getMockClientBuilder()
            ->shouldHandle(
                RequestTypes::EVALUATE,
                DummyFactory::createResponseFromData([$this->oldValue]),
                DummyFactory::createResponseFromData([$this->newValue])
            )
            ->build();
    }

    public function provideRequestNames() : iterable
    {
        return [
            ['Auth'],
            ['Call'],
            ['Delete'],
            ['Eval'],
            ['Insert'],
            ['Prepare'],
            ['Replace'],
            ['Select'],
            ['Update'],
            ['Upsert'],
        ];
    }

    /**
     * @dataProvider provideRequestNames
     */
    public function testExpectToBeCalledSucceeds(string $requestName) : void
    {
        $this->oldValue = 1;
        $this->newValue = 3;
        $this->{"expect{$requestName}RequestToBeCalled"}(2);
        $this->verifyExpectations();
    }

    /**
     * @dataProvider provideRequestNames
     */
    public function testExpectToBeCalledFails(string $requestName) : void
    {
        $this->oldValue = 1;
        $this->newValue = 4;
        $this->{"expect{$requestName}RequestToBeCalled"}(2);

        $this->expectException(ExpectationFailedException::class);
        $this->verifyExpectations();
    }

    /**
     * @dataProvider provideRequestNames
     */
    public function testExpectToBeCalledAtLeastSucceeds(string $requestName) : void
    {
        $this->oldValue = 1;
        $this->newValue = 4;
        $this->{"expect{$requestName}RequestToBeCalledAtLeast"}(2);
        $this->verifyExpectations();
    }

    /**
     * @dataProvider provideRequestNames
     */
    public function testExpectToBeCalledAtLeastFails(string $requestName) : void
    {
        $this->oldValue = 1;
        $this->newValue = 2;
        $this->{"expect{$requestName}RequestToBeCalledAtLeast"}(2);

        $this->expectException(ExpectationFailedException::class);
        $this->verifyExpectations();
    }

    /**
     * @dataProvider provideRequestNames
     */
    public function testExpectToBeCalledAtMostSucceeds(string $requestName) : void
    {
        $this->oldValue = 1;
        $this->newValue = 2;
        $this->{"expect{$requestName}RequestToBeCalledAtMost"}(2);
        $this->verifyExpectations();
    }

    /**
     * @dataProvider provideRequestNames
     */
    public function testExpectToBeCalledAtMostFails(string $requestName) : void
    {
        $this->oldValue = 1;
        $this->newValue = 4;
        $this->{"expect{$requestName}RequestToBeCalledAtMost"}(2);

        $this->expectException(ExpectationFailedException::class);
        $this->verifyExpectations();
    }

    /**
     * @dataProvider provideRequestNames
     */
    public function testExpectToBeCalledOnceSucceeds(string $requestName) : void
    {
        $this->oldValue = 1;
        $this->newValue = 2;
        $this->{"expect{$requestName}RequestToBeCalledOnce"}();
        $this->verifyExpectations();
    }

    /**
     * @dataProvider provideRequestNames
     */
    public function testExpectToBeCalledOnceFails(string $requestName) : void
    {
        $this->oldValue = 1;
        $this->newValue = 3;
        $this->{"expect{$requestName}RequestToBeCalledOnce"}();

        $this->expectException(ExpectationFailedException::class);
        $this->verifyExpectations();
    }

    /**
     * @dataProvider provideRequestNames
     */
    public function testExpectToBeNeverCalledSucceeds(string $requestName) : void
    {
        $this->oldValue = 1;
        $this->newValue = 1;
        $this->{"expect{$requestName}RequestToBeNeverCalled"}();
        $this->verifyExpectations();
    }

    /**
     * @dataProvider provideRequestNames
     */
    public function testExpectToBeNeverCalledFails(string $requestName) : void
    {
        $this->oldValue = 1;
        $this->newValue = 2;
        $this->{"expect{$requestName}RequestToBeNeverCalled"}();

        $this->expectException(ExpectationFailedException::class);
        $this->verifyExpectations();
    }

    /**
     * @dataProvider provideRequestNames
     */
    public function testExpectToBeCalledAtLeastOnceSucceeds(string $requestName) : void
    {
        $this->oldValue = 1;
        $this->newValue = 3;
        $this->{"expect{$requestName}RequestToBeCalledAtLeastOnce"}();
        $this->verifyExpectations();
    }

    /**
     * @dataProvider provideRequestNames
     */
    public function testExpectToBeCalledAtLeastOnceFails(string $requestName) : void
    {
        $this->oldValue = 1;
        $this->newValue = 1;
        $this->{"expect{$requestName}RequestToBeCalledAtLeastOnce"}();

        $this->expectException(ExpectationFailedException::class);
        $this->verifyExpectations();
    }

    /**
     * @dataProvider provideRequestNames
     */
    public function testExpectToBeCalledAtMostOnceSucceeds(string $requestName) : void
    {
        $this->oldValue = 1;
        $this->newValue = 2;
        $this->{"expect{$requestName}RequestToBeCalledAtMostOnce"}();
        $this->verifyExpectations();
    }

    /**
     * @dataProvider provideRequestNames
     */
    public function testExpectToBeCalledAtMostOnceFails(string $requestName) : void
    {
        $this->oldValue = 1;
        $this->newValue = 3;
        $this->{"expect{$requestName}RequestToBeCalledAtMostOnce"}();

        $this->expectException(ExpectationFailedException::class);
        $this->verifyExpectations();
    }
}
