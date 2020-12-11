# PHPUnit Extras

[![Quality Assurance](https://github.com/tarantool-php/phpunit-extras/workflows/QA/badge.svg)](https://github.com/tarantool-php/phpunit-extras/actions?query=workflow%3AQA)
[![Telegram](https://img.shields.io/badge/Telegram-join%20chat-blue.svg)](https://t.me/tarantool_php)

A collection of helpers for [PHPUnit](https://phpunit.de/) to ease testing [Tarantool](https://www.tarantool.io/en/developers/) libraries.
It is based on [rybakit/phpunit-extras](https://github.com/rybakit/phpunit-extras), please refer to this package for more documentation.


## Table of contents

 * [Installation](#installation)
 * [Annotations](#annotations)
   * [Processors](#processors)
     * [Lua](#lua)
     * [Sql](#sql)
   * [Requirements](#requirements)
     * [ClientPacker](#clientpacker)
     * [LuaCondition](#luacondition)
     * [TarantoolVersion](#tarantoolversion)
 * [Expectations](#expectations)
   * [Requests](#requests)
   * [Prepared statements](#prepared-statements)
 * [Mocking](#mocking)
 * [Testing](#testing)
 * [License](#license)


## Installation

```bash
composer require --dev tarantool/phpunit-extras
```


## Annotations

Besides the annotations provided by the package `rybakit/phpunit-extras`, the library is shipped
with annotations specific to Tarantool. The easiest way to enable them is by inheriting your test classes
from `Tarantool\PhpUnit\TestCase`:

```php
use Tarantool\Client\Client;
use Tarantool\PhpUnit\TestCase;

final class MyTest extends TestCase
{
    protected function getClient() : Client
    {
        // TODO: Implement getClient() method.
    }
    
    // ...
}
```

Another option is to register an extension called `AnnotationExtension`:

```xml
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
    bootstrap="vendor/autoload.php"
>
    <!-- ... -->

    <extensions>
        <extension class="Tarantool\PhpUnit\Annotation\AnnotationExtension" />
    </extensions>
</phpunit>
```

By default, the extension assumes that the Tarantool server you are going to connect to is available on `127.0.0.1:3301`.
You can customize the default settings by specifying either a [DSN string](https://github.com/tarantool-php/client#dsn-string) or an [array of options](https://github.com/tarantool-php/client#array-of-options)
as extension configuration values:

```xml
<extension class="Tarantool\PhpUnit\Annotation\AnnotationExtension">
    <arguments>
        <string>tcp://127.0.0.1:3301/?socket_timeout=10</string>
    </arguments>
</extension>
```
or
```xml
<extension class="Tarantool\PhpUnit\Annotation\AnnotationExtension">
    <arguments>
        <array>
            <element key="uri">
                <string>tcp://127.0.0.1:3301</string>
            </element>
            <element key="socket_timeout">
                <integer>10</integer>
            </element>
        </array>
    </arguments>
</extension>
```

On top of that, the configuration values can resolve environment variables,
which might be useful if you need to share the same settings with a Tarantool
instance file or any other script:

```xml
<extension class="Tarantool\PhpUnit\Annotation\AnnotationExtension">
    <arguments>
        <string>tcp://%env(TARANTOOL_HOST)%:%env(TARANTOOL_PORT)%</string>
    </arguments>
</extension>
```

Once the annotations are configured, you can start using them:

### Processors

#### Lua

Allows executing Lua code before running a test.

*Example:*

```php
/**
 * @lua tube:put('kick_me')
 * @lua tube:bury(0)
 */
public function testKickReleasesBuriedTask() : void
{
    // ...
}
```

#### Sql

Allows executing SQL statements before running a test (requires Tarantool 2.0+).

*Example:*

```php
/**
 * @sql DROP TABLE IF EXISTS foobar
 * @sql CREATE TABLE foobar (id INTEGER PRIMARY KEY, name VARCHAR(50))
 * @sql INSERT INTO foobar VALUES (1, 'A'), (2, 'B')
 */ 
public function testExecuteQueryFetchesAllRows() : void
{
    // ...
}
```


### Requirements

Requirements allow skipping tests based on preconditions.

#### ClientPacker

*Format:*

```
@requires clientPacker <packer>
```
where `<packer>` is either `pure`, `pecl`, or a fully qualified class name, e.g. `Tarantool\Client\Packer\PurePacker`.

*Example:*

```php
/**
 * @requires clientPacker pure 
 */
public function testPackerUnpacksBigIntegerAsDecimal() : void
{
    // ...
}
```

#### LuaCondition

*Format:*

```
@requires luaCondition <condition>
```
where `<condition>` is an arbitrary lua expression that should be evaluated to a Boolean value.

*Example:*

```php
/**
 * @requires luaCondition box.session.user() ~= 'guest'
 */
public function testChangeUserPassword() : void
{
    // ...
}
```

#### TarantoolVersion

*Format:*

```
@requires Tarantool <version-constraint>
```
where `<version-constraint>` is a composer-like version constraint. For details on supported formats, 
please see the Composer [documentation](https://getcomposer.org/doc/articles/versions.md#writing-version-constraints).

*Example:*

```php
/**
 * @requires Tarantool ^2.3.2 
 */
public function testPrepareCreatesPreparedStatement() : void
{
    // ...
}
```

> *If you're interested in how to create and register your own annotations and requirements,
> please refer to the `rybakit/phpunit-extras` [README](https://github.com/rybakit/phpunit-extras).*


## Expectations

### Requests

To test that your code sends (or does not send) certain requests, the following methods are available:

 * `TestCase::expect<REQUEST_NAME>RequestToBeCalled(int $count) : void`
 * `TestCase::expect<REQUEST_NAME>RequestToBeCalledAtLeast(int $count) : void`
 * `TestCase::expect<REQUEST_NAME>RequestToBeCalledAtMost(int $count) : void`
 * `TestCase::expect<REQUEST_NAME>RequestToBeCalledOnce() : void`
 * `TestCase::expect<REQUEST_NAME>RequestToBeCalledAtLeastOnce() : void`
 * `TestCase::expect<REQUEST_NAME>RequestToBeCalledAtMostOnce() : void`
 * `TestCase::expect<REQUEST_NAME>RequestToBeNeverCalled() : void`
 * `TestCase::expectNoRequestToBeCalled() : void`

where `<REQUEST_NAME>` is the name of the request, for example `Call`, `Insert`, etc.
These methods are part of the `Tarantool\PhpUnit\TestCase` class, but they can also be enabled through a trait:

```php
use PHPUnit\Framework\TestCase;
use PHPUnitExtras\Expectation\Expectations as BaseExpectations;
use Tarantool\Client\Client;
use Tarantool\PhpUnit\Expectation\RequestExpectations;

final class MyTest extends TestCase
{
    use BaseExpectations;
    use RequestExpectations;

    protected function getClient() : Client
    {
        // TODO: Implement getClient() method.
    }

    /**
     * @after
     */
    protected function verifyTestCaseExpectations() : void
    {
        $this->verifyExpectations();
    }

    // ...
}
```

*Example:*

```php
public function testGetSpaceIsCached() : void
{
    $this->client->flushSpaces();

    $this->expectSelectRequestToBeCalledOnce();
    $this->client->getSpace('test_space');
    $this->client->getSpace('test_space');
}
```

### Prepared statements

In order to assert prepared statement allocations, use the `Tarantool\PhpUnit\Expectation\PreparedStatementExpectations` trait,
which contains the following methods:

 * `expectPreparedStatementToBe<TYPE>(int $count) : void`
 * `expectPreparedStatementToBe<TYPE>AtLeast(int $count) : void`
 * `expectPreparedStatementToBe<TYPE>AtMost(int $count) : void`
 * `expectPreparedStatementToBe<TYPE>Once() : void`
 * `expectPreparedStatementToBeNever<TYPE>() : void`
 * `expectPreparedStatementToBe<TYPE>AtLeastOnce() : void`
 * `expectPreparedStatementToBe<TYPE>AtMostOnce() : void`

where `<TYPE>` is either `Allocated` or `Deallocated`.

*Example:*

```php
public function testCloseDeallocatesPreparedStatement() : void
{
    $stmt = $this->client->prepare('SELECT ?');

    $this->expectPreparedStatementToBeDeallocatedOnce();
    $stmt->close();
}
```

To enable all the above expectation methods in one go, use the `Tarantool\PhpUnit\Expectation\Expectations` trait,
or extend the `Tarantool\PhpUnit\TestCase` class.


## Mocking

The library provides several helper classes to create test doubles for the [Tarantool Сlient](https://github.com/tarantool-php/client)
to avoid sending real requests to the Tarantool server. For the convenience of creating such objects,
add the trait `TestDoubleClient` to your test class:

```php
use PHPUnit\Framework\TestCase;
use Tarantool\PhpUnit\Client\TestDoubleClient;

final class MyTest extends TestCase
{
    use TestDoubleClient;

    // ...
}
```

> *If your test cases extend the `Tarantool\PhpUnit\TestCase` class, this step is not needed
> because the trait is already included in that class.*

A dummy client object can be created as follows:

```php
public function testFoo() : void
{
    $dummyClient = $this->createDummyClient();

    // ...
}
```

To simulate specific scenarios, such as establishing a connection to a server
or returning specific responses in a specific order from the server, use the facilities
of the `TestDoubleClientBuilder` class. For example, to simulate the `PING` request:

```php
use Tarantool\Client\Request\PingRequest;
use Tarantool\PhpUnit\TestCase;

final class MyTest extends TestCase
{
    public function testFoo() : void
    {
        $mockClient = $this->getTestDoubleClientBuilder()
            ->shouldSend(new PingRequest())
            ->build();

        // ...
    }

    // ...
}
```

Another example, sending two `EVALUATE` requests and returning a different response for each:

```php
use Tarantool\Client\RequestTypes;
use Tarantool\PhpUnit\Client\TestDoubleFactory;
use Tarantool\PhpUnit\TestCase;

final class MyTest extends TestCase
{
    public function testFoo() : void
    {
        $mockClient = $this->getTestDoubleClientBuilder()
            ->shouldSend(
                RequestTypes::EVALUATE, 
                RequestTypes::EVALUATE
            )->willReceive(
                TestDoubleFactory::createResponseFromData([2]),
                TestDoubleFactory::createResponseFromData([3])
            )->build();
    
        // ...
    }

    // ...
}
```
The above example can be simplified to:

```php
$mockClient = $this->getTestDoubleClientBuilder()
    ->shouldHandle(
        RequestTypes::EVALUATE,
        TestDoubleFactory::createResponseFromData([2]),
        TestDoubleFactory::createResponseFromData([3])
    )->build();
```

Besides, the builder allows setting custom `Connection` and `Packer` instances:

```php
$stubClient = $this->getMockClientBuilder()
    ->willUseConnection($myConnection)
    ->willUsePacker($myPacker)
    ->build();
```

## Testing

Before running tests, the development dependencies must be installed:

```bash
composer install
```

Then, to run all the tests:

```bash
vendor/bin/phpunit
vendor/bin/phpunit -c phpunit-extension.xml
```


## License

The library is released under the MIT License. See the bundled [LICENSE](LICENSE) file for details.
