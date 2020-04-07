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

    /** @var \SplObjectStorage<object, array<int, Response>> */
    private $requests;

    /** @var Connection|null */
    private $connection;

    /** @var Packer|null */
    private $packer;

    public function __construct(TestCase $testCase)
    {
        $this->testCase = $testCase;
        $this->requests = new \SplObjectStorage();
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
     * @param Response ...$responses
     */
    public function shouldHandle($request, ...$responses) : self
    {
        if (\is_int($request)) {
            $request = new IsRequestType($request);
        }

        $this->requests->attach($request, $responses);

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

        $defaultResponse = DummyFactory::createEmptyResponse();

        if (!$this->requests->count()) {
            $handler->method('handle')->willReturn($defaultResponse);

            return $handler;
        }

        foreach ($this->requests as $request) {
            if (!$responses = $this->requests->getInfo()) {
                $handler->method('handle')->with($request)->willReturn($defaultResponse);
                continue;
            }

            $handler->expects(TestCase::exactly(\count($responses)))
                ->method('handle')->with($request)
                ->willReturnOnConsecutiveCalls(...$responses);
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
