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
use Tarantool\Client\Request\EvaluateRequest;
use Tarantool\PhpUnit\Attribute\Processor\LuaProcessor;
use Tarantool\PhpUnit\Attribute\Lua;
use PHPUnitExtras\Attribute\PlaceholderResolver\PlaceholderResolver;
use PHPUnitExtras\Attribute\Target;
use Tarantool\PhpUnit\Client\TestDoubleClient;
use Tarantool\PhpUnit\Client\TestDoubleFactory;

final class LuaProcessorTest extends TestCase
{
    use TestDoubleClient;

    public function testProcessProcessesLuaExpression() : void
    {
        $luaExpression = 'a = 42';

        $mockClient = $this->getTestDoubleClientBuilder()
            ->shouldHandle(
                new EvaluateRequest($luaExpression),
                TestDoubleFactory::createEmptyResponse()
            )
            ->build();

        $processor = new LuaProcessor($mockClient);
        $processor->process(new Lua($luaExpression), new Target(self::class), new class implements PlaceholderResolver {
            public function getName() : string { return 'identity'; }
            public function resolve(string $value, Target $target) : string { return $value; }
        });
    }
}
