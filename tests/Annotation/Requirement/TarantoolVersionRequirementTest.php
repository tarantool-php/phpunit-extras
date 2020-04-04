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
use Tarantool\Client\Request\CallRequest;
use Tarantool\PhpUnit\Annotation\Requirement\TarantoolVersionRequirement;
use Tarantool\PhpUnit\Client\ClientMocking;
use Tarantool\PhpUnit\Client\DummyFactory;

final class TarantoolVersionRequirementTest extends TestCase
{
    use ClientMocking;

    /**
     * @dataProvider provideCheckPassesForValidConstraintsData()
     */
    public function testCheckPassesForValidConstraints(string $serverVersion, string $constraints) : void
    {
        $mockClient = $this->getMockClientBuilder()
            ->shouldHandle(
                new CallRequest('box.info'),
                DummyFactory::createResponseFromData([['version' => $serverVersion]]))
            ->build();

        $requirement = new TarantoolVersionRequirement($mockClient);

        self::assertNull($requirement->check($constraints));
    }

    public function provideCheckPassesForValidConstraintsData() : iterable
    {
        $v2_3_1_3 = '2.3.1-3-g878e2a42c';

        return [
            [$v2_3_1_3, '^1|^2'],
            [$v2_3_1_3, '^2'],

            [$v2_3_1_3, '2.3.1-3'],
            [$v2_3_1_3, '2.3.1.3'],
            [$v2_3_1_3, '~2.3.1'],
            [$v2_3_1_3, '~2.3'],
            [$v2_3_1_3, '~2'],

            [$v2_3_1_3, '>= 2.3.1-2'],
            [$v2_3_1_3, '>= 2.3.1.2'],
            [$v2_3_1_3, '>= 2.3.1'],
            [$v2_3_1_3, '>= 2.3'],
            [$v2_3_1_3, '>= 2.2'],
            [$v2_3_1_3, '>= 2'],

            [$v2_3_1_3, '> 2.3.0-4'],
            [$v2_3_1_3, '> 2.3.0.4'],
            [$v2_3_1_3, '> 2.3.0'],
            [$v2_3_1_3, '> 2.3'],
            [$v2_3_1_3, '> 2.2'],
            [$v2_3_1_3, '> 2'],

            [$v2_3_1_3, '<= 2.3.1-3'],
            [$v2_3_1_3, '<= 2.3.1.3'],
            [$v2_3_1_3, '<= 2.3.1-4'],
            [$v2_3_1_3, '<= 2.3.1.4'],
            [$v2_3_1_3, '<= 2.3.2'],
            [$v2_3_1_3, '<= 2.4'],
            [$v2_3_1_3, '<= 3'],

            [$v2_3_1_3, '< 2.3.2-1'],
            [$v2_3_1_3, '< 2.3.2.1'],
            [$v2_3_1_3, '< 2.3.2'],
            [$v2_3_1_3, '< 2.4'],
            [$v2_3_1_3, '< 3'],
        ];
    }
}
