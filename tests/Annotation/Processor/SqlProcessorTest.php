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

namespace Tarantool\PhpUnit\Tests\Annotation\Processor;

use PHPUnit\Framework\TestCase;
use Tarantool\Client\Keys;
use Tarantool\Client\Request\ExecuteRequest;
use Tarantool\PhpUnit\Annotation\Processor\SqlProcessor;
use Tarantool\PhpUnit\Client\ClientMocking;
use Tarantool\PhpUnit\Client\DummyFactory;

final class SqlProcessorTest extends TestCase
{
    use ClientMocking;

    public function testProcessProcessesSqlStatement() : void
    {
        $sqlStatement = 'INSERT INTO foo VALUES (1)';

        $mockClient = $this->getMockClientBuilder()
            ->shouldHandle(
                ExecuteRequest::fromSql($sqlStatement),
                DummyFactory::createResponse([Keys::SQL_INFO => []])
            )
            ->build();

        $processor = new SqlProcessor($mockClient);
        $processor->process($sqlStatement);
    }
}
