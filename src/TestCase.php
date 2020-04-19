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

namespace Tarantool\PhpUnit;

use PHPUnitExtras\TestCase as BaseTestCase;
use Tarantool\PhpUnit\Annotation\Annotations;
use Tarantool\PhpUnit\Client\TestDoubleClient;
use Tarantool\PhpUnit\Expectation\Expectations;

abstract class TestCase extends BaseTestCase
{
    use TestDoubleClient;
    use Annotations;
    use Expectations;
}
