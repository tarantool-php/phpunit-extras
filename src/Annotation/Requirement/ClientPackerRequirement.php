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
use Tarantool\Client\Packer\PeclPacker;
use Tarantool\Client\Packer\PurePacker;

final class ClientPackerRequirement implements Requirement
{
    private const ALIASES = [
        'pure' => PurePacker::class,
        'pecl' => PeclPacker::class,
    ];

    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getName() : string
    {
        return 'clientPacker';
    }

    public function check(string $value) : ?string
    {
        $packer = $this->client->getHandler()->getPacker();

        if (isset(self::ALIASES[$value])) {
            $value = self::ALIASES[$value];
        }

        if (\get_class($packer) === ltrim($value, '\\')) {
            return null;
        }

        return sprintf('Client packer "%s" is required', $value);
    }
}
