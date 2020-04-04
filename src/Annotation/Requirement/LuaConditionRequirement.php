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

namespace Tarantool\PhpUnit\Annotation\Requirement;

use PHPUnitExtras\Annotation\Requirement\Requirement;
use Tarantool\Client\Client;

final class LuaConditionRequirement implements Requirement
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getName() : string
    {
        return 'luaCondition';
    }

    public function check(string $value) : ?string
    {
        [$result] = $this->client->evaluate("return ($value)");

        if ($result) {
            return null;
        }

        return sprintf('"%s" is not evaluated to true', $value);
    }
}
