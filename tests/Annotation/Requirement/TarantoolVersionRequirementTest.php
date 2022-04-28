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
use Tarantool\PhpUnit\Client\TestDoubleClient;
use Tarantool\PhpUnit\Client\TestDoubleFactory;

final class TarantoolVersionRequirementTest extends TestCase
{
    use TestDoubleClient;

    /**
     * @dataProvider provideCheckPassesForValidConstraintsData()
     */
    public function testCheckPassesForValidConstraints(string $serverVersion, string $constraints) : void
    {
        $mockClient = $this->getTestDoubleClientBuilder()
            ->shouldHandle(
                new CallRequest('box.info'),
                TestDoubleFactory::createResponseFromData([['version' => $serverVersion]]))
            ->build();

        $requirement = new TarantoolVersionRequirement($mockClient);

        self::assertNull($requirement->check($constraints));
    }

    public function provideCheckPassesForValidConstraintsData() : iterable
    {
        $v2_3_1_3 = '2.3.1-3-g878e2a42c';

        return [
            [$v2_3_1_3, '^2.3.1-2'],
            [$v2_3_1_3, '^2.3.0-4'],
            [$v2_3_1_3, '^2.3.1'],
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

            // @see https://github.com/tarantool/tarantool/discussions/6182
            ['2.11.0-entrypoint', '< 2.11.0-alpha'],
            ['2.11.0-entrypoint.8', '< 2.11.0-alpha'],
            ['2.11.0-entrypoint.8', '< 2.11.1'],
            ['2.11.0-entrypoint', '= 2.11.0-dev'],
            ['2.11.0-entrypoint.8', '= 2.11.0-dev'],
            ['2.11.0-entrypoint.8-g878e2a42c', '= 2.11.0-dev'],
            ['2.11.0-entrypoint-17-g878e2a42c-dev', '= 2.11.0-dev'],
        ];
    }

    /**
     * @dataProvider provideCheckFailsForInvalidConstraintsData()
     */
    public function testCheckFailsForInvalidConstraints(string $serverVersion, string $constraints) : void
    {
        $mockClient = $this->getTestDoubleClientBuilder()
            ->shouldHandle(
                new CallRequest('box.info'),
                TestDoubleFactory::createResponseFromData([['version' => $serverVersion]]))
            ->build();

        $requirement = new TarantoolVersionRequirement($mockClient);
        $errorMessage = sprintf('Tarantool version %s is required', $constraints);

        self::assertSame($errorMessage, $requirement->check($constraints));
    }

    public function provideCheckFailsForInvalidConstraintsData() : iterable
    {
        $v2_3_1_3 = '2.3.1-3-g878e2a42c';

        return [
            [$v2_3_1_3, '^2.3.1-4'],
            [$v2_3_1_3, '^3|^4'],
            [$v2_3_1_3, '^3'],

            [$v2_3_1_3, '2.3.1-4'],
            [$v2_3_1_3, '2.3.2'],
            [$v2_3_1_3, '2.4'],
            [$v2_3_1_3, '3'],

            [$v2_3_1_3, '>= 2.3.1-4'],
            [$v2_3_1_3, '>= 2.3.2'],
            [$v2_3_1_3, '>= 2.4'],
            [$v2_3_1_3, '>= 3'],

            [$v2_3_1_3, '> 2.3.1-3'],
            [$v2_3_1_3, '> 2.3.2'],
            [$v2_3_1_3, '> 2.4'],
            [$v2_3_1_3, '> 3'],

            [$v2_3_1_3, '<= 2.3.1-2'],
            [$v2_3_1_3, '<= 2.3.1'],
            [$v2_3_1_3, '<= 2.3.0'],
            [$v2_3_1_3, '<= 2.3'],
            [$v2_3_1_3, '<= 2'],

            [$v2_3_1_3, '< 2.3.1-3'],
            [$v2_3_1_3, '< 2.3.1'],
            [$v2_3_1_3, '< 2.3'],
            [$v2_3_1_3, '< 2'],
        ];
    }
}
