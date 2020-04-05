# PHPUnit Extras

![Continuous Integration](https://github.com/tarantool-php/phpunit-extras/workflows/Continuous%20Integration/badge.svg)

A collection of helpers for [PHPUnit](https://phpunit.de/) to ease testing [Tarantool](https://www.tarantool.io/en/developers/) libraries. 


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


## License

The library is released under the MIT License. See the bundled [LICENSE](LICENSE) file for details.
