# veManager

A simple but powerful PHP Virtual Entity Data Manager `(in progress)`.

**Virtual**, because sometimes we don't like to create model classes, but we still want all the power of OOP when manipulating data that will be persisted in a table.


## How it works

If you decide to create a physical Entity (a php file model), you can create a class just like this:

```php
// Database Table
user
- id_user int
- name varchar
- age int
- email varchar

// model/User.php
class User extends Osians\VeManager\Entity
{
    protected $_name = null;
    protected $_age = null;
    protected $_mail = null;
}

// controller/User.php
$user = new User();
$user->setId(4);
$user->setName('John Doe');
$user->setAge(54);
$user->setEmail('john.doe@acmeinc.com');

$vem = new Osians\VeManager\VeManager($connectionParams);
$vem->save($user);
```

That's right, and that's how most Entity Manager systems work.

But if you want to create a Virtual Entity, as this system suggests, you can do it as follows:

```php
$vem = new Osians\VeManager\VeManager($connectionParams);

$user = $vem->createEntity('user');
$user->setName('John Doe');
$user->setEmail('john.doe@hotmail.com');
$user->setAge(31);
$user->setActive(1);

//  Persiste
$vem->save($user);
```
You don't need to have a file that represents a Model class. You only need to have an `User` table registered in the database. When you use the `creatEntity` Method the system will search the database for the necessary information and then create a Virtual representation of the Data Model.


### **This system is composed by**

Some technologies are necessary for this system to work:

1. A Database Provider (/Database/Mysql.php)
1. A Query Builder (/QueryBuilder.php)
1. An Abstract Model Entity (/Entity.php)
1. A Virtual Entity Class (/VirtualEntity.php)
1. An Entity Manager (VeManager.php)

**Database Provider** - it is responsible for allowing the system to talk to the database.

**Query Builder** - This will allow a PHP class to be converted to an SQL language.

**Abstract Model Entity** - this Class implements all the methods necessary for an Entity to be manipulated by other objects in the system.

**Virtual Entity** - Represents a Virtual Instance, which is not based on a model file.

**Entity Manager** - This will orchestrate the communication between the class and the database.


## **How the database should be normalized**

The entire system is programmed using the CamelCase syntax scheme, however, the database needs to be used in the SnakeCase syntax.
For example, the user and address tables:
```
user
 - id_user
 - name
 - email
 - id_address
 - active

address
 - id_address
 - address
 - number
 - postal_code
 - active
```

The most important thing is that the primary key of the table is composed of "id_" + "tablename". If not, the system will have some issues to manipulate the table information.

## **Get Start**

**It will be done soon**


**Thanks**.
Wanderlei Santana <sans.pds@gmail.com>