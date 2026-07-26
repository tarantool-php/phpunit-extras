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

namespace Tarantool\PhpUnit\Tests\Annotation;

use PHPUnit\Framework\TestCase;
use Tarantool\PhpUnit\Annotation\Attribute\Lua;

final class AnnotationExtensionTest extends TestCase
{
    private function bootstrapExtension(string $method, array $parameters = []) : AnnotationExtension
    {
        $ext = new AnnotationExtension();
        $ext->doParseParameters($parameters);
        $ext->processTestAttributes(self::class, $method);

        return $ext;
    }

    #[Lua('dummy_code_to_trigger_annotation_processing = true')]
    public function testConstructorUsesDefaultDsn() : void
    {
        $ext = $this->bootstrapExtension(__FUNCTION__);

        self::assertSame('tcp://127.0.0.1:3301', $ext->resolvedDnsOrOptions);
    }

    #[Lua('dummy_code_to_trigger_annotation_processing = true')]
    public function testConstructorUsesCustomDsn() : void
    {
        $dsn = 'tcp://tnt_foobar:3302';
        $ext = $this->bootstrapExtension(__FUNCTION__, ['dsn' => $dsn]);

        self::assertSame($dsn, $ext->resolvedDnsOrOptions);
    }

    #[Lua('dummy_code_to_trigger_annotation_processing = true')]
    public function testGetClientConfigNormalizesDsnString() : void
    {
        $hostname = 'tnt_foobar';
        $port = '3303';
        $envHostName = 'tnt_phpunit_env_host_'.random_int(1, 1000);
        $envPortName = 'tnt_phpunit_env_port_'.random_int(1, 1000);
        putenv("$envHostName=$hostname");
        putenv("$envPortName=$port");
        $ext = $this->bootstrapExtension(__FUNCTION__, ['dsn' => "tcp://%env($envHostName)%:%env($envPortName)%"]);

        self::assertSame("tcp://$hostname:$port", $ext->resolvedDnsOrOptions);
    }

    #[Lua('dummy_code_to_trigger_annotation_processing = true')]
    public function testGetClientConfigNormalizesOptionArray() : void
    {
        $hostname = 'tnt_foobar';
        $port = '3303';
        $envHostName = 'tnt_phpunit_env_host_'.random_int(1, 1000);
        $envPortName = 'tnt_phpunit_env_port_'.random_int(1, 1000);
        putenv("$envHostName=$hostname");
        putenv("$envPortName=$port");
        $ext = $this->bootstrapExtension(__FUNCTION__, [
            'uri' => "tcp://%env($envHostName)%:%env($envPortName)%",
            'socket_timeout' => '10',
            'persistent' => '1',
        ]);

        self::assertEquals([
            'uri' => "tcp://$hostname:$port",
            'socket_timeout' => '10',
            'persistent' => '1',
        ], $ext->resolvedDnsOrOptions);
    }
}
