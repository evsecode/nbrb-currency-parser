# NBRB Exchange

NBRB Exchange is a web application that allows you to exchange foreign currencies into BYN.
Part of the application is a currency converter, which allows you to convert currencies into BYN. The converter is based on the NBRB API.
### Launch

If you are using [Lando](https://lando.dev/), then you need to run the following commands:

```shell
lando start
```

To run using OpenServer or other analogs,
you need to place the project in a folder that is accessible from the web server and configure the host so that the root folder is the `public` folder.

### Setup

For the application to work, you need to install dependencies using Composer.
If you are using Lando, you need to run the following command:

```shell
lando composer install
```

You can also use Composer installed locally, for this you need to run the following command:

```shell
composer install
```

### Database

For the application to work, you need to create a database and import the dump into it, which is located in the `database` folder.

# HIGHLY RECOMMENDED
After first start you need to run the following command to parse data from API:

```shell
lando php scripts/update_currencies.php
```