# PHPUnit Extras

![Continuous Integration](https://github.com/tarantool-php/phpunit-extras/workflows/Continuous%20Integration/badge.svg)

A collection of helpers for [PHPUnit](https://phpunit.de/) to ease testing [Tarantool](https://www.tarantool.io/en/developers/) libraries.
It is based on [rybakit/phpunit-extras](https://github.com/rybakit/phpunit-extras), please refer to this package for more documentation.


## Table of contents

 * [Installation](#installation)
 * [Annotations](#annotations)
   * [Processors](#processors)
     * [Lua](#lua)
     * [Sql](#sql)
   * [Requirements](#requirements)
     * [clientPacker](#clientpacker)
     * [luaCondition](#luacondition)
     * [Tarantool](#tarantool)
 * [Expectations](#expectations)
 * [Mocking](#mocking)
 * [Testing](#testing)
 * [License](#license)


## Installation

```bash
composer require --dev tarantool/phpunit-extras
```


## Annotations

Besides the annotations provided by the package `rybakit/phpunit-extras`, the library is shipped
with add-ons specific to Tarantool. The easiest way to enable them is by inheriting your test classes
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
<extension class="Tarantool\PhpUnit\Annotation">
    <arguments>
        <string>tcp://127.0.0.1:3301/?socket_timeout=10</string>
    </arguments>
</extension>
```
or
```xml
<extension class="Tarantool\PhpUnit\Annotation">
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
<extension class="Tarantool\PhpUnit\Annotation">
    <arguments>
        <string>tcp://%env(TARANTOOL_HOST)%:%env(TARANTOOL_PORT)%</string>
    </arguments>
</extension>
```

Once the annotations are configured, you can start using them:

### Processors

#### Lua

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

#### clientPacker

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

#### luaCondition

*Format:*

```
@requires luaCondition <lua-expression>
```
where `<lua-expression>` is an arbitrary lua expression that should be evaluated to a Boolean value.

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

#### Tarantool

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

> *Note*
>
> If you're interested in how to create and register your own annotations,
> please refer to the `rybakit/phpunit-extras` [README](https://github.com/rybakit/phpunit-extras).


## Expectations

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
use Tarantool\PhpUnit\Expectation\RequestExpectations;

final class MyTest extends TestCase
{
    use RequestExpectations;

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
add the trait `ClientMocking` to your test class:

```php
use PHPUnit\Framework\TestCase;
use Tarantool\PhpUnit\Client\ClientMocking;

final class MyTest extends TestCase
{
    use ClientMocking;

    // ...
}
```

> *Note*
>
> If your test cases extend the `Tarantool\PhpUnit\TestCase` class, this step is not needed
> because the trait is already included in that class.

A dummy client object can be created as follows:

```php
public function testFoo() : void
{
    $dummyClient = $this->createMockClient();

    // ...
}
```

To simulate specific scenarios, such as establishing a connection to a server
or returning specific responses in a specific order from the server, use the facilities
of the `MockClientBuilder` class. For example, to simulate the `PING` request:

```php
use Tarantool\Client\Request\PingRequest;
use Tarantool\PhpUnit\TestCase;

final class MyTest extends TestCase
{
    public function testFoo() : void
    {
        $mockClient = $this->getMockClientBuilder()
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
use Tarantool\PhpUnit\Client\DummyFactory;
use Tarantool\PhpUnit\TestCase;

final class MyTest extends TestCase
{
    public function testFoo() : void
    {
        $mockClient = $this->getMockClientBuilder()
            ->shouldSend(
                RequestTypes::EVALUATE, 
                RequestTypes::EVALUATE
            )->willReceive(
                DummyFactory::createResponseFromData([2]),
                DummyFactory::createResponseFromData([3])
            )->build();
    
        // ...
    }

    // ...
}
```
The above example can be simplified to:

```php
$mockClient = $this->getMockClientBuilder()
    ->shouldHandle(
        RequestTypes::EVALUATE,
        DummyFactory::createResponseFromData([2]),
        DummyFactory::createResponseFromData([3])
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
