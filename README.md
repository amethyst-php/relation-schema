# Relation Schema

[![Action Status](https://github.com/amethyst-php/relation-schema/workflows/Test/badge.svg)](https://github.com/amethyst-php/relation-schema/actions)
[![Amethyst](https://img.shields.io/badge/Package-Amethyst-7e57c2)](https://github.com/amethyst-php/amethyst)

Create your own custom relations between Models without altering the code.

# Documentation

# Requirements

- PHP from 7.1 to 7.4
- Laravel from 5.8 to 8.x

## Installation

You can install it via [Composer](https://getcomposer.org/) by typing the following command:

```bash
composer require amethyst/relation-schema
```

The package will automatically register itself.

Add `app('amethyst.relation-schema')->boot();` in any ServiceProvider in the method `boot`

## Usage

This explanation presumes that you're already know the basics of how any Amethyst Package works. If you don't know check the documentation

All relations are stored by the Eloquent Model `Amethyst\Models\RelationSchema`. 

There are currently 5 relations supported: `BelongsTo`, `MorphTo`, `MorphToMany`, `MorphToOne`, `MorphMany`.

To create a new relation insert a new record like the following example.
```php
use Symfony\Component\Yaml\Yaml;

app('amethyst')->get('relation-schema')->createOrFail([
    'name'    => 'parent',
    'type'    => 'BelongsTo',
    'data'    => 'foo',
    'payload' => Yaml::dump([
        'target' => 'bar',
    ]),
]);
```
Here's a list of all attributes with a brief explanation:
- name: The name of the relation. Can be only alphanumeric and it must start with a letter
- description: An optional field that will help to describe the relation 
- type: The type of the relation, for now there are only 5 relations: `BelongsTo`, `MorphTo`, `MorphToMany`, `MorphToOne`, `MorphMany`
- data: The name of the data this relation start with. If a `Book` is related to an `Author` through the relation `BelongsTo` named `author`, then `book` is the data
- payload: A sets of information used to define better the relation, this changes based on the type of the relation. For example in a `BelongsTo` relation you'll neet a target, in the previous example `Author` is the target. 

Now we'll see how the payload changes 
### BelongsTo

- target
- foreignKey

### MorphTo

- foreignKey
- ownerKey

### MorphToMany

### MorphToOne

### MorphMany

## Testing

- Clone this repository
- Copy the default `phpunit.xml.dist` to `phpunit.xml`
- Change the environment variables as you see fit
- Launch `./vendor/bin/phpunit`
