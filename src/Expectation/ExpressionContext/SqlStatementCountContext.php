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

namespace Tarantool\PhpUnit\Expectation\ExpressionContext;

use PHPUnitExtras\Expectation\ExpressionContext;
use Tarantool\Client\Client;

final class SqlStatementCountContext implements ExpressionContext
{
    /** @var Client */
    private $client;

    /** @var string */
    private $expression;

    /** @var int */
    private $initialValue;

    private function __construct(Client $client, string $expression)
    {
        $this->client = $client;
        $this->expression = $expression;
        $this->initialValue = $this->getValue();
    }

    public static function exactly(Client $client, int $count) : self
    {
        return new self($client, "new_count === old_count + $count");
    }

    public static function atLeast(Client $client, int $count) : self
    {
        return new self($client, "new_count >= old_count + $count");
    }

    public static function atMost(Client $client, int $count) : self
    {
        return new self($client, "new_count <= old_count + $count");
    }

    public function getExpression() : string
    {
        return $this->expression;
    }

    public function getValues() : array
    {
        return [
            'old_count' => $this->initialValue,
            'new_count' => $this->getValue(),
        ];
    }

    private function getValue() : int
    {
        return $this->client->evaluate('return box.info.sql().cache.stmt_count')[0];
    }
}
