BadgeApp
========

Running on Ubuntu with PHP 8.3
 - Don't forget to enable the PHP repo
 
Steps to install:

1. Install proper php modules:
   - apt install php8.3 php8.3-fpm libapache2-mod-fcgid php8.3-cli php8.3-common php8.3-curl php8.3-dev php8.3-fpm php8.3-gd php8.3-igbinary php8.3-imagick php8.3-imap php8.3-intl php8.3-mbstring php8.3-mysql php8.3-opcache php8.3-phpdbg php8.3-readline php8.3-redis php8.3-soap php8.3-xml php8.3-xmlrpc php8.3-zip php8.3-zmq

   - apt install composer
2. Clone Repo:
   - cd /var/www/
   - git clone https://github.com/sharkbit/BadgeApp.git
3. Run Composer Intstall
   - cd BadgeApp
   - composer install --prefer-dist --no-progress --no-suggest
4. Update directory permissions:
   - sudo chown -R www-data.\<proper group> .
   - sudo find . -type f -exec chmod 664 {} \\;
   - sudo find . -type d -exec chmod 775 {} \\;
 5. Apache Setup DocumentRoot 
    - "/var/www/badgeApp/"
 6. Test site /Requirements.php and verify green on required items.
 7. Update DocumentRoot to proper launch point:
    - DocumentRoot "/var/www/badgeApp/backend/web/"
 8. Copy .htaccess.template to .htaccess
    - ./backend/web/.htacceess.template -> .htaccess
 9. Configure your DB user and password in:
    - ./common/config/main-local.php
10. Configure Cookie and Debugging in:
    - ./backend/config/main-local.php
11. Configure mail settings in:
    - ./backend/config/params.php
12. Run App by going to  /


Built On: Yii 2 Advanced Project Template
=========================================

Yii 2 Advanced Project Template is a skeleton [Yii 2](http://www.yiiframework.com/) application best for
developing complex Web applications with multiple tiers.

The template includes three tiers: front end, back end, and console, each of which
is a separate Yii application.

The template is designed to work in a team development environment. It supports
deploying the application in different environments.

Documentation is at [docs/guide/README.md](docs/guide/README.md).

[![Latest Stable Version](https://poser.pugx.org/yiisoft/yii2-app-advanced/v/stable.png)](https://packagist.org/packages/yiisoft/yii2-app-advanced)
[![Total Downloads](https://poser.pugx.org/yiisoft/yii2-app-advanced/downloads.png)](https://packagist.org/packages/yiisoft/yii2-app-advanced)
[![Build Status](https://travis-ci.org/yiisoft/yii2-app-advanced.svg?branch=master)](https://travis-ci.org/yiisoft/yii2-app-advanced)

DIRECTORY STRUCTURE
-------------------

```
common
    config/              contains shared configurations
    mail/                contains view files for e-mails
    models/              contains model classes used in both backend and frontend
    tests/               contains tests for common classes
console
    config/              contains console configurations
    controllers/         contains console controllers (commands)
    migrations/          contains database migrations
    models/              contains console-specific model classes
    runtime/             contains files generated during runtime
backend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains backend configurations
    controllers/         contains Web controller classes
    models/              contains backend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for backend application
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
frontend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains frontend configurations
    controllers/         contains Web controller classes
    models/              contains frontend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for frontend application
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
    widgets/             contains frontend widgets
vendor/                  contains dependent 3rd-party packages
environments/            contains environment-based overrides
```
