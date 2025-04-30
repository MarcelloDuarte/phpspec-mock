# Phpspec Mock

A lightweight and powerful mocking framework for PHP.

Phpspec Mock provides fast, flexible, and fluent mocks and stubs for your tests, inspired by Prophecy and Mockery, but fully modern and streamlined codebase.

---

## Installation

```bash
composer require --dev phpspec/mock
```

### Quick Example

```php
use function Phpspec\Mock\mock;

$double = mock(SomeService::class);

// Setup expectations
$double->someMethod(42)->shouldBeCalled()->willReturn('The answer');

// Use the mock
$someService = $double->mock();
$result = $someService->someMethod(42);

// Assert expectations
$double->verify();
```

## Key Concepts

### Doubles

Create doubles using the Double factory (or the helper functions):

```php
$double = Double::create(SomeClass::class);
```

Or simply:

```php
$double = mock(SomeClass::class);
// or
$double = stub(SomeClass::class);
// or
$double = spy(SomeClass::class);
```

Both the helper functions and the Double factory return the exact same double configurator object.
The difference is only semantics, to help you communicate your intention.

### Stubbing methods

Return a value when a method is called:

```php
$double->someMethod(1, 2)->willReturn('something');
```

You can also throw exceptions:

```php
$double->someMethod(1, 2)->willThrow(new \RuntimeException('fail'));
```

### Mocking (Expectations)

Set expectations that methods should or should not be called:

```php
$mock->someMethod(1, 2)->shouldBeCalled();
$mock->otherMethod()->shouldNotBeCalled();
```

You can specify how many times a method should be called:

```php
$mock->anotherMethod()->shouldBeCalled(2);
```

At the end of the test, verify:

```php
$mock->verify();
```

If expectations are not met, `verify()` will throw detailed errors.

## Argument Matching

You can match arguments flexibly:

```php
use function Phpspec\Mock\any;
use function Phpspec\Mock\exact;

$mock->someMethod(any(), exact(42))->shouldBeCalled();
```

## Consecutive Call Stubbing

Return different values on consecutive calls to the same method.

```php
$mock->someMethod()->willReturn('first', 'second', 'third');
```

## Why Another Mocking Framework?

 - ⚡ Modernised, built from scratch for PHP 8.4+
 - 🎯 Explicit and simple: no magic tricks
 - 🧹 Clear separation of stubs and mocks
 - 💬 Fluent API, intuitive syntax
 - 🛡️ Full test coverage
 - 🛠️ Easy to extend
 - Compatible with Phpspec 9.0.0-preview for PHP 8.4+

## License

MIT License.

## Development Status

- [x] Core Doubler implementation
- [x] DoubledMethod split into StubbedMethod and MockedMethod
- [x] Verification of mocked expectations
- [x] Code generation fully modular
- [x] Helper functions (in progress)
- [x] Internal mocks for framework self-testing (in progress)
- [x] Readme started (this file!)
- [x] Argument matching
- [x] Can mock readonly classes
- [ ] Behaviour chaining (willReturnOnConsecutiveCalls)
- [x] More detailed error reporting (diffs)
