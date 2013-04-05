# UniqueKeyInstancePoolingBehavior

[![Build Status](https://secure.travis-ci.org/havvg/UniqueKeyInstancePoolingBehavior.png?branch=master)](http://travis-ci.org/havvg/UniqueKeyInstancePoolingBehavior)

See the Propel documentation on how to [install a third party behavior](http://propelorm.org/documentation/07-behaviors.html#using_thirdparty_behaviors)

## Usage

Just add the following XML tag in your `schema.xml` file:

```xml
<behavior name="unique_key_instance_pooling" />
```

For example:

```xml
<database name="default" defaultIdMethod="native">
    <table name="user">
        <column name="id" type="integer" autoIncrement="true" primaryKey="true" />
        <column name="email" type="varchar" size="255" required="true" primaryString="true" />

        <behavior name="unique_key_instance_pooling" />

        <unique>
            <unique-column name="email" />
        </unique>
    </table>
</database>
```

The behavior will add two methods for each unique key:

1. A static key generator; from the example this would be `createUniquePoolingKeyForEmail`.
  This method returns the key used when accessing the instances pool.

2. The `findOneByEmail` method will be actually implemented.
  This method wraps the original method around instance pooling.

```php
<?php

$email = 'mail@example.com';

/*
 * This retrieves the user from the database.
 * The hydrated object will be put into the instances pool under a special key.
 */
$user = UserQuery::create()->findOneByEmail($email);

// .. more code ..

/*
 * As this user is in the instances pool of the behavior,
 * the cached object will be returned without accessing the database.
 */
$user = UserQuery::create()->findOneByEmail($email);
```
