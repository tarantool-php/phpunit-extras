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
use Tarantool\Client\RequestTypes;

final class RequestCountContext implements ExpressionContext
{
    private const MAP = [
        'AUTH' => RequestTypes::AUTHENTICATE,
        'CALL' => RequestTypes::CALL,
        'DELETE' => RequestTypes::DELETE,
        'EVAL' => RequestTypes::EVALUATE,
        'EXECUTE' => RequestTypes::EXECUTE,
        'PREPARE' => RequestTypes::PREPARE,
        'INSERT' => RequestTypes::INSERT,
        'REPLACE' => RequestTypes::REPLACE,
        'SELECT' => RequestTypes::SELECT,
        'UPDATE' => RequestTypes::UPDATE,
        'UPSERT' => RequestTypes::UPSERT,
    ];

    /** @var RequestCounter */
    private $requestCounter;

    /** @var Client */
    private $client;

    /** @var string */
    private $requestName;

    /** @var string */
    private $expression;

    /** @var int */
    private $initialValue;

    /** @var int|null */
    private $finalValue;

    private function __construct(Client $client, RequestCounter $requestCounter, string $requestName, string $expression)
    {
        $this->requestCounter = $requestCounter;
        $this->client = $client->withMiddleware($this->requestCounter);
        $this->requestName = $requestName;
        $this->expression = $expression;
        $this->initialValue = $this->getValue();
    }

    public static function exactly(Client $client, RequestCounter $requestCounter, string $requestName, int $count) : self
    {
        return new self($client, $requestCounter, $requestName, "new_count === old_count + $count");
    }

    public static function atLeast(Client $client, RequestCounter $requestCounter, string $requestName, int $count) : self
    {
        return new self($client, $requestCounter, $requestName, "new_count >= old_count + $count");
    }

    public static function atMost(Client $client, RequestCounter $requestCounter, string $requestName, int $count) : self
    {
        return new self($client, $requestCounter, $requestName, "new_count <= old_count + $count");
    }

    public function getExpression() : string
    {
        return $this->expression;
    }

    public function getValues() : array
    {
        if (null === $this->finalValue) {
            $this->finalValue = $this->getValue();
        }

        return [
            'old_count' => $this->initialValue,
            'new_count' => $this->finalValue,
        ];
    }

    private function getValue() : int
    {
        $count = $this->client->evaluate("return box.stat().{$this->requestName}.total")[0];
        if (!isset(self::MAP[$this->requestName])) {
            return $count;
        }

        $requestType = self::MAP[$this->requestName];
        $clientExtraCount = $this->requestCounter->getCount($requestType);

        return $count - $clientExtraCount;
    }
}
