# amethyst-relation-schema

[![Action Status](https://github.com/amethyst-php/relation-schema/workflows/Test/badge.svg)](https://github.com/amethyst-php/relation-schema/actions)

[Amethyst](https://github.com/amethyst-php/amethyst) package.

Create your own custom relations between Models without altering the code.

Current supported relationships:
- MorphMany

Future features:
- Define inverse relationships (e.g. children and parent)

# Requirements

PHP 7.1 and later.

## Installation

You can install it via [Composer](https://getcomposer.org/) by typing the following command:

```bash
composer require amethyst/relation-schema
```

The package will automatically register itself.

## Demo

```php
use Amethyst\Models\RelationSchema;
use Amethyst\Models\Foo;

RelationSchema::create([
    'name' => 'redChildren',
    'source' => 'foo',
    'target' => 'foo',
    'type' => 'MorphMany',
    'filter' => "redChildren"
]);

RelationSchema::create([
    'name' => 'blueChildren',
    'source' => 'foo',
    'target' => 'foo',
    'type' => 'MorphMany',
    'filter' => "blueChildren"
]);

$parent = Foo::create(['name' => 'Parent']);
$redChildren = Foo::create(['name' => 'Child:Red']);
$blueChildren = Foo::create(['name' => 'Child:Blue']);

$parent->redChildren()->attach($redChildren);
$parent->blueChildren()->attach($blueChildren);

$parent->redChildren->count(); // 1
$parent->blueChildren->count(); // 1

 ```
## Documentation

[Read](docs/index.md)

## Testing

Configure the .env file before launching `./vendor/bin/phpunit`
