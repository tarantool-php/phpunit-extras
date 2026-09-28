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

use PHPUnitExtras\Attribute\AttributeProcessorBuilder;
use PHPUnitExtras\Attribute\Attributes as BaseAttributes;
use Tarantool\Client\Client;
use Tarantool\PhpUnit\Attribute\Processor\LuaProcessor;
use Tarantool\PhpUnit\Attribute\Processor\SqlProcessor;
use Tarantool\PhpUnit\Attribute\Requirement\IfLuaRequirement;
use Tarantool\PhpUnit\Attribute\Requirement\TarantoolRequirement;

trait Attributes
{
    use BaseAttributes {
        BaseAttributes::createAttributeProcessorBuilder as createBaseAttributeProcessorBuilder;
    }

    protected function createAttributeProcessorBuilder() : AttributeProcessorBuilder
    {
        $client = $this->getClient();

        return $this->createBaseAttributeProcessorBuilder()
            ->addProcessor(new LuaProcessor($client))
            ->addProcessor(new SqlProcessor($client))
            ->addRequirement(new IfLuaRequirement($client))
            ->addRequirement(new TarantoolRequirement($client))
        ;
    }

    abstract protected function getClient() : Client;
}
