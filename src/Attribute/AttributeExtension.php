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

namespace Tarantool\PhpUnit\Attribute;

use PHPUnit\Exception;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;
use PHPUnitExtras\Attribute\AttributeExtension as BaseAttributeExtension;
use Tarantool\Client\Client;

/** @psalm-suppress ClassMustBeFinal This extension is intended to be extended. */
class AttributeExtension extends BaseAttributeExtension
{
    use Attributes;

    /** @var array<string, string|int|bool>|string */
    private $clientConfig = 'tcp://127.0.0.1:3301';

    /** @var Client|null */
    private $client;

    #[\Override]
    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters) : void
    {
        $this->parseParameters($parameters);
        parent::bootstrap($configuration, $facade, $parameters);
    }

    protected function parseParameters(ParameterCollection $parameters) : void
    {
        if ($parameters->has('dsn')) {
            $this->clientConfig = $parameters->get('dsn');
        } else {
            $closure = \Closure::bind(function () {
                /**
                 * @psalm-suppress InaccessibleProperty
                 * @var ParameterCollection $this
                 */
                return $this->parameters;
            }, $parameters, ParameterCollection::class);
            $options = $closure ? $closure() : [];

            if ([] !== $options) {
                $this->clientConfig = $options;
            }
        }
    }

    #[\Override]
    protected function getClient() : Client
    {
        if ($this->client) {
            return $this->client;
        }

        $config = $this->getClientConfig();

        return $this->client = \is_string($config)
            ? Client::fromDsn($config)
            : Client::fromOptions($config);
    }

    /**
     * @return array<string, string|int|bool>|string
     */
    final protected function getClientConfig(bool $resolveEnvVars = true)
    {
        if (!$resolveEnvVars) {
            return $this->clientConfig;
        }

        if (\is_string($this->clientConfig)) {
            return self::resolveEnvValues($this->clientConfig);
        }

        $clientConfig = $this->clientConfig;
        foreach ($clientConfig as $key => $value) {
            $clientConfig[$key] = \is_string($value) ? self::resolveEnvValues($value) : $value;
        }

        return $clientConfig;
    }

    private static function resolveEnvValues(string $configValue) : string
    {
        return (string) preg_replace_callback('/%env\((?P<name>.+?)\)%/', static function (array $matches) : string {
            if (false !== $value = getenv($matches['name'])) {
                return $value;
            }

            $errorMessage = \sprintf('Environment variable "%s" does not exist', $matches['name']);
            throw new class($errorMessage) extends \RuntimeException implements Exception { };
        }, $configValue);
    }
}
