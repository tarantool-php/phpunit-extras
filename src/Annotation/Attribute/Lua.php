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

namespace Tarantool\PhpUnit\Annotation\Attribute;

use PHPUnitExtras\Annotation\Attribute\AnnotationAttribute;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class Lua implements AnnotationAttribute
{
    private $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    #[\Override]
    public function getName() : string
    {
        return 'lua';
    }

    #[\Override]
    public function getValue() : string
    {
        return $this->code;
    }
}
