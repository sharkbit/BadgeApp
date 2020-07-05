BadgeApp
========

Running on CentOs with PHP 7.4
 - Don't forget to enable the PHP repo.  A good resource is [here}(https://blog.remirepo.net/post/2019/12/03/Install-PHP-7.4-on-CentOS-RHEL-or-Fedora)
 
Steps to install:

1. Install proper php modules:
 - yum install php74 php74-php-fpm php74-php-gd php74-php-json php74-php-mbstring php74-php-mysqlnd php74-php-xml php74-php-xmlrpc php74-php-opcache php-pdo php-mbst* php-intl* php-dom* php-mysq* --skip-broken
 - yum install composer
2. Clone Repo:
 - git clone ssh://git@github.com:22/sharkbit/BadgeApp.git
3. Run Composer Intstall
 - composer install --prefer-dist --no-progress --no-suggest
4. Update directory permissions:
 - chown -R www-data.\<proper group> <root Git Dir>
 - find \<root Git Dir> -type f -exec chmod 664 {} \\;
 - find \<root Git Dir> -type d -exec chmod 775 {} \\;


5. Apache Setup DocumentRoot 
 - "/var/www/badgeApp/"
6. Test site /Requirements.php and verify green on required items.
7. Update DocumentRoot to proper launch point:
  - DocumentRoot "/var/www/badgeApp/backend/web/"
8. Configure your DB user and password in:
  - ./common/config/main-local.php
9. Configure Cookie and Debugging in:
  - ./backend/config/main-local.php
10. Run App by going to  /




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
