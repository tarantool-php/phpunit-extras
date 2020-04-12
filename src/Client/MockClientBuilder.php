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

namespace Tarantool\PhpUnit\Client;

use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tarantool\Client\Client;
use Tarantool\Client\Connection\Connection;
use Tarantool\Client\Handler\Handler;
use Tarantool\Client\Packer\Packer;
use Tarantool\Client\Request\Request;
use Tarantool\Client\Response;

final class MockClientBuilder
{
    /** @var TestCase */
    private $testCase;

    /** @var array<int, Request>|null */
    private $requests;

    /** @var array<int, Response> */
    private $responses;

    /** @var Connection|null */
    private $connection;

    /** @var Packer|null */
    private $packer;

    /** @var int|null */
    private $shouldBeCalledTimes = null;

    public function __construct(TestCase $testCase)
    {
        $this->testCase = $testCase;
        $this->responses = [DummyFactory::createEmptyResponse()];
    }

    public static function buildDefault() : Client
    {
        /** @psalm-suppress PropertyNotSetInConstructor */
        $self = new self(new class() extends TestCase {
        });

        return $self->build();
    }

    /**
     * @param Request|Constraint|int $request
     * @param Request|Constraint|int ...$requests
     */
    public function shouldSend($request, ...$requests) : self
    {
        $this->requests = [];
        foreach (\func_get_args() as $arg) {
            $this->requests[] = \is_int($arg) ? new IsRequestType($arg) : $arg;
        }

        $this->shouldBeCalledTimes = \count($this->requests);

        return $this;
    }

    /**
     * @param Request|Constraint|int $request
     * @param Response ...$responses
     */
    public function shouldHandle($request, ...$responses) : self
    {
        $this->shouldSend($request);
        $this->willReceive(...$responses);

        if ($responses) {
            $this->shouldBeCalledTimes = \count($responses);
        }

        return $this;
    }

    public function willReceive(Response $response, Response ...$responses) : self
    {
        $this->responses = \func_get_args();

        return $this;
    }

    public function willUseConnection(Connection $connection) : self
    {
        $this->connection = $connection;

        return $this;
    }

    public function willUsePacker(Packer $packer) : self
    {
        $this->packer = $packer;

        return $this;
    }

    public function build() : Client
    {
        /** @var Handler $handler */
        $handler = $this->createHandler();

        return new Client($handler);
    }

    private function createHandler() : MockObject
    {
        $handler = $this->createMock(Handler::class);

        $connection = $this->createConnection();
        $handler->method('getConnection')->willReturn($connection);

        $packer = $this->createPacker();
        $handler->method('getPacker')->willReturn($packer);

        $handleMocker = null !== $this->shouldBeCalledTimes
            ? $handler->expects(TestCase::exactly($this->shouldBeCalledTimes))->method('handle')
            : $handler->method('handle');

        if ($this->requests) {
            $handleMocker->withConsecutive(...array_chunk($this->requests, 1));
        }

        if (1 === \count($this->responses)) {
            $handleMocker->willReturn($this->responses[0]);
        } else {
            $handleMocker->willReturnOnConsecutiveCalls(...$this->responses);
        }

        return $handler;
    }

    /**
     * @return Connection|MockObject
     */
    private function createConnection()
    {
        if ($this->connection) {
            return $this->connection;
        }

        return $this->createMock(Connection::class);
    }

    /**
     * @return Packer|MockObject
     */
    private function createPacker()
    {
        if ($this->packer) {
            return $this->packer;
        }

        return $this->createMock(Packer::class);
    }

    /**
     * @param class-string $originalClassName
     */
    private function createMock(string $originalClassName) : MockObject
    {
        return $this->testCase->getMockBuilder($originalClassName)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
    }
}
