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

use Composer\Semver\Semver;
use PHPUnitExtras\Annotation\Requirement\Requirement;
use Tarantool\Client\Client;

final class TarantoolVersionRequirement implements Requirement
{
    private $client;

    /** @var string|null */
    private $version;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getName() : string
    {
        return 'Tarantool';
    }

    public function check(string $value) : ?string
    {
        // replace dash with dot
        $constraints = preg_replace('/(\d+\.\d+\.\d+)-(\d+)/', '$1.$2', $value);

        if (Semver::satisfies($this->getVersion(), $constraints)) {
            return null;
        }

        return sprintf('%s version %s is required', $this->getName(), $value);
    }

    private function getVersion() : string
    {
        if ($this->version) {
            return $this->version;
        }

        $version = $this->client->call('box.info')[0]['version'];
        // normalize 2.2.1-3-g878e2a42c to 2.2.1.3
        $version = preg_replace('/-(\d+)-[^-]+$/', '.$1', $version);

        return $this->version = $version;
    }
}
