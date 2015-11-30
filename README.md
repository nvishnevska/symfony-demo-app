Demo App: Step Form With Timer
=====

Installation
====

* Configure DB connection ```app/config/parameters.yml```
* Create DB
```sh
$ php app/console doctrine:database:create
```
or
```sql
CREATE DATABASE symfony CHARACTER SET utf8 COLLATE utf8_general_ci;
```
* Create DB schema
```sh
php app/console doctrine:schema:update --force
```
* That's it.

