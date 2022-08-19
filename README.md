# Chess forum (portfolio)

This repository contains a chess forum that is part of my portfolio.
The website is build with Symfony in its version 5.2.

## Limitations

The docker environnement who contains the website can only work on localhost with ssl certificate.
If you want to use another domain you need to change the virtual host in the apache folder and generate a new ssl certificate corresponding to your new domain by your own way.

I'm a french freelancer who target mainly client in france so the website will be only in french.

## Requirements

You need to have Docker with compose.

## Installation

### Clone the project

Clone the project in the directory you want to execute it.

```sh
cd /path/to/myfolder
git clone https://github.com/yanb94/echec.git
```

### Create an environnement file for the docker environnement

Create an environnent file for docker who contains the password for the database and the possibility to enable or not opcache revalidation of file base on timestamp.

```env
#.env
DB_PASSWORD=<your_database_password>
OPCACHE_REVALIDATE=1 # Recommended to leave on 1 for the first utilisation
```

### Create an environnement file for the website

Create a local environnement file for the website who contains:

-   The environnement to execute the website (prod recommended)
-   The app secret for symfony (You can generate one [here](https://coderstoolbox.online/toolbox/generate-symfony-secret))
-   The url of the database
-   The mailer url (See symfony documentation for [more informations](https://symfony.com/doc/current/mailer.html))
-   The trusted proxies
-   Your admin email

```env
#web/.env.local
APP_ENV=prod
APP_SECRET=<your_app_secret>
DATABASE_URL=mysql://root:<your_database_password>@mysql/echec
MAILER_DSN=<your_mailer_url>
TRUSTED_PROXIES=127.0.0.1,REMOTE_ADDR
ADMIN_MAIL=<your_admin_email>
```

### Start docker container

```sh
cd /path/to/myfolder
docker-compose up
```

### Open a new terminal in php-fpm docker container

```
cd /path/to/myfolder
docker-compose exec php-fpm bash
```

You will have to type all the following installation command in this opened terminal.

### Install the composer dependencies

```sh
composer update
```

### Install the javascript dependency

```sh
yarn install
```

### Install the website assets

```sh
yarn encore prod
```

### Initialize the database tables

```sh
php bin/console doctrine:schema:update --force
```

### Load the fixtures

```sh
php bin/console doctrine:fixture:load --group=init
```

This command will ask you if you want to purge the database respond yes.

## Execute tests

Before you can execute tests you need to realize this steps

### Create an environnement file for tests

Create a local test environnement file for the website who contains:

-   The database url
-   The app secret for tests (You can generate one [here](https://coderstoolbox.online/toolbox/generate-symfony-secret))
-   (Optionnal) The mailer url if you want to receive the email send during test (See symfony documentation for [more informations](https://symfony.com/doc/current/mailer.html))

```env
#web/.env.test.local
DATABASE_URL=sqlite:///%kernel.cache_dir%/test.db
APP_SECRET=<your_app_secret_for_test>
MAILER_DSN=smtp://localhost
```

### Create the test database

```sh
php bin/console doctrine:database:create --env=test
```

### Initialize the test database tables

```sh
php bin/console doctrine:schema:update --force --env=test
```

## Use grumPHP

[GrumPHP](https://github.com/phpro/grumphp) is a PHP code-quality tool who execute some tests before a commit and stop the commit if one this test fail to avoid to commit errors

To use grumPHP with this project you need to add some git hooks. To do so execute the following command:

```sh
bash addGitHook.sh
```

## Usage

### Initial Data

The fixtures from the installation add two users:

-   **admin** who has adminstrator right
-   **john** who is just a regular user

The both user has for password: _P8ssword?_

For some features about user work properly you need to change the email attach to these account in the user space.

The fixtures also add some post with message.

### Administration space

You can access the administration space at _https://localhost/admin_ if your are logged with administrator right.

In the administration space you can :

-   Manage categories
-   Manage legal documents
-   Moderate message or post

### Public space

In the public space you can :

-   Add a post (need to be logged)
-   Add a message to a post (need to be logged)
-   Follow a post and receive a notification by email when a new message is added (need to be logged)
-   Create an account
-   Reinitialise your forgotten password
-   Change your email (need to be logged)
-   Change your password (need to be logged)
-   Change other personnal data (need to be logged)
-   Contact the administrator through the contact form

### Change user rights

You can change user right by using the following command:

To attribute administrator right:

```sh
php bin/console user:admin:promote
```

To remove adminstrator right:

```sh
php bin/console user:admin:demote
```

## Troubleshooting

In the first start of the docker environnement the mysql container can take a long time to completely start.
This can create an issue where you can see during the installation the error message "Connection Refused".
The solution to this issue is to wait until the complete initialization of the container data and retry.
You can see when the container is ready in the docker log associate when you see this line and no more logs of the container without action:

```
[System] [MY-010931] [Server] /usr/sbin/mysqld: ready for connections. Version: '8.0.29'  socket: '/var/run/mysqld/mysqld.sock'  port: 3306  MySQL Community Server - GPL
```
