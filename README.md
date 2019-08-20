# Phlexus Auth Library

Authentication library for Phlexus CMS. With support of several drivers/adapters to use for different situations.

Inspired by [SidRoberts/phalcon-auth](https://github.com/SidRoberts/phalcon-auth)

## Example of usage

```php
use Phlexus\Libraries\Auth\Manager as AuthManager;

$manager = new AuthManager('plain', ['auth_file_path' => 'passwords.txt']);
$manager->login(['user', 'password']);
``` 

## Currently supported drivers

* **model** - Phalcon Model
* **plain** - Plain text file
