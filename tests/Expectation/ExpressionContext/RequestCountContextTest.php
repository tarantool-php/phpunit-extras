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

namespace Tarantool\PhpUnit\Tests\Expectation\ExpressionContext;

use PHPUnit\Framework\TestCase;
use PHPUnitExtras\Expectation\ExpressionContext;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Tarantool\Client\RequestTypes;
use Tarantool\PhpUnit\Client\ClientMocking;
use Tarantool\PhpUnit\Client\DummyFactory;
use Tarantool\PhpUnit\Expectation\ExpressionContext\RequestCountContext;
use Tarantool\PhpUnit\Expectation\ExpressionContext\RequestCounter;

final class RequestCountContextTest extends TestCase
{
    use ClientMocking;

    public function testGetValuesReturnsCorrectValues() : void
    {
        $mockClient = $this->getMockClientBuilder()
            ->shouldHandle(
                RequestTypes::EVALUATE,
                DummyFactory::createResponseFromData([2]),
                DummyFactory::createResponseFromData([3])
            )->build();

        $context = RequestCountContext::exactly($mockClient, new RequestCounter(), 'CALL', 1);

        self::assertSame(['old_count' => 2, 'new_count' => 3], $context->getValues());
    }

    public function testExactlyExpressionEvaluatesToTrue() : void
    {
        $context = RequestCountContext::exactly($this->createMockClient(), new RequestCounter(), 'CALL', 3);

        self::assertEvaluatedToTrue($context, ['old_count' => 1, 'new_count' => 4]);
    }

    public function testExactlyExpressionEvaluatesToFalse() : void
    {
        $context = RequestCountContext::exactly($this->createMockClient(), new RequestCounter(), 'CALL', 7);

        self::assertEvaluatedToFalse($context, ['old_count' => 1, 'new_count' => 4]);
    }

    public function testAtLeastExpressionEvaluatesToTrue() : void
    {
        $context = RequestCountContext::atLeast($this->createMockClient(), new RequestCounter(), 'CALL', 3);

        self::assertEvaluatedToTrue($context, ['old_count' => 1, 'new_count' => 4]);
    }

    public function testAtLeastExpressionEvaluatesToFalse() : void
    {
        $context = RequestCountContext::atLeast($this->createMockClient(), new RequestCounter(), 'CALL', 7);

        self::assertEvaluatedToFalse($context, ['old_count' => 1, 'new_count' => 4]);
    }

    public function testAtMostExpressionEvaluatesToTrue() : void
    {
        $context = RequestCountContext::atMost($this->createMockClient(), new RequestCounter(), 'CALL', 3);

        self::assertEvaluatedToTrue($context, ['old_count' => 1, 'new_count' => 4]);
    }

    public function testAtMostExpressionEvaluatesToFalse() : void
    {
        $context = RequestCountContext::atMost($this->createMockClient(), new RequestCounter(), 'CALL', 3);

        self::assertEvaluatedToFalse($context, ['old_count' => 1, 'new_count' => 7]);
    }

    private static function assertEvaluatedToTrue(ExpressionContext $context, array $values) : void
    {
        self::assertTrue((new ExpressionLanguage())->evaluate($context->getExpression(), $values));
    }

    private static function assertEvaluatedToFalse(ExpressionContext $context, array $values) : void
    {
        self::assertFalse((new ExpressionLanguage())->evaluate($context->getExpression(), $values));
    }
}
