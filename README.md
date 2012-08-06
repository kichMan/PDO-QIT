#PDO-QOT
==============

**The script-helper in the administration of the MySQL database.**

> Text search in all tables of a database of the current connection or all databases, available to the user. Uses PHP Data Objects (PDO) library.

##The current version:
* v 0.1 2012-07-30

##Usage
> Attention! PDOQueryInTables is receiver the PDO. Therefore it inherits all properties of the parent class.

### Creating instance

Creating a PDOQueryInTables instance representing a connection to a database.
```php
<?php
/*
* The description of parameters
* to read on http://www.php.net/manual/en/pdo.construct.php
*/
$DB = new PDOQueryInTables(
    "mysql:host=localhost;dbname=myBase",
    "login",
    "pass",
    array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
);
?>
```

### Simple request in all tables of a database of the current connection
```php
<?php
/*The text meets the requirements of LIKE function MySQL*/
$arr = $DB->MoveQuery("%Required text%");
print_r($arr);
?>
```
Will return NULL if the empty parameter is transferred.

Otherwise will return an empty array if the line isn't found, or data array.
```
Array
(
    [0] => '`DataBase`.`Table1`',
    [1] => '`DataBase`.`Table2`',
    [2] => '`DataBase`.`Table3`'
)
```
### Request during operation

PDOQueryInTables is receiver the PDO , he can execute everything that the can parent can.
```php
<?php
/*For example, the database structure here changes*/
$DB->exec("DROP TABLE Table2");

/*Need new scheme on database tables.*/
$arr = $DB->genQuery()->MoveQuery("%Required text%");
?>
```
### To include in request all databases available to the user
The new scheme of databases and their tables is necessary. And to specify argument the truth
```php
<?php
$arr = $DB->genQuery(true)->MoveQuery("%Required text%");
print_r($arr);
?>
```
Pay attention that the first the result of the current connection returns.
```
Array
(
    [0] => '`DataBase`.`Table1`',
    [1] => '`DataBase`.`Table3`',
    [2] => '`DataBase2`.`Table1`',
    [3] => '`DataBase2`.`Table2`',
    [4] => '`DataBase3`.`Table1`'
)
```

##Examples
Example from life of the author.

When CMS was attacked through the active XSS, was necessary to find the initial text of a harmful script in database tables. But the hoster didn't allow to use stored procedures in MySQL and forbade to change property of the group_concat_max_len server.

Therefore, this alternate class on PHP was created.

##Contact
Igor Kechaykin

* http://github.com/kichMan

##License
Licenced under GPL license http://www.gnu.org/licenses/gpl.html

###Changelog
* 2012.07.30 - Project beginning