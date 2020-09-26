# Relation Schema

[![Action Status](https://github.com/amethyst-php/relation-schema/workflows/test/badge.svg)](https://github.com/amethyst-php/relation-schema/actions)
[![Amethyst](https://img.shields.io/badge/package-Amethyst-7e57c2)](https://github.com/amethyst-php/amethyst)

Create your own custom relations between Eloquent Models without altering the code.

# Requirements

- PHP from 7.2 to 7.4
- Laravel from 5.8 to 8.x

## Installation

You can install it via [Composer](https://getcomposer.org/) by typing the following command:

```bash
composer require amethyst/relation-schema
```

The package will automatically register itself.

## Initialization

Add `app('amethyst.relation-schema')->boot();` in any ServiceProvider in the method `boot`

## Usage

A simple usage looks like this
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
- type: The type of the relation. Values: `BelongsTo`, `HasMany`, `HasOne`, `MorphTo`, `MorphToMany`, `MorphToOne`, `MorphMany`
- data: The name of the data this relation start with. If a `Book` is related to an `Author` through the relation `BelongsTo` named `author`, then `book` is the data
- payload: A sets of information used to define better the relation, this changes based on the type of the relation. For example in a `BelongsTo` relation you'll neet a target, in the previous example `Author` is the target. 

Whenever refering to any model, we will call it by name. For e.g. `\App\Models\Customer` will be `customer`. [More info](https://github.com/amethyst-php/core)

Keep in mind that this is an [Amethyst Package](https://github.com/amethyst-php/amethyst), if you wish to see the full list of available features and customization please check [core](https://github.com/amethyst-php/core)

## Relationships

As said before, based on the `type` attribute, `payload` must have a different sets of value. It uses Yaml for the serialization, so for all examples we will use a `Symfony\Component\Yaml\Yaml` to convert from array to Yaml.

Most of relation uses the laravel-defined convention of names, such as `foreignKey` or `ownerKey`.

### BelongsTo

Starting with the most simple relation, `BelongsTo`. It requires only the `target` parameter that indicate to which Model we are relating.

You can also set the `foreignKey` option.

So for example this new record
```php
use Symfony\Component\Yaml\Yaml;

app('amethyst')->get('relation-schema')->createOrFail([
    'name'    => 'parent',
    'type'    => 'BelongsTo',
    'data'    => 'foo',
    'payload' => Yaml::dump([
        'target' => 'bar',
        'foreignKey' => 'foreign_key'
    ])
]);
```
is the exact same thing like this

```php
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Foo
{
	public function parent(): BelongsTo
	{
		return $this->belongsTo(Bar::class, 'foreign_key');
	}
}

```

### HasMany and HasOne

The inverse relationship of BelongsTo. They behave exactly the same.

Beside the `target` you can set `foreignKey` and `localKey`.

Now let's look an example of the same relationship in `BelongsTo` but inverted

```php
use Symfony\Component\Yaml\Yaml;

app('amethyst')->get('relation-schema')->createOrFail([
    'name'    => 'children',
    'type'    => 'HasMany',
    'data'    => 'bar',
    'payload' => Yaml::dump([
        'target' => 'foo',
        'foreignKey' => 'foreign_key',
        'localKey' => 'local_key'
    ]),
]);
```
Will result in:
```php
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bar
{
	public function children(): HasMany
	{
		return $this->belongsTo(Foo::class, 'foreign_key', 'local_key');
	}
}

```

But wait, there's more! You can create relations with a custom subfilter. Say for example that having a `customer` and `invoice` you want to create a relation that retrieve all `invoices` with `status`:`paid` from your customer.

You can add another parameter called `filter` in the `payload`.

So having something like this: `filter: "status eq 'paid'"`. You can also use auto joins here, so for e.g. a query like: only the invoice that contains `Maintenance` in the name, will result in `filter: "status eq 'paid' and items.name ct 'Maintenance'"`.

Limitation: You cannot insert in the filter the same relation you're currently defining
### ManyToMany and BelongsToMany
Not yet implemented.

### MorphTo

Similar to`BelongsTo` it doesn't require a `target`, instead it requires a `foreignKey` that in this case means the name of the field

### MorphToMany and MorphToOne

WIP

### MorphMany

WIP 

## Api

There are no additional routes in this package, only the default provided by the [core](https://github.com/amethyst-php/core).

## Testing

- Clone this repository
- Copy the default `phpunit.xml.dist` to `phpunit.xml`
- Change the environment variables as you see fit
- Launch `./vendor/bin/phpunit`
