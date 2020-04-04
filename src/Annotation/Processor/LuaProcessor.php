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

namespace Tarantool\PhpUnit\Annotation\Processor;

use PHPUnitExtras\Annotation\Processor\Processor;
use Tarantool\Client\Client;

final class LuaProcessor implements Processor
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getName() : string
    {
        return 'lua';
    }

    public function process(string $value) : void
    {
        $this->client->evaluate($value);
    }
}
