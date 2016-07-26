# OtherCode Database

Light database abstraction layer (PDO) 

Currently supported:

* MySQL
* SQLite
* Postgres

## Installation

To install the package we only have to add the dependency to ***composer.json*** file:

```javascript
"require": {
  "othercode/database": "*"
}
```

And run the following command:


```bash
composer update
```

### Install without Composer

Also we can use this library without Composer, we only have to include in our script the **"database/autoload.php"** file.
```php
require_once "database/autoload.php".
```

## Configuration

Now we have to create the instance and add a new connection to it.

```php
$db = new OtherCode\Database\Database();

$db->addConnection(array(
    'driver' => 'mysql',
    'host' => 'localhost',
    'dbname' => 'test',
    'username' => 'username',
    'password' => 'password'
));
```