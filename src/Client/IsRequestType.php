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
use Tarantool\Client\Request\Request;
use Tarantool\Client\RequestTypes;

final class IsRequestType extends Constraint
{
    /** @var int */
    private $requestType;

    public function __construct(int $requestType)
    {
        // needed for backward compatibility with PHPUnit 7
        if (\is_callable('parent::__construct')) {
            parent::__construct();
        }

        $this->requestType = $requestType;
    }

    public function toString() : string
    {
        return sprintf('is a "%s" request', strtoupper(RequestTypes::getName($this->requestType)));
    }

    protected function matches($other) : bool
    {
        return $other instanceof Request && $other->getType() === $this->requestType;
    }
}
