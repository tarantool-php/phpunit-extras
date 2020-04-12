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

namespace Tarantool\PhpUnit\Tests\Annotation\Requirement;

use PHPUnit\Framework\TestCase;
use Tarantool\Client\Packer\Packer;
use Tarantool\Client\Packer\PeclPacker;
use Tarantool\Client\Packer\PurePacker;
use Tarantool\PhpUnit\Annotation\Requirement\ClientPackerRequirement;
use Tarantool\PhpUnit\Client\ClientMocking;

final class ClientPackerRequirementTest extends TestCase
{
    use ClientMocking;

    /**
     * @dataProvider provideCheckPassesForMatchedPackerData()
     */
    public function testCheckPassesForMatchedPacker(Packer $packer, string $requiredPackerName) : void
    {
        $mockClient = $this->getMockClientBuilder()
            ->willUsePacker($packer)
            ->build();

        $requirement = new ClientPackerRequirement($mockClient);

        self::assertNull($requirement->check($requiredPackerName));
    }

    public function provideCheckPassesForMatchedPackerData() : iterable
    {
        return [
            [new PurePacker(), 'pure'],
            [new PurePacker(), PurePacker::class],
            [new PeclPacker(), 'pecl'],
            [new PeclPacker(), PeclPacker::class],
        ];
    }

    /**
     * @dataProvider provideCheckFailsForMismatchedPackerData()
     */
    public function testCheckFailsForMismatchedPacker(Packer $packer, string $requiredPackerName, string $expectedPackerClass) : void
    {
        $mockClient = $this->getMockClientBuilder()
            ->willUsePacker($packer)
            ->build();

        $requirement = new ClientPackerRequirement($mockClient);
        $errorMessage = sprintf('Client packer "%s" is required', $expectedPackerClass);

        self::assertSame($errorMessage, $requirement->check($requiredPackerName));
    }

    public function provideCheckFailsForMismatchedPackerData() : iterable
    {
        return [
            [new PurePacker(), 'pecl', PeclPacker::class],
            [new PurePacker(), PeclPacker::class, PeclPacker::class],
            [new PeclPacker(), 'pure', PurePacker::class],
            [new PeclPacker(), PurePacker::class, PurePacker::class],
        ];
    }
}
