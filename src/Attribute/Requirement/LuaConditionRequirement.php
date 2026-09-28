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

namespace Tarantool\PhpUnit\Attribute\Requirement;

use PHPUnitExtras\Attribute\PlaceholderResolver\PlaceholderResolver;
use PHPUnitExtras\Attribute\ProcessableAttribute;
use PHPUnitExtras\Attribute\Target;
use PHPUnitExtras\Attribute\Requirement\Requirement;
use Tarantool\PhpUnit\Attribute\RequiresLuaCondition;
use Tarantool\Client\Client;

final class LuaConditionRequirement implements Requirement
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    #[\Override]
    public function getAttributeClass() : string
    {
        return RequiresLuaCondition::class;
    }

    #[\Override]
    public function check(ProcessableAttribute $attribute, Target $target, PlaceholderResolver $placeholderResolver) : ?string
    {
        if (!$attribute instanceof RequiresLuaCondition) {
            throw new \InvalidArgumentException('LuaConditionRequirement only handles RequiresLuaCondition attributes');
        }
        $condition = $placeholderResolver->resolve($attribute->condition, $target);
        [$result] = $this->client->evaluate("return ($condition)");

        if ($result) {
            return null;
        }

        return \sprintf('"%s" is not evaluated to true', $condition);
    }
}
