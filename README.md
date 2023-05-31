e-commerce-app
=========

A simple API that supports e-commerce related functionalities. It has an access system to the endpoints
so that all users have permission to query the endpoints but only some can, for example, add or delete products.

# Getting started

The following dependencies must be installed:

```
Php
Symfony
Nginx
Mysql
Composer
```

For the installation process, you have two options: one by using docker and the second one by installing
dependencies individually.

### Docker-Install

Download and install docker and run the following command:

```
docker compose up -d --build
```

After running it you can check that the app is working correctly
by clicking in the URL [http://localhost](http://localhost)

### Local-Install

You must install the dependencies php:8.2,mysql and a web server [apache,nginx]

### DB-Config

When installing the dependencies by the second alternative, it is necessary to create the database schema:

```
php bin/console doctrine:schema:create
```

Afterward, we apply migrations to the database, this is valid for both installation methods:

```
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

### ENV-Config

Create a copy of .env.template and rename it to .env

```
cp .\.env.template .env
```


It is necessary to specify the security phrase for the generation of JWT public and private keys:

```
JWT_PASSPHRASE=<!ChangeMe!>
```

In case you use the first installation option you do not need to update any other values.

For the second option, you must specify the database connection:

```
DATABASE_URL="mysql://[user]:[pass]@[host]:[post]/[db_app_name]?serverVersion=8.0.32&charset=utf8mb4"
```

### JWT-Config

In case of running the app using docker it is necessary to use the PHP container console:

```
docker-compose exec php /bin/bash
```

After being in the console it is necessary to generate the public and private keys for the use of JWT.
Valid for both installation methods:

```
php bin/console lexik:jwt:generate-keypair --overwrite
```

# Command Line

Creating users
-------------------------

The system has a functionality that allows you to create new users in the system. You need to specify:
user name, email, password, and the user's role. Remember that to perform some actions you must create a user with the administrator role ['ROLE_ADMIN'].
The command has an interactive function.

```
php bin/console app:create-user
```

Generating products
-------------------------

```
php bin/console app:product-fill
```

The command allows to generate given an integer that number of products with random values. This is ideal
if you intend to test the functionalities of the API without the need to add each product individually.

# Product attributes

```
sku
name
price
categories
tags
quantity_stock
quantity_sold
description
more_info
rating
attached_img
```

# Endpoints

#### [URL]/login

Allows you to create the access token for all actions on the products. Without it, you will not be able to
access any of the other endpoints. Always keep in mind that the lifetime of each token is only 30 minutes.

#### [URL]/product?page=1

It contains a 10-page list of all available products. These products can be filtered by each of their attributes.

#### [URL]/product/add

Send in the body of the request using the form-data, all the necessary data to create a product.
This endpoint is only for system administrators through the "POST" method.

#### [URL]/product/edit

Send in the body of the request using the form-data, all the necessary data to modify a product.
This endpoint is only for system administrators through the "POST" method. It is mandatory to define the attribute [sku].

#### [URL]/product/remove

Send in the body of the request using the form-data, the value [sku] associated with the product to delete.
This endpoint is only for system administrators using the "POST" method. Note that this action is irreversible
and you will lose all the information about the product.

#### [URL]/product/sell

Send in the request body using the form-data, the value [sku] associated with the product to sell using
the "POST" method. This action decreases the stock and increases the sales by 1 for the associated product.

#### [URL]/product/list-sold

Displays a paginated list based on 10 of the products that have made a sale.

#### [URL]/product/total-profit

Displays the total amount of sales made.

#### [URL]/product/out-stock

Displays a paginated list based on 10 of the products that are not available in stock.

# Importand

So far we have tested how to run the app and main functions but for some operating systems, you may have problems
associated with the permissions of the application. Remember to apply the appropriate permissions according to OS.

## Next MVP Aplication
* Implement CRUD to handle categories and tags with their associated entities.
* Allow search by similarity instead of just strict equality.
* Improve exception handling.