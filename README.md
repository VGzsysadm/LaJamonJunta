# LaJamonJunta

<center>Conjunta de jamones para Forocoches</center>

![Alt text](public/jamonjunta.jpg?raw=true "LaJamonJunta")

### Prerequisites

* PHP 7.1+ ( xml-zip-mysql-mbstring-intl)
* Mysql
* Composer

## Getting Started

Clone the project

```
git clone https://github.com/VGzsysadm/Inventory-app.git
```
### Installing

Install dependencies

```
cd Inventory-app
composer install
```
### Prod mode

Create ENV variables in the host, confirm data at doctrine.yaml

Create the database and tables:

```
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

Apache2 site

```
<VirtualHost *:443>
        ServerName lajamonjunta.online
        ServerAlias lajamonjunta.online www.lajamonjunta.online
        DocumentRoot /var/www/JAMONJUNTA/public
        <Directory /var/www/JAMONJUNTA/public>
                Options Indexes FollowSymLinks MultiViews
                AllowOverride All
                Require all granted
                <IfModule mod_rewrite.c>
                Options -MultiViews
                RewriteEngine On
                RewriteCond %{REQUEST_FILENAME} !-f
                RewriteRule ^(.*)$ index.php [QSA,L]
                </IfModule>
        </Directory>
        <Directory /var/www/html/JAMONJUNTA>
        Options FollowSymlinks
        </Directory>
        ErrorLog /var/log/apache2/JAMONJUNTA/project_error.log
        CustomLog /var/log/apache2/JAMONJUNTA/project_access.log combined
        SSLEngine on
        SSLCertificateFile /etc/letsencrypt/live/lajamonjunta.online/cert.pem
        SSLCertificateKeyFile //etc/letsencrypt/live/lajamonjunta.online/privkey                                                                                     .pem
        SSLCertificateChainFile /etc/letsencrypt/live/lajamonjunta.online/fullch                                                                                     ain.pem
        SetEnv APP_ENV prod
        SetEnv APP_SECRET 64732cb97c775c6cc0e480593ca9edfca2a7aad3
</VirtualHost>
<IfModule mod_headers.c>
        Header always add Strict-Transport-Security "max-age=15768000; includeSu                                                                                     bDomains; preload"
</IfModule>
<IfModule mod_reqtimeout.c>
   RequestReadTimeout header=20-40,MinRate=500 body=20-40,MinRate=500
</IfModule>
```

Execute the script config.php

## Built With

* [Symfony 4](https://symfony.com/doc/current/index.html)
* [Bootstrap](https://getbootstrap.com/docs/4.1/getting-started/introduction/)
* [Materials Icons](https://material.io/design)

## Authors

* **VGzsysadm** - *https://sysadm.es* - [@VGzsysadm](https://github.com/VGzsysadm)

## License

This project is licensed under the MIT License - see the [LICENSE.md](https://github.com/VGzsysadm/Inventory-app/blob/master/LICENSE.md) file for details


