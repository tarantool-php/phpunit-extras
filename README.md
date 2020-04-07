# PHPUnit Extras

![Continuous Integration](https://github.com/tarantool-php/phpunit-extras/workflows/Continuous%20Integration/badge.svg)

A collection of helpers for [PHPUnit](https://phpunit.de/) to ease testing [Tarantool](https://www.tarantool.io/en/developers/) libraries.


## Table of contents

 * [Installation](#installation)
 * [Annotations](#annotations)
   * [Processors](#processors)
   * [Requirements](#requirements)
 * [Expectations](#expectations)
 * [Mocking](#mocking)
 * [Testing](#testing)
 * [License](#license)


## Installation

```bash
composer require --dev tarantool/phpunit-extras
```


## Annotations

### Processors

*Lua*

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

*Sql*

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

*ClientPacker*

```php
/**
 * @requires clientPacker pure 
 */
public function testPackerUnpacksBigIntegerAsDecimal() : void
{
    // ...
}
```

*LuaCondition*

```php
/**
 * @requires luaCondition box.space.foobar ~= nil 
 */
public function testUserCanAccessSpace() : void
{
    // ...
}
```

*Tarantool*

```php
/**
 * @requires Tarantool >= 2.3.2 
 */
public function testPrepareCreatesPreparedStatement() : void
{
    // ...
}
```


## Expectations

*RequestExpectations*

```php
public function testGetSpaceIsCached() : void
{
    $this->client->flushSpaces();

    $this->expectSelectRequestToBeCalledOnce();
    $this->client->getSpace('test_space')->delete(42);
    $this->client->getSpace('test_space')->delete(42);
}
```


## Mocking

This library provides several helper classes to mock the [Tarantool Client](https://github.com/tarantool-php/client)
without having to send real requests to the Tarantool server. For convenient creation of mocks in your tests, 
add a `ClientMocking` trait to your test case:

```php
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

The most basic version of the mock object can be created as follows:

```php
public function testFoo() : void
{
    $mockClient = $this->createMockClient();

    // ...
}
```

To simulate specific scenarios, such as establishing a connection to a server
or returning specific responses in a specific order from a server, use the facilities
of the `MockClientBuilder` class. For example, to simulate the ping request/response:

```php
use Tarantool\Client\Request\PingRequest;
use Tarantool\PhpUnit\Client\DummyFactory;

public function testFoo() : void
{
    $mockClient = $this->getMockClientBuilder()
        ->shouldHandle(
            new PingRequest(),
            DummyFactory::createEmptyResponse()
        )->build();

    // ...
}
```

Another example, sending two `EVALUATE` requests and returning a different response for each:

```php
use Tarantool\Client\RequestTypes;
use Tarantool\PhpUnit\Client\DummyFactory;

public function testFoo() : void
{
    $mockClient = $this->getMockClientBuilder()
        ->shouldHandle(
            RequestTypes::EVALUATE,
            DummyFactory::createResponseFromData([2]),
            DummyFactory::createResponseFromData([3])
        )->build();

    // ...
}
```

Besides, the builder allows setting custom `Connection` and `Packer` instances:

```php
$mockClient = $this->getMockClientBuilder()
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
