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

final class AnnotationExtensionTest extends TestCase
{
    /**
     * @lua dummy_code_to_trigger_annotation_processing = true
     */
    public function testConstructorUsesDefaultDsn() : void
    {
        $ext = new AnnotationExtension();

        $ext->executeBeforeTest(__METHOD__);
        self::assertSame('tcp://127.0.0.1:3301', $ext->resolvedDnsOrOptions);
    }

    /**
     * @lua dummy_code_to_trigger_annotation_processing = true
     */
    public function testConstructorUsesCustomDsn() : void
    {
        $dsn = 'tcp://tnt_foobar:3302';
        $ext = new AnnotationExtension($dsn);

        $ext->executeBeforeTest(__METHOD__);
        self::assertSame($dsn, $ext->resolvedDnsOrOptions);
    }

    /**
     * @lua dummy_code_to_trigger_annotation_processing = true
     */
    public function testGetClientConfigNormalizesDsnString() : void
    {
        $hostname = 'tnt_foobar';
        $port = '3303';
        $envHostName = 'tnt_phpunit_env_host_'.random_int(1, 1000);
        $envPortName = 'tnt_phpunit_env_port_'.random_int(1, 1000);
        putenv("$envHostName=$hostname");
        putenv("$envPortName=$port");
        $ext = new AnnotationExtension("tcp://%env($envHostName)%:%env($envPortName)%");

        $ext->executeBeforeTest(__METHOD__);
        self::assertSame("tcp://$hostname:$port", $ext->resolvedDnsOrOptions);
    }

    /**
     * @lua dummy_code_to_trigger_annotation_processing = true
     */
    public function testGetClientConfigNormalizesOptionArray() : void
    {
        $hostname = 'tnt_foobar';
        $port = '3303';
        $envHostName = 'tnt_phpunit_env_host_'.random_int(1, 1000);
        $envPortName = 'tnt_phpunit_env_port_'.random_int(1, 1000);
        putenv("$envHostName=$hostname");
        putenv("$envPortName=$port");
        $ext = new AnnotationExtension([
            'uri' => "tcp://%env($envHostName)%:%env($envPortName)%",
            'socket_timeout' => 10,
            'persistent' => true,
        ]);

        $ext->executeBeforeTest(__METHOD__);
        self::assertSame([
            'uri' => "tcp://$hostname:$port",
            'socket_timeout' => 10,
            'persistent' => true,
        ], $ext->resolvedDnsOrOptions);
    }
}
