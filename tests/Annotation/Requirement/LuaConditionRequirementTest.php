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

namespace Tarantool\PhpUnit\Tests\Annotation\Requirement;

use PHPUnit\Framework\TestCase;
use Tarantool\Client\Request\EvaluateRequest;
use Tarantool\PhpUnit\Annotation\Requirement\LuaConditionRequirement;
use Tarantool\PhpUnit\Client\ClientMocking;
use Tarantool\PhpUnit\Client\DummyFactory;

final class LuaConditionRequirementTest extends TestCase
{
    use ClientMocking;

    public function testCheckPassesForTruthyExpression() : void
    {
        $luaExpression = '1 == 1';

        $mockClient = $this->getMockClientBuilder()
            ->shouldHandle(
                new EvaluateRequest("return ($luaExpression)"),
                DummyFactory::createResponseFromData([true])
            )
            ->build();

        $requirement = new LuaConditionRequirement($mockClient);

        self::assertNull($requirement->check($luaExpression));
    }

    public function testCheckFailsForFalsyExpression() : void
    {
        $luaExpression = '1 == 2';

        $mockClient = $this->getMockClientBuilder()
            ->shouldHandle(
                new EvaluateRequest("return ($luaExpression)"),
                DummyFactory::createResponseFromData([false])
            )
            ->build();

        $errorMessage = sprintf('"%s" is not evaluated to true', $luaExpression);
        $requirement = new LuaConditionRequirement($mockClient);

        self::assertSame($errorMessage, $requirement->check($luaExpression));
    }
}
