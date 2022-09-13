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

namespace Tarantool\PhpUnit\Annotation;

use PHPUnitExtras\Annotation\AnnotationProcessorBuilder;
use PHPUnitExtras\Annotation\Annotations as BaseAnnotations;
use Tarantool\Client\Client;
use Tarantool\PhpUnit\Annotation\Processor\LuaProcessor;
use Tarantool\PhpUnit\Annotation\Processor\SqlProcessor;
use Tarantool\PhpUnit\Annotation\Requirement\LuaConditionRequirement;
use Tarantool\PhpUnit\Annotation\Requirement\TarantoolVersionRequirement;

trait Annotations
{
    use BaseAnnotations {
        BaseAnnotations::createAnnotationProcessorBuilder as createBaseAnnotationProcessorBuilder;
    }

    protected function createAnnotationProcessorBuilder() : AnnotationProcessorBuilder
    {
        $client = $this->getClient();

        return $this->createBaseAnnotationProcessorBuilder()
            ->addProcessor(new LuaProcessor($client))
            ->addProcessor(new SqlProcessor($client))
            ->addRequirement(new LuaConditionRequirement($client))
            ->addRequirement(new TarantoolVersionRequirement($client))
        ;
    }

    abstract protected function getClient() : Client;
}
