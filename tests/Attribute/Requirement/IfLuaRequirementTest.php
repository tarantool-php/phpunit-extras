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

namespace Tarantool\PhpUnit\Tests\Attribute\Requirement;

use PHPUnit\Framework\TestCase;
use PHPUnitExtras\Attribute\PlaceholderResolver\PlaceholderResolver;
use PHPUnitExtras\Attribute\Target;
use Tarantool\Client\Request\EvaluateRequest;
use Tarantool\PhpUnit\Attribute\Requirement\IfLuaRequirement;
use Tarantool\PhpUnit\Attribute\RequiresIfLua;
use Tarantool\PhpUnit\Client\TestDoubleClient;
use Tarantool\PhpUnit\Client\TestDoubleFactory;

final class IfLuaRequirementTest extends TestCase
{
    use TestDoubleClient;

    public function testCheckPassesForTruthyExpression() : void
    {
        $luaExpression = '1 == 1';

        $mockClient = $this->getTestDoubleClientBuilder()
            ->shouldHandle(
                new EvaluateRequest("return ($luaExpression)"),
                TestDoubleFactory::createResponseFromData([true])
            )
            ->build();

        $requirement = new IfLuaRequirement($mockClient);

        self::assertNull($requirement->check(new RequiresIfLua($luaExpression), new Target(self::class), self::resolver()));
    }

    public function testCheckFailsForFalsyExpression() : void
    {
        $luaExpression = '1 == 2';

        $mockClient = $this->getTestDoubleClientBuilder()
            ->shouldHandle(
                new EvaluateRequest("return ($luaExpression)"),
                TestDoubleFactory::createResponseFromData([false])
            )
            ->build();

        $errorMessage = \sprintf('"%s" is not evaluated to true', $luaExpression);
        $requirement = new IfLuaRequirement($mockClient);

        self::assertSame($errorMessage, $requirement->check(new RequiresIfLua($luaExpression), new Target(self::class), self::resolver()));
    }

    private static function resolver() : PlaceholderResolver
    {
        return new class implements PlaceholderResolver {
            public function getName() : string
            {
                return 'identity';
            }

            public function resolve(string $value, Target $target) : string
            {
                return $value;
            }
        };
    }
}
