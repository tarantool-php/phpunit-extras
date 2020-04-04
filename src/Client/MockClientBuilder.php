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

use Prophecy\Argument;
use Prophecy\Argument\Token\TokenInterface;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophet;
use Tarantool\Client\Client;
use Tarantool\Client\Connection\Connection;
use Tarantool\Client\Handler\Handler;
use Tarantool\Client\Packer\Packer;
use Tarantool\Client\Request\Request;
use Tarantool\Client\Response;

final class MockClientBuilder
{
    /** @var \Closure */
    private $prophesize;

    /** @var \SplObjectStorage<object, array<int, Response>> */
    private $requests;

    /** @var Connection|null */
    private $connection;

    /** @var Packer|null */
    private $packer;

    public function __construct(\Closure $prophesize)
    {
        $this->prophesize = $prophesize;
        $this->requests = new \SplObjectStorage();
    }

    public static function buildDefault() : Client
    {
        $self = new self(\Closure::fromCallable([new Prophet(), 'prophesize']));

        return $self->build();
    }

    /**
     * @param Request|TokenInterface $request
     * @param Response ...$responses
     */
    public function shouldHandle($request, ...$responses) : self
    {
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
        $handler = $this->createHandler()->reveal();

        return new Client($handler);
    }

    private function createHandler() : ObjectProphecy
    {
        $handler = ($this->prophesize)(Handler::class);

        $connection = $this->createConnection();
        $handler->getConnection()->willReturn($connection);

        $packer = $this->createPacker();
        $handler->getPacker()->willReturn($packer);

        $defaultResponse = DummyFactory::createEmptyResponse();

        if (!$this->requests->count()) {
            $handler->handle(Argument::type(Request::class))->willReturn($defaultResponse);

            return $handler;
        }

        foreach ($this->requests as $request) {
            if (!$responses = $this->requests->getInfo()) {
                $handler->handle($request)->willReturn($defaultResponse);
                continue;
            }

            $handler->handle($request)->willReturn(...$responses)
                ->shouldBeCalledTimes(\count($responses));
        }

        return $handler;
    }

    /**
     * @return Connection|ObjectProphecy
     */
    private function createConnection()
    {
        if ($this->connection) {
            return $this->connection;
        }

        return ($this->prophesize)(Connection::class);
    }

    /**
     * @return Packer|ObjectProphecy
     */
    private function createPacker()
    {
        if ($this->packer) {
            return $this->packer;
        }

        return ($this->prophesize)(Packer::class);
    }
}
