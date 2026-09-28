<?php
declare(strict_types=1);
namespace Tarantool\PhpUnit\Attribute;
use PHPUnitExtras\Attribute\ProcessableAttribute;
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class RequiresTarantoolVersion implements ProcessableAttribute
{
    public function __construct(public readonly string $constraint) { }
}
