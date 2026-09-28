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

use Composer\Semver\Semver;
use PHPUnitExtras\Attribute\PlaceholderResolver\PlaceholderResolver;
use PHPUnitExtras\Attribute\ProcessableAttribute;
use PHPUnitExtras\Attribute\Requirement\Requirement;
use PHPUnitExtras\Attribute\Target;
use Tarantool\Client\Client;
use Tarantool\PhpUnit\Attribute\RequiresTarantoolVersion;

final class TarantoolVersionRequirement implements Requirement
{
    private Client $client;

    /** @var string|null */
    private $version;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    #[\Override]
    public function getAttributeClass() : string
    {
        return RequiresTarantoolVersion::class;
    }

    #[\Override]
    public function check(ProcessableAttribute $attribute, Target $target, PlaceholderResolver $placeholderResolver) : ?string
    {
        if (!$attribute instanceof RequiresTarantoolVersion) {
            throw new \InvalidArgumentException('TarantoolVersionRequirement only handles RequiresTarantoolVersion attributes');
        }
        $value = $placeholderResolver->resolve($attribute->constraint, $target);
        // Replace dash with dot.
        $constraints = (string) preg_replace('/(\d+\.\d+\.\d+)-(\d+)/', '$1.$2', $value);

        if (Semver::satisfies($this->getVersion(), $constraints)) {
            return null;
        }

        return \sprintf('Tarantool version %s is required', $value);
    }

    private function getVersion() : string
    {
        if (null !== $this->version) {
            return $this->version;
        }

        $version = $this->client->call('box.info')[0]['version'];
        if (!\is_string($version)) {
            throw new \UnexpectedValueException('Tarantool version must be a string');
        }

        // Normalize 2.2.1-3-g878e2a42c to 2.2.1.3.
        $version = (string) preg_replace('/-(\d+)-[^-]+$/', '.$1', $version);

        // Treat "entrypoint" versions as "dev",
        // so 2.11.0-entrypoint.8 becomes 2.11.0-dev+entrypoint.8.
        $version = (string) preg_replace('/(\d)-entrypoint/', '$1-dev+entrypoint', $version);

        return $this->version = $version;
    }
}
