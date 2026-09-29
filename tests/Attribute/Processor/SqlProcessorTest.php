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

namespace Tarantool\PhpUnit\Tests\Attribute\Processor;

use PHPUnit\Framework\TestCase;
use PHPUnitExtras\Attribute\PlaceholderResolver\PlaceholderResolver;
use PHPUnitExtras\Attribute\Target;
use Tarantool\Client\Keys;
use Tarantool\Client\Request\ExecuteRequest;
use Tarantool\PhpUnit\Attribute\Processor\SqlProcessor;
use Tarantool\PhpUnit\Attribute\Sql;
use Tarantool\PhpUnit\Client\TestDoubleClient;
use Tarantool\PhpUnit\Client\TestDoubleFactory;

final class SqlProcessorTest extends TestCase
{
    use TestDoubleClient;

    public function testProcessProcessesSqlStatement() : void
    {
        $sqlStatement = 'INSERT INTO foo VALUES (1)';

        $mockClient = $this->getTestDoubleClientBuilder()
            ->shouldHandle(
                ExecuteRequest::fromSql($sqlStatement),
                TestDoubleFactory::createResponse([Keys::SQL_INFO => []])
            )
            ->build();

        $processor = new SqlProcessor($mockClient);
        $processor->process(new Sql($sqlStatement), new Target(self::class), new class implements PlaceholderResolver {
            public function getName() : string
            {
                return 'identity';
            }

            public function resolve(string $value, Target $target) : string
            {
                return $value;
            }
        });
    }
}
