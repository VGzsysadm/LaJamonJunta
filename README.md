# LaJamonJunta

<center>Conjunta de jamones para Forocoches</center>

![Alt text](public/jamonjunta.jpg?raw=true "LaJamonJunta")

### Prerequisites

* PHP 7.3.0+ ( xml-zip-mysql-mbstring-intl)
* Mysql
* Composer

## Getting Started

Clone the project

```
https://github.com/VGzsysadm/LaJamonJunta.git
```
### Installing

Install dependencies

```
cd LaJamonJunta
composer install
```
### Prod mode

Create ENV variables in the host, confirm data at doctrine.yaml

Create the database and tables:

```
php bin/console doctrine:database:create
php bin/console doctrine:database:create
php bin/console doctrine:schema:validate
php bin/console doctrine:schema:update --force
```

## Built With

* [Symfony 4](https://symfony.com/doc/current/index.html)
* [Bootstrap](https://getbootstrap.com/docs/4.1/getting-started/introduction/)
* [Materials Icons](https://material.io/design)

## Authors

* **VGzsysadm** - *https://sysadm.es* - [@VGzsysadm](https://github.com/VGzsysadm)

## License

This project is licensed under the MIT License - see the [LICENSE.md](https://github.com/VGzsysadm/Inventory-app/blob/master/LICENSE.md) file for details


