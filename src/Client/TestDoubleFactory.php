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

namespace Tarantool\PhpUnit\Client;

use Tarantool\Client\Keys;
use Tarantool\Client\Response;

final class TestDoubleFactory
{
    public static function createResponse(array $body, array $header = []) : Response
    {
        return new Response($header, $body);
    }

    public static function createResponseFromData(array $data) : Response
    {
        return self::createResponse([Keys::DATA => $data]);
    }

    public static function createEmptyResponse() : Response
    {
        return self::createResponseFromData([null]);
    }
}
