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

namespace Tarantool\PhpUnit\Attribute\Processor;

use PHPUnitExtras\Attribute\PlaceholderResolver\PlaceholderResolver;
use PHPUnitExtras\Attribute\ProcessableAttribute;
use PHPUnitExtras\Attribute\Processor\Processor;
use PHPUnitExtras\Attribute\Target;
use Tarantool\Client\Client;
use Tarantool\PhpUnit\Attribute\Sql;

final class SqlProcessor implements Processor
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    #[\Override]
    public function getAttributeClasses() : array
    {
        return [Sql::class];
    }

    #[\Override]
    public function process(ProcessableAttribute $attribute, Target $target, PlaceholderResolver $placeholderResolver) : void
    {
        if (!$attribute instanceof Sql) {
            throw new \InvalidArgumentException('SqlProcessor only handles Sql attributes');
        }

        $this->client->executeUpdate($placeholderResolver->resolve($attribute->code, $target));
    }
}
