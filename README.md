![Logo laminas](/public/img/laminas-logo.svg)

# Simple Web Demo Free Lottery Management Application

<table>
    <tr>
        <th>
            <p>This project is no longer maintained.</p>
            <p>At this time, the repository has been archived, and is read-only.</p>
            <h3>(c) Denis Puzik <b>scorpion3dd@gmail.com</b></h3>
        </th>
        <th>
            <img src="/public/img/readmy/3ds.jpg" alt="Logo 3ds">
        </th>
  </tr>
</table>


---
---

# INTRODUCTION

## RELEASE INFORMATION

### The Simple web demo "Free Lottery" management application project on Laminas Framework is a demo:

- examples of `design and implementation of basic architectural solutions` that `improve performance and security`:
  - built on the use of `triggers, functions, procedures and events in the MySql` database;
  - to `improve performance work with caching` at all levels: standard cache in Laminas Framework, cache in Doctrine, 
    cache customers objects and data to lists in `Redis`, which stores `data in RAM`;
  - `asynchronous thread processing potentially lengthy processes` tied to interaction with third-party services,
    using `Apache Kafka` programmatic message broker and using a scheme to exchange information between the sender
    and by the recipient, when data sources send information flows, and recipients process them as needed;
  - logging in the NoSql `MongoDB` database and the ability to view logs through the web interface;
- `program code` written according to the `"clean code"` principle, according to the concepts of `SOLID`, `DRY`, `DDD`,
  with the implementation of the current recommendations adopted by the `PSR`, written in Laminas Framework 
  using the most common `Design Patterns`;
- using most of the `standard components` of the Laminas framework and third-party most effective
  and common components offering proven solutions:
  - for databases (relational MySql and NoSql types, MongoDB type) - a set of `Doctrine` components:
    DBAL, ORM, doctrine-orm-module, ODM, doctrine-mongo-odm-module, data-fixtures, migrations;
  - for complex unit and integration testing - `phpunit` component set: laminas-test, php-code-coverage;
- `automatic check` of `style` and `"purity"`, `static analysis` of the written `code`;
  executing `PHP Unit and Integration tests`, providing the most `full code coverage` with tests and checking
  various combinations `all possible test cases`; which can be executed in the console after each
  change to the code and be sure that all checks are successfully passed;
- `automatic installation/updating/checking` in the project, by executing simple `composer` commands in the
  console from the `composer.json` file from the `scripts` section:
  - a set of all dependent components and libraries;
  - deleting the entire existing structure of the main database in MySql;
  - creating an empty structure of the main database in MySql,
  - generation of any volume, amount of data and filling them with tables of the main database in MySql;
- `Clean as Code` is an approach to code quality that eliminates many of the challenges that come with 
traditional approaches. As a developer, focus on maintaining high standards and taking responsibility 
specifically in the new code working on. `SonarQube` gives the tools to set high standards and take 
pride in knowing that code meets those standards.
- `optimal automated process for releasing software releases` use the principle of `CI / CD` in `Bitbucket
  Pipelines` use an integrated CI/CD service built into Bitbucket, which allows to `automatically build, test,
  and even deploy code` based on a configuration file in repository, in `Docker containers in the Cloud`.
- which `can guarantee` a `high speed of release of new versions` of the software
  product, `high quality of the code and the functionality` of the released
  software product, the `absence of bugs` and, as a result, `customer
  satisfaction` and `increase in sales of the manufactured software product` and,
  accordingly, an `increase in profits`.



---
---


# GETTING STARTED


## SYSTEM REQUIREMENTS


1. Web HTTP Server (example: Apache 2.4 HTTP server with mod_rewrite
   module or Nginx HTTP server)
2. PHP 7.4 or 8.0 with extensions gd, mbstring, xdebug, intl, pdo,
   mongodb, redis
3. Laminas Framework, Doctrine ORM, ODM, DataFixtures, Migrations, 
   PHPUnit and other
4. DB MySql 8.0, or later (active use triggers in tables, functions
   and procedures, events)
5. DB Redis 5.0 or later
6. MongoDB 5.0 or later
7. Apache Kafka
8. Bitbucket Pipelines use the principle of CI / CD
9. SonarQube


## INSTALLATION


1. In MySql create DB laminas_mvc_demo
~~~~~~mysql
CREATE DATABASE laminas_mvc_demo
CHARACTER SET utf8mb4
COLLATE utf8mb4_0900_ai_ci;
~~~~~~

2. In MySql create DB laminas_mvc_demo_integration
~~~~~~mysql
CREATE DATABASE laminas_mvc_demo_integration
CHARACTER SET utf8mb4
COLLATE utf8mb4_0900_ai_ci;
~~~~~~

3. In MySql create user laminas_mvc_demo for DB laminas_mvc_demo
~~~~~~mysql
CREATE USER 'laminas_mvc_demo'@'localhost' identified with mysql_native_password by 'laminas_mvc_demo123';
GRANT ALL PRIVILEGES ON laminas_mvc_demo.* TO laminas_mvc_demo@localhost;
~~~~~~

4. In MySql create user laminas_mvc_demo_integration for DB laminas_mvc_demo_integration
~~~~~~mysql
CREATE USER 'laminas_mvc_demo_integration'@'localhost' identified with mysql_native_password by 'laminas_mvc_demo_integration123';
GRANT ALL PRIVILEGES ON laminas_mvc_demo_integration.* TO laminas_mvc_demo_integration@localhost;
~~~~~~

5. In MySql, if necessary, set a parameter to relax the checking of non-deterministic functions.
~~~~~~bush
mysql -u root -p
~~~~~~

~~~~~~mysql
SET GLOBAL log_bin_trust_function_creators = 1;
~~~~~~

6. Clone a project code from the repository
~~~~~~bash
git clone https://scorpion3dd@bitbucket.org/3dscorpion7/laminas-mvc.git
~~~~~~

Execute the actions of the items 7.-13. - automatically by executing the 
executable file `project_init.sh`, after giving it permission to execute:
~~~~~~bash
sudo chmod +x ./project_init.sh
./project_init.sh
sudo chmod -R 777 ./data/logs
~~~~~~

Or execute the actions of the items 7.-13. individually manually.

7. Composer install the dependencies (Laminas Framework components and
    Doctrine)
~~~~~~bash
composer install
~~~~~~
or
~~~~~~bash
composer install --ignore-platform-reqs
~~~~~~

8. In the file /config/autoload/global.php, if necessary, change the
    parameters
9. Copy config files from distribute version to worked, if necessary, change the parameters
    (example set database password parameter)
```bash
cp ./config/autoload/local.php.dist ./config/autoload/local.php
```

```bash
cp ./config/autoload/module.doctrine-mongo-odm.local.php.dist ./config/autoload/module.doctrine-mongo-odm.local.php
```

```bash
cp ./config/autoload_test/local.php.dist ./config/autoload_test/local.php
```

```bash
cp ./config/autoload_test/module.doctrine-mongo-odm.local.php.dist ./config/autoload_test/module.doctrine-mongo-odm.local.php
```

```bash
cp ./config/autoload/laminas-developer-tools.local.php.dist ./config/autoload/laminas-developer-tools.local.php
```

```bash
cp ./config/development.config.php.dist ./config/development.config.php
```

```bash
cp ./public/.htaccess.dist ./public/.htaccess
```

10. Enable development mode:
    The Laminas Framework ships with [zf-development-mode](https://github.com/zfcampus/zf-development-mode)
    by default, and provides three aliases for consuming the script it ships with:
```bash
composer development-enable  # enable development mode
composer development-disable # disable development mode
composer development-status  # whether or not development mode is enabled
```
You may provide development-only modules and bootstrap-level configuration in
`config/development.config.php.dist`, and development-only application
configuration in `config/autoload/development.local.php.dist`. Enabling
development mode will copy these files to versions removing the `.dist` suffix,
while disabling development mode will remove those copies.

Development mode is automatically enabled as part of the skeleton installation process.
After making changes to one of the above-mentioned `.dist` configuration files you will
either need to disable then enable development mode for the changes to take effect,
or manually make matching updates to the `.dist`-less copies of those files.

11. Give write permissions to directories:
- /data/cache
- /data/cache_test
- /data/logs
- /data/DoctrineModule/cache
- /data/DoctrineMongoODMModule
- /public/img/captcha

Adjust permissions for `data` directory:
~~~~~~bash
sudo chown -R www-data:www-data ./data/cache
sudo chown -R www-data:www-data ./data/cache_test
sudo chown -R www-data:www-data ./data/logs
sudo chown -R www-data:www-data ./data/DoctrineModule/cache
~~~~~~
Adjust permissions for next directories:
~~~~~~bash
sudo chmod -R 777 ./data/cache
sudo chmod -R 777 ./data/cache_test
sudo chmod -R 777 ./data/logs
sudo chmod -R 777 ./data/DoctrineModule/cache
sudo chmod -R 777 ./data/DoctrineMongoODMModule
sudo chmod -R 777 ./public/img/captcha
~~~~~~

12. In MySql create empty structure for database laminas_mvc_demo (tables,
   triggers, functions, procedures, events) and generate fixtures data by
   executing the SQL script, run next command:
~~~~~~bash
composer project-init
~~~~~~

13. In MySql create empty structure for database laminas_mvc_demo_integration (tables,
   triggers, functions, procedures) and generate fixtures data by
   executing the SQL script, run next command:
~~~~~~bash
composer project-init-integration
~~~~~~

14. Create virtual host in you web server

### Web servers setup

#### Apache setup

To setup apache, setup a virtual host to point to the public/ directory of the
project and you should be ready to go! It should look something like below:

```apache
<VirtualHost *:80>
    ServerName laminas-mvc.demo.vms
    ServerAlias laminas-mvc.demo.vms
	DocumentRoot /path/to/laminas-mvc/public
	<Directory /path/to/laminas-mvc/public/>
          DirectoryIndex index.php
          AllowOverride All
          Order allow,deny
          Allow from all
          <IfModule mod_authz_core.c>
              Require all granted
          </IfModule>
    </Directory>
	ErrorLog ${APACHE_LOG_DIR}/error_laminas_mvc_demo.log
	CustomLog ${APACHE_LOG_DIR}/access_laminas_mvc_demo.log combined
</VirtualHost>
```

#### Nginx setup

To setup nginx, open your `/path/to/nginx/nginx.conf` and add an
[include directive](http://nginx.org/en/docs/ngx_core_module.html#include) below
into `http` block if it does not already exist:

```nginx
http {
    # ...
    include sites-enabled/*.conf;
}
```

Create a virtual host configuration file for your project under `/path/to/nginx/sites-enabled/zfapp.localhost.conf`
it should look something like below:

```nginx
server {
    listen       80;
    server_name  laminas-mvc.demo.vms;
    root         /path/to/laminas-mvc-demo/public;

    location / {
        index index.php;
        try_files $uri $uri/ @php;
    }

    location @php {
        # Pass the PHP requests to FastCGI server (php-fpm) on 127.0.0.1:9000
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_param  SCRIPT_FILENAME /path/to/laminas-mvc-demo/public/index.php;
        include fastcgi_params;
    }
}
```

Restart the Apache or Nginx, now you should be ready to go!

15. Reload Web Server (example Apache)
~~~~~~bash
sudo systemctl restart apache2
~~~~~~

16. Reload Web Server (example Nginx)
~~~~~~bash
sudo systemctl restart nginx
~~~~~~

17. Run all PHP automatic checks (code style checker, code static checker, Unit and Integration tests): 
~~~~~~bash
composer project-check-all
~~~~~~

18. Now you should be able to see the `Simple Web Demo Free Lottery Management Application` 
website by visiting the link "http://laminas-mvc.demo.vms/".

19. If you want to test the site performance on any amount of data, you can:
- set value to parameter `app.count_users` the required number of entries for new users - in the 
file `/config/autoload/local.php` for the website and in the file `/config/autoload_test/local.php` for 
integration tests, and save;
- automatically completely recreate all data sets for the website and for integration tests, i.e. delete all data 
and structures of MySql databases, create a new empty database structure, generate dummy datasets, 
perform all automatic checks - to do this, execute just one command:
~~~~~~bash
composer project-refresh-all
~~~~~~


## DESCRIPTION OF WEB DEMO APPLICATION


This `Simple Web Demo Free Lottery Management Application`.

The `Free Lottery application` performs user management.
Users with the "Administrator" role in `manual mode`, after
authentication in the application, with next credentials:
- email (login): admin@example.com
- password: admin123

Through the admin menu can:
- create, edit, delete records with user data;
- can change the status of users from active to inactive;
- can give other users access to a very valuable resource.

When the Administrator changes the user's data, for example, changes the
Status or the User's Access - after saving the changes in the database,
the user is `sent a notification letter to his email`.

Since the sending of emails affects `external systems`, this process may take
some time. In order to quickly release (not delay the process of sending letters)
the web interface to the Administrator for its further work, `Apache Kafka` is used,
which is a message broker and provides uninterrupted and convenient data exchange
in messages during `asynchronous communication between the Producer and the Consumer`.

Thus, the `Producer` script writes the necessary information in a specific data structure,
which is serialized into a string and transmitted as a message to Apache Kafka and returns
control back to the Administrator, `without delay`.

And after some short period of time in the `Consumer` script, all messages from Apache Kafka
are read in a stream and the text of each message is parsed in a certain way and
deserialized into a certain data structure, followed by `specific processing` (`sending a letter`
from the Administrator to the user with a specific message text).

And also in `automatic mode` for random users, the application can perform
a "Free Lottery", that is, change the status of users from active to
inactive and can give other active users the opportunity to access a
very valuable resource. This is possible through active use in DB MySql
`triggers in tables, functions and procedures, events`.

Another application "Free Lottery" manages roles and user access
privileges. Users with the "Administrator" role in manual mode, after
authentication in the application, through the admin menu can:
- create, edit, delete user roles;
- create, edit, remove user access privileges;
- make user roles hierarchical;
- assign access privileges to user roles;
- change user roles;

The "Free Lottery" application on the main page, freely available,
without user authentication, allows you to view a list of active
users with open access to a very valuable resource at a given time.

It shows how to:
* Implement roles and permissions
* Organize roles in database into an hierarchy
* Use Laminas\Permissions\Rbac component to implement role-based access
  control
* Use dynamic assertions to implement complex access control rules

The web part of this project consists of a public part and an admin panel,
which can only be entered after passing authentication, having entered
the email and password correctly.

Select `different languages` in the user interface and all services texts
in pages `translator to selected language`.

For great performance application use DB `Redis`, which save data to RAM and
operations write and read from Redis running very quickly.

The "Free Lottery" application during operation `writes logs` to a `text file`,
in the `MongoDB database` and in the `MySql database`.
In order to view the logs from the mongo database, you need to go to the "Logs" page,
on which you can view a list of logs, you can click on a specific log and view detailed data about the log,
you can edit or delete a specific log, you can manually add a new entry for a new log.



---
---


# ARCHITECTURE


## SKELETON APPLICATION BY LAMINAS FRAMEWORK

### Introduction

This is a skeleton application using the Laminas Framework:
- MVC layer and module systems.
- Security.
- Performance.
- Standard Design Patterns.
- Main Components.
- PHP Standards Recommendations (PSR).
- Principles of Clean Code (SOLID, DRY) and Clean Architecture in PHP.

### Advantages

- Develop websites `much faster` with Laminas Framework, because Laminas Framework provides `many
  standards and loosely coupled components`. What gives `easier interaction
  with team members`.
- Using the `concept of modules` Laminas Framework makes it `easy to scale` the website.
- The `Model-View-Controller` (MVC) pattern used in Laminas Framework allows to
  implement `Domain Driven Design` (DDD) separate the business logic from
  the presentation layer, making `the code structure more consistent and
  manageable`.
- Instead of interacting directly with the database through SQL queries,
  using `Doctrine Object-Relational Mapping` (ORM, ODM) allows you to `manage
  the structure and relationships of data` by accessing the database in
  an `object-oriented style`.
- The use of components such as `filters` and `form validators`, HTML
  output `escapers` and `crypto algorithms`, `human check` (Captcha) and
  `Cross-Site Request Forgery` (CSRF) form elements allow to create `the
  most secure websites`.

### Security

- The `input script` (index.php) - is the only PHP file available to
  web visitors. All other PHP scripts are outside the document root
  directory of the Apache web server. This is much safer than giving all
  visitors access to any of the PHP scripts.
- `Request Routing` (Routing) - allows to set `strict rules` for how an
  acceptable web page URL should look like. If the user enters an invalid
  URL into the browser's navigation bar, they are automatically directed
  to an error page.
- `Access control lists` (ACL) and `Role-Based Access Control` (RBAC) -
  allow to set `rules to allow` or `deny access` to specific resources on
  website.
- Web form `validators` and `filters` - allow you to be sure that harmful
  data entered by the user will not pass through the web form. `Filters`,
  for example, allow you to trim the input string or remove HTML tags from it.
  `Validators` are used to make sure that the data submitted through a web
  form meet certain rules.
- `Captcha` and `Cross-Site Request Forgery` (CSRF) form elements - used
  to prevent hacker attacks.
- Laminas component `Escaper` - allows you to strip unwanted HTML tags from
  data output to a web page.
- Support for `cryptography` - allows you to store important data, such
  as passwords, encrypted with strong cryptographic algorithms, which are
  difficult to crack.

### Performance

- `Lazy class autoloading` - classes are loaded only when needed.
- `Efficient loading of services and plugins` in Laminas Framework - business logic
  classes are instantiated only when really needed. This is achieved
  through the `service manager`, the central container for all of the
  application's services.
- `Caching support` - PHP has several caching extensions (such as
  Memcached) that can be used to speed up sites built with Laminas Framework.

### Standard Design Patterns

- The `Model-View-Controller` pattern is used in all modern PHP frameworks. In an MVC application,
  you separate your code into three categories: `models` (your business logic), `views` (presentation),
  and `controllers` (code responsible for user interaction). With MVC, you can reuse the components of
  this triad in other projects. It is also easy to replace any part of the triad. For example, you can
  easily replace a view with another view without changing the business logic.
- `Domain Driven Design` (DDD) in Laminas Framework, you will be dividing the model layer even further into: `entities` -
  classes that work with database tables, `repositories` - classes that allow you to get entities from the database,
  `value objects` - model classes, without an identifier, and `services` - that is, classes responsible for business logic.
  Additionally, you will have web `forms` - model classes responsible for user input, `form helpers` in the form of
  `validators` and `filters`. You will have a view rendering strategy that determines what how the page will be rendered.
  By default, to get an HTML page, the `.phtml view template` is rendered using the `PhpRenderer` class,
  which lives in the Laminas\View\Renderer namespace. This strategy works well 99% of the time. But sometimes you may need
  to render something other than the HTML page, for example, response in JSON format or a news feed (RSS feed).
  View helpers, reusable plugins designed to display different content on a web page, and probably other types of models.
- `Aspect Oriented Design template` - everything in Laminas Framework is based on events. When a user requests a web page,
  an event is fired. An observer can respond to an event. `Observers` can be divided into listeners (listener)
  and `subscribers` (subscriber). This allows you to expand the capabilities of the framework. For example,
  the Laminas\Router component parses the URL and determines which controller to call.
- The `Strategy` template - is just a class that encapsulates an algorithm. And you can use different algorithms
  if certain conditions are triggered. For example, a renderer has several strategies for rendering a web page
  (for example, it can generate an HTML page, a JSON array, or an RSS feed based on the HTTP headers request).
- `Adapter` pattern - allows you to tailor a general purpose class to a specific use case. For example,
  the Laminas\Db component provides access to a database regardless of the type of DBMS. Internally, it uses adapters
  for each supported DBMS (SQLite, MySQL, PostgreSQL, etc.)
- `Factory` pattern - you can create an instance of a class using the new operator. Or can create it with a factory.
  A factory is just a class that creates other objects. Factories are useful because they make `dependency injection` easier.
  It also makes it `easier to test models and controllers`.
- The `Service Manager` template - is a centralized repository of all the services available in the application.
  Extract services from the service manager not anywhere in the code, but inside the factory (factory).
  When you create an object, extract the services it depends on and pass those services (dependencies) to
  the object's constructor. This is also called `dependency injection`.
- `Singleton` pattern - each service in the centralized repository of all services available in the application
  exists in only one instance.

### Main Components

- `Laminas\EventManager` - allows you to create events (events) and register event handlers.
- `Laminas\ModuleManager` - In Laminas Framework based sites everything is made up of modules and this component allows you to load modules.
- `Laminas\ServiceManager` - This is the central repository for all services available in the application.
  Services contain business application logic.
- `Laminas\Http` - Provides a simple interface for working with Hypertext Transfer Protocol (HTTP) requests.
- `Laminas\Mvc` - Support for the Model-View-Controller (Model-View-Controller) pattern.
- `Laminas\View` - Provides a system of helpers (view helpers) and output escapers. Used in the presentation (view) layer.
- `Laminas\Form` - Helps to collect data entered by the user, as well as filter it, validate it, and display forms on a web page.
- `Laminas\InputFilter` - Allows you to set filtering and validation rules for data entered by the user in a web form.
- `Laminas\Filter` - Provides a set of commonly used filters such as string trimmer.
- `Laminas\Validator` - Provides a set of commonly used validators.
- `Laminas\Mvc\I18n` - Provides a simple localize view templates, view helpers, forms, validator messages, services.
- `Laminas\Log\Logger` - Provides a simple logger to save records to file and MongoDB.
- `Doctrine\ORM` - Provides allows to manage the structure and relationships of data
  by accessing the database in an object-oriented style.
- `Doctrine\Common\DataFixtures` - Provides a simple generate data fixtures to tables in DB.
- `Laminas\Test\PHPUnit` - Provides a simple PHP unit tests all code in controllers, services, repositories, entities.

### PHP Standards Recommendations (PSR)

- `PSR-1 - Basic Coding Standard` - used in all codes in Laminas Framework.
- `PSR-3 - Logger Interface` - used in `laminas-log` component in Laminas Framework.
- `PSR-4 - Autoloading Standard` - in Laminas Framework, the recommended `directory structure` follows this standard and
  used in `autoload` all components, libraries, packages in Laminas Framework by composer.
- `PSR-6 - Caching Interface` - used in `laminas-cache` component in Laminas Framework.
- `PSR-7 - HTTP Message Interface` - used in `Laminas\Stdlib\Message`, `Laminas\Http\Request`, `Laminas\Http\Response`
  components in Laminas Framework.
- `PSR-11 - Container Interface` - used in `Interop\Container\ContainerInterface`,
  `Laminas\ServiceManager\Factory\FactoryInterface`, `Laminas\ServiceManager\ServiceManager` components in Laminas Framework.
- `PSR-12 - Extended Coding Style Guide` - used in all codes in Laminas Framework.
- `PSR-13 - Hypermedia Links` - used in `Laminas\View\Helper\Url`, `Laminas\Uri\Http` components in Laminas Framework.
- `PSR-14 - Event Dispatcher` - used in `EventManagerInterface`, `Laminas\Mvc\MvcEvent`, `Doctrine\Common\EventManager`,
  `Doctrine\DBAL\Events` component in Laminas Framework.

## Principles of Clean Code (SOLID, DRY) and Clean Architecture in PHP

### SOLID

- `Single Responsibility Principle` (SRP)
- `Open/Closed Principle` (OCP)
- `Liskov Substitution Principle` (LSP)
- `Interface Segregation Principle` (ISP)
- `Dependency Inversion Principle` (DIP)

### DRY

- Need completely get rid of duplicate code.


## COMPOSER

Composer is a `dependency manager` for the PHP programming language.
An additional feature of Composer is that it offers utilities for
hooking packages to PHP `autoload` by PSR-4.
`Custom scripts` that do not fit one of the predefined event name above,
can either run them with run-script or also run them as `native Composer commands`.

The handler for automatic code style checking can be executed by running:
```bash
composer cs-check
```

The handler for automatic code style fixing errors can be executed by running:
```bash
composer cs-fix
```

The handler for automatic code static checking can be executed by running:
```bash
composer stan-check
```

The handler for automatic checking all tests in module application can be executed by running:
```bash
composer test-application
```

The handler for automatic checking unit tests in module application can be executed by running:
```bash
composer test-application-unit
```

The handler for automatic checking integration tests in module application can be executed by running:
```bash
composer test-application-integration
```

The handler for automatic checking all tests in module user can be executed by running:
```bash
composer test-user
```

The handler for automatic checking unit tests in module user can be executed by running:
```bash
composer test-user-unit
```

The handler for automatic checking integration tests in module user can be executed by running:
```bash
composer test-user-integration
```

The handler for automatic checking unit tests in all modules can be executed by running:
```bash
composer test-unit
```

The handler for automatic checking integration tests in all modules can be executed by running:
```bash
composer test-integration
```

The handler for automatic checking all, unit and integration tests in all modules can be executed by running:
```bash
composer test-all
```

The handler for automatic checking all, unit and integration tests in all modules with coverage
can be executed by running:
```bash
composer test-all-coverage
```

The handler for automatic drop all Databases can be executed by running:
```bash
composer db-drop
```

The handler for automatic drop all Databases for integration tests can be executed by running:
```bash
composer db-drop-integration
```

The handler for automatic create all Databases can be executed by running:
```bash
composer db-create
```

The handler for automatic create all Databases for integration tests can be executed by running:
```bash
composer db-create-integration
```

The handler for automatic update Databases by Doctrine Migrations from file with SQL scripts 
updating can be executed by running:
```bash
composer doctrine-migrations-generate
composer doctrine-migrations-migrate
composer doctrine-migrations-status
```

The handler for automatic loading fixtures to all Databases can be executed by running:
```bash
composer db-loading-fixtures
```

The handler for automatic loading fixtures to all Databases for integration tests can be executed by running:
```bash
composer db-loading-fixtures-integration
```

The handler for automatic create all Databases and loading fixtures to all Databases can be executed by running:
```bash
composer project-init
```

The handler for automatic create all Databases and loading fixtures to all Databases for integration tests
can be executed by running:
```bash
composer project-init-integration
```

The handler for automatic checking code style, code static, unit tests in all modules can be executed by running:
```bash
composer project-check-unit
```

The handler for automatic checking code style, code static, integration tests in all modules can be executed by running:
```bash
composer project-check-integration
```

The handler for automatic checking code style, code static, all unit and integration tests in all modules
can be executed by running:
```bash
composer project-check-all
```

The handler for automatic drop all Databases, create all Databases and loading fixtures to all Databases,
checking code style, code static, unit tests in all modules can be executed by running:
```bash
composer project-refresh-unit
```

The handler for automatic drop all Databases, create all Databases and loading fixtures to all Databases,
checking code style, code static, integration tests in all modules can be executed by running:
```bash
composer project-refresh-integration
```

The handler for automatic drop all Databases, create all Databases and loading fixtures to all Databases,
checking code style, code static, all unit and integration tests in all modules can be executed by running:
```bash
composer project-refresh-all
```

Start SonarQube (and don't close):
```bash
composer sonar-start
```

Run scanner SonarQube:
```bash
composer sonar-scanner
```

The handler for automatic checking code style, code static, all unit and integration tests in all modules,
run scanner SonarQube - can be executed by running:
```bash
composer project-check-sonar
```

The handler for automatic drop all Databases, create all Databases and loading fixtures to all Databases,
checking code style, code static, all unit and integration tests in all modules, run scanner SonarQube - can 
be executed by running:
```bash
composer project-refresh-sonar
```


## CI/CD AUTOMATION


Continuous Integration, Delivery, and Deployment (CI/CD) - is at the heart
of the success of DevOps practices.

The principle of CI / CD is focused on creating an optimal automated process
for releasing software releases.

Teams that put CI/CD into practice receive constant feedback, delivering
software to end users as quickly as possible, by studying user experience
and embodying their ideas in the next releases.


### BITBUCKET PIPELINES AUTOMATION


Bitbucket Pipelines runs all builds in Docker containers using an image
that specify at the beginning of configuration file. Can easily use PHP
with Bitbucket Pipelines by using one of the official PHP Docker images
on Docker Hub.

Bitbucket Pipelines is an integrated CI/CD service built into Bitbucket.
It allows to automatically build, test, and even deploy code based on a
configuration file in repository. Essentially, create Docker containers
in the cloud.

Each time a pull request is created, a build is started in an empty, reference
environment of the Docker container, with all the advantages of a fresh system,
customized and configured in which install all necessary services, extensions
for PHP are installed, all dependencies of libraries, components and packages
are installed, and then all checks are performed.

Then inside these containers, run commands (like might on a local machine)
with all checking: check code style, check static code (stan check), check
all PHP unit and integration tests - automation of verification of absolutely
almost all test cases.

Which can guarantee a high speed of release of new versions of the software
product, high quality of the code and the functionality of the released
software product, the absence of bugs and, as a result, customer
satisfaction and increase in sales of the manufactured software product and,
accordingly, an increase in profits.



## DATABASES

### DB MySql

DB MySql - it is the main relational database that stores all application
data in the appropriate tables.

The `Database diagram`, table structure, relationships between tables is
shown below:
![Database diagram](/public/img/readmy/zf3_db_diagrams.png)

Some tables have triggers that automatically perform auxiliary operations,
such as logging all performed data write/modify/delete operations.
Example table `user` has next triggers:
- `user_AFTER_INSERT` with SQL code:
```mysql
create trigger user_AFTER_INSERT after insert on user for each row
BEGIN
    DECLARE v_user_id INT DEFAULT 0;
    IF (@SESSION.user_id IS NOT NULL ) THEN
        SET v_user_id = @SESSION.user_id;
    END IF;
    INSERT INTO `user_log`
    (`user_id`, `action_user_id`, `action`, `changed`, `date_action`)
    VALUES (NEW.`id`, v_user_id, 1, '', NOW());
END;
```
- `user_AFTER_UPDATE` with SQL code:
```mysql
create trigger user_AFTER_UPDATE after update on user for each row
BEGIN
    DECLARE v_user_id INT DEFAULT 0;
    DECLARE v_changed LONGTEXT DEFAULT '';
    IF (NEW.`email` <> OLD.`email` or
        NEW.`full_name` <> OLD.`full_name` or
        NEW.`description` <> OLD.`description` or
        NEW.`password` <> OLD.`password` or
        NEW.`status` <> OLD.`status` or
        NEW.`access` <> OLD.`access` or
        NEW.`gender` <> OLD.`gender` or
        NEW.`date_birthday` <> OLD.`date_birthday` or
        NEW.`date_created` <> OLD.`date_created` or
        NEW.`pwd_reset_token` <> OLD.`pwd_reset_token` or
        NEW.`pwd_reset_token_creation_date` <> OLD.`pwd_reset_token_creation_date`)
    THEN
        IF (NEW.`email` <> OLD.`email`) THEN
            SET v_changed = CONCAT(v_changed, 'email = ', NEW.`email`, '; ');
        END IF;
        IF (NEW.`full_name` <> OLD.`full_name`) THEN
            SET v_changed = CONCAT(v_changed, 'full_name = ', NEW.`full_name`, '; ');
        END IF;
        IF (NEW.`description` <> OLD.`description`) THEN
            SET v_changed = CONCAT(v_changed, 'description = ', NEW.`description`, '; ');
        END IF;
        IF (NEW.`status` <> OLD.`status`) THEN
            SET v_changed = CONCAT(v_changed, 'status = ', NEW.`status`, '; ');
        END IF;
        IF (NEW.`access` <> OLD.`access`) THEN
            SET v_changed = CONCAT(v_changed, 'access = ', NEW.`access`, '; ');
        END IF;
        IF (NEW.`gender` <> OLD.`gender`) THEN
            SET v_changed = CONCAT(v_changed, 'gender = ', NEW.`gender`, '; ');
        END IF;
        IF (NEW.`date_birthday` <> OLD.`date_birthday`) THEN
            SET v_changed = CONCAT(v_changed, 'date_birthday = ', NEW.`date_birthday`, '; ');
        END IF;
        IF (NEW.`pwd_reset_token` <> OLD.`pwd_reset_token`) THEN
            SET v_changed = CONCAT(v_changed, 'pwd_reset_token = ', NEW.`pwd_reset_token`, '; ');
        END IF;
        IF (NEW.`pwd_reset_token_creation_date` <> OLD.`pwd_reset_token_creation_date`) THEN
            SET v_changed = CONCAT(v_changed, 'pwd_reset_token_creation_date = ', NEW.`pwd_reset_token_creation_date`, '; ');
        END IF;
        IF (@SESSION.user_id IS NOT NULL ) THEN
            SET v_user_id = @SESSION.user_id;
        END IF;
        INSERT INTO `user_log`
        (`user_id`, `action_user_id`, `action`, `changed`, `date_action`)
        VALUES (OLD.`id`, v_user_id, 2, v_changed, NOW());
    END IF;
END;
```
- `user_BEFORE_DELETE` with SQL code:
```mysql
create trigger user_BEFORE_DELETE before delete on user for each row
BEGIN
    DECLARE v_user_id INT DEFAULT 0;
    DECLARE v_archive INT DEFAULT 3;
    IF (@SESSION.user_id IS NOT NULL) THEN
        SET v_user_id = @SESSION.user_id;
    END IF;
    IF (@SESSION.archive IS NOT NULL) THEN
        SET v_archive = @SESSION.archive;
    END IF;
    INSERT INTO `user_log`
    (`user_id`, `action_user_id`, `action`, `changed`, `date_action`)
    VALUES (OLD.`id`, v_user_id, v_archive, '', NOW());

    DELETE FROM `user_role` WHERE `user_id` = OLD.`id`;
END;
```

To simulate business processes the Database also uses basic procedures:
- `moveUsersArchives` with SQL code:
```mysql
create procedure moveUsersArchives()
BEGIN
    DECLARE v_user_id_archived INT DEFAULT 0;
    DECLARE v_user_id INT DEFAULT 0;
    DECLARE v_done integer DEFAULT 0;
    DECLARE v_id decimal(20, 0) DEFAULT 0;
    DECLARE v_email varchar(128) DEFAULT '';
    DECLARE v_full_name varchar(256) DEFAULT '';
    DECLARE v_description varchar(1024) DEFAULT '';
    DECLARE v_password varchar(128) DEFAULT '';
    DECLARE v_status integer DEFAULT 0;
    DECLARE v_access integer DEFAULT 0;
    DECLARE v_gender integer DEFAULT 0;
    DECLARE v_date_birthday DATETIME;
    DECLARE v_date_created DATETIME;
    DECLARE v_date_updated DATETIME;
    DECLARE v_pwd_reset_token varchar(32) DEFAULT '';
    DECLARE v_pwd_reset_token_creation_date DATETIME;

    DECLARE v_users_cursor CURSOR FOR
        SELECT u.`id`, u.`email`, u.`full_name`, u.`description`, u.`password`, u.`status`,
            u.`access`, u.`gender`, u.`date_birthday`, u.`date_created`, u.`date_updated`,
            u.`pwd_reset_token`, u.`pwd_reset_token_creation_date`
        FROM `user` u
            INNER JOIN user_role ur on u.`id` = ur.`user_id`
            INNER JOIN role r on r.`id` = ur.`role_id`
        WHERE u.`status` = 2 AND r.`name` = 'Guest';

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = 1;

    IF (@SESSION.user_id IS NOT NULL ) THEN
        SET v_user_id = @SESSION.user_id;
    END IF;

    OPEN v_users_cursor;

    users_loop:
    LOOP
        FETCH v_users_cursor INTO v_id, v_email, v_full_name, v_description, v_password,
            v_status, v_access, v_gender, v_date_birthday, v_date_created, v_date_updated,
            v_pwd_reset_token, v_pwd_reset_token_creation_date;

        IF v_done = 1 THEN
            LEAVE users_loop;
        END IF;

        INSERT INTO `user_archives`
        (`email`, `full_name`, `description`, `password`, `status`, `access`, `gender`,
         `date_birthday`, `date_created`, `date_updated`, `pwd_reset_token`,
         `pwd_reset_token_creation_date`, `date_archived`, `archived_user_id`)
        VALUES (v_email,  v_full_name, v_description, v_password, v_status, v_access, v_gender,
                v_date_birthday, v_date_created, v_date_updated, v_pwd_reset_token,
                v_pwd_reset_token_creation_date, NOW(), v_user_id);
        SET v_user_id_archived = LAST_INSERT_ID();

        UPDATE `user_role` SET `user_id` = 0, `user_archived_id` = v_user_id_archived WHERE `user_id` = v_id;

        SET @SESSION.archive = 4;
        DELETE FROM `user` WHERE `id` = v_id;
    END LOOP users_loop;
    CLOSE v_users_cursor;
END;
```

- `setUsersAccesses` with SQL code:
```mysql
create procedure setUsersAccesses()
BEGIN
    DECLARE v_max_id INT DEFAULT 0;
    SELECT MAX(id) FROM `user` INTO v_max_id;
    UPDATE `user` u
        INNER JOIN user_role ur on u.`id` = ur.`user_id`
        INNER JOIN role r on r.`id` = ur.`role_id`
    SET u.`access` = 1, u.`date_updated` = NOW()
    WHERE u.`status` = 1 AND r.`name` = 'Guest' AND u.`id` = randomInt(v_max_id);
END;
```

- `setUsersArchives` with SQL code:
```mysql
create procedure setUsersArchives()
BEGIN
    DECLARE v_max_id INT DEFAULT 0;
    SELECT MAX(id) FROM `user` INTO v_max_id;
    UPDATE `user` u
        INNER JOIN user_role ur on u.`id` = ur.`user_id`
        INNER JOIN role r on r.`id` = ur.`role_id`
    SET u.`status` = 2, u.`date_updated` = NOW()
    WHERE u.`status` = 1 AND r.`name` = 'Guest' AND u.`id` = randomInt(v_max_id);
END;
```

- `setUsersNotAccesses` with SQL code:
```mysql
create procedure setUsersNotAccesses()
BEGIN
    UPDATE `user` SET `access` = 2, `date_updated` = NOW() WHERE `access` = 1;
END;
```

The Database also uses events that run the main procedures on a given schedule.
- `moveArchives` with SQL code:
```mysql
create event moveArchives on schedule
    every '1' HOUR starts '2023-01-24 00:42:59' enable do
    BEGIN
    CALL moveUsersArchives();
END;
```
- `setAccesses`;
- `setArchives`;
- `setNotAccesses`.

The Database also uses helper function:
- `randomInt` with SQL code:
```mysql
create function randomInt(count int) returns tinyint
BEGIN
    DECLARE vResult INT DEFAULT 0;
    SELECT FLOOR((RAND() * 100)) INTO vResult;

    RETURN vResult;
END;
```


### DB Mongo

MongoDB - is an additional NoSqL documentary Database in which application
logs are written, it consists of one collection `logs`.



### DB Redis

Redis is an auxiliary database that stores data in RAM in the form
of key-value records `role:`, lists `roles`, sorted lists `role:set`
and speeds up the application by replacing heavier references to
the main relational DB.



## PROGRAMMATIC MESSAGE BROKER

### Apache Kafka

`Asynchronous thread processing potentially lengthy processes` tied
to interaction with third-party services, using `Apache Kafka`
programmatic message broker and using a scheme to exchange information
between the sender and by the recipient, when data sources send
information flows, and recipients process them as needed.

The handler for running `zookeeper-server` can be executed by running:
```bash
.\bin\windows\zookeeper-server-start.bat .\config\zookeeper.properties
```

The handler for running `Kafka` can be executed by running:
```bash
.\bin\windows\kafka-server-start.bat .\config\server.properties
```

The handler for viewing the list of topics in `Kafka` can be
executed by running:
```bash
.\bin\windows\kafka-topics.bat  --list --bootstrap-server localhost:9092
```

The handler for viewing the list of messages in topic `user_notification`
in `Kafka` can be executed by running:
```bash
.\bin\windows\kafka-console-consumer.bat  --bootstrap-server localhost:9092 --topic user_notification
```



## LOGGING

### Logging information in the application

1. in file `logfile.log`, example in console run next commands:
```bash
cd /data/logs/logfile.log
tail -n 100 -f /data/logs/logfile.log
```
2. in MongoDB `laminas_mvc_demo` collection `logs`, example run next query:
```bash
db.getCollection('logs').find({priority:6, timestamp:{$gte:ISODate("2023-01-24"),$lt:ISODate("2023-02-24")}});
```
3. in MySql DB `z3.demo` tables `user_log` and `user_role_log`



## AUTOMATION QA TOOLS

This project has a QA tooling, with configuration for each of:

- [phpcs](https://github.com/squizlabs/php_codesniffer)
- [phpstan](https://phpstan.org)
- [phpunit tests](https://phpunit.de)
- [sonar qube](https://docs.sonarqube.org/latest/)

Provide aliases for each of these tools in the Composer configuration.

### Automatic code style checker

```bash
# Run CS checks:
composer cs-check
```

![phpcs](/public/img/readmy/phpcs.jpg)

```bash
# Fix CS errors:
composer cs-fix
```

### Automatic code static checker

```bash
# Run Stan check:
composer stan-check
phpstan analyse --level=7 --memory-limit=1024M --xdebug
```

![phpstan](/public/img/readmy/phpstan.jpg)

```bash
# Or run all checks automatic:
composer project-check-unit
```

### Automatic tests checker

For a successful, cyclical, smooth and error-free release of each version
of the software product testers must write a `test plan`, that is, make a
`very large number` (from several dozen, up to several hundred) `test cases`
to cover `absolutely all possible use cases users of the functionality` of
the software product (for example, each route), for `absolutely all
business processes`.

And then, before the release of each version, testers must `manually
execute all previously written test plan`, that is, they must check all
hundreds of test cases. Such a very large number of test cases for manual
verification, makes the work of the tester routine and introduces the
concept of `"human factor"`, which increases the likelihood of them making
an `error` during the check.

And with subsequent releases of versions of the software product, there
may be a need to perform `integration testing`, that is, checking not only
the changes in the latest version, but also the entire functionality,
which means that there will already be `several thousand test cases for
manual verification`.

Therefore, once writing all the unit and integration tests for each
test case, even if for each route there will be several dozen of them -
you can almost completely `automate` all the routine, manual work of
testers and eliminate the human factor in the work of testers almost
completely.

Thus, it is possible to `automate up to 90% of all manual work` of testers.

The `remaining 10%`, something that cannot be automated, such as
intellectual, creative, research test cases, tests to identify new ways
to use existing functionality, to search for new ones business processes
and, as a result, new offers to users - can remain testers for manual
execution by them.

`Automation of verification of absolutely almost all test cases`, once
writing all the unit and integration tests, allows you to check everything
at any time, repeatedly in a fully automatic mode, for example, at each
code changes, with each merge to the base branch, with each release of a
new version, periodically the entire working system.

Which `can guarantee` a `high speed of release of new versions of the
software product`, `high quality of the code and the functionality of the
released software product`, the `absence of bugs` and, as a result, customer
satisfaction error-free and trouble-free operation of the system they
use, and as a result, an `increase in sales of the manufactured software
product` and, accordingly, an `increase in profits`.


#### PHP Unit tests

All Unit tests running in `total isolation`, without connecting
to any external services, such as databases, message brokers, etc.

Which provides very `high execution speed` and allows developers
to constantly run the entire unit test suite after each code
change and before each merge to the base branch.

All calls to any external services are `mute`.

PHP Unit tests writing with `full coverage for all test cases`.

This project has a complete PHP Unit tests with `full coverage
code` for all methods in:
- Controllers;
- Services;
- Repositories;
- Entities;

Running all PHP Unit Tests:
```bash
composer test-unit
```

![phpunit_unit](/public/img/readmy/phpunit_unit.jpg)

or by module Application:
```bash
composer test-application-unit
```
or by module User:
```bash
composer test-user-unit
```


#### PHP Integration tests

All Integration tests running with `real connecting to all external
services`, such as databases, message brokers, etc.

PHP Integration tests writing with `full coverage for all test cases`.

This project has a complete PHP Integration tests for all methods in:
- Controllers;


Running all PHP Integration Tests:
```bash
composer test-integration
```
or by module Application:
```bash
composer test-application-integration
```

![phpunit_integration](/public/img/readmy/phpunit_integration.jpg)

or by module User:
```bash
composer test-user-integration
```


#### Running all PHP Unit and Integration Tests

```bash
composer test-all
```

With the most complete coverage of the source code on PHP.

In all modules:

![phpunit_coverage_all](/public/img/readmy/phpunit_coverage_all.jpg)

In module Application:

![phpunit_coverage_application](/public/img/readmy/phpunit_coverage_application.jpg)

In module User:

![phpunit_coverage_user](/public/img/readmy/phpunit_coverage_user.jpg)



### Automatic SonarQube

#### SonarQube description

SonarQube is a self-managed, automatic code review tool that systematically 
helps deliver clean code. As a core element of Sonar solution, SonarQube 
integrates into existing workflow and detects issues in code to help perform 
continuous code inspections of projects. 
The tool analyses programming languages and integrates into CI pipeline and 
DevOps platform to ensure that code meets high-quality standards.

Writing `clean code` is essential to maintaining a healthy codebase. 
We define clean code as code that meets a certain defined standard, 
i.e. code that is reliable, secure, maintainable, readable, and modular, 
in addition to having other key attributes. This applies to all code: 
source code, test code, infrastructure as code, glue code, scripts, etc.

Sonar's `Clean as Code` approach eliminates many of the pitfalls that arise from 
reviewing code at a late stage in the development process. 
The `Clean as Code` approach uses quality gate to alert/inform when there’s 
something to fix or review in new code (code that has been added or changed), 
allowing to maintain high standards and focus on code quality.

![phpstan](/public/img/readmy/sonar_01.jpg)

The Sonar solution performs checks at every stage of the development process:

- SonarLint provides immediate feedback in IDE as write code so you can find 
and fix issues before a commit.
- SonarQube’s PR analysis fits into CI/CD workflows with SonarQube’s PR analysis 
and use of quality gates.
- Quality gates keep code with issues from being released to production, a key tool 
in helping incorporate the Clean as Code methodology.
- The Clean as Code approach helps focus on submitting new, clean code for production, 
knowing that existing code will be improved over time.


With Clean as Code, your focus is always on `New code` (code that has been added or 
changed according to new code definition) and making sure the code write today is clean 
and safe.
The New code definition can be set at different levels (global, project, and, starting 
in Developer Edition, at the branch level). 
Depending on the level at which your new code definition is set, you can change the 
starting point to fit your situation.


Organizations start off with a default set of rules and metrics called the Sonar way 
`quality profile`. This can be customized per project to satisfy different technical 
requirements. Issues raised in the analysis are compared against the conditions defined 
in the quality profile to establish quality gate.

![phpstan](/public/img/readmy/sonar_02.jpg)


A `quality gate` is an indicator of code quality that can be configured to give a go/no-go 
signal on the current release-worthiness of the code. 
It indicates whether your code is clean and can move forward:
- A passing (green) quality gate means the code meets standard and is ready to be merged.
- A failing (red) quality gate means there are issues to address.

With the Clean as You Code approach, your Quality gate should:
- Focus on `New code metrics` – When quality gate is set to focus on new code metrics 
(like the built-in Sonar way quality gate), new features will be delivered cleanly. 
As long as your quality gate is green, releases will continue to improve.
- Set and enforce `high standards` – When standards are set and enforced on new code, 
you aren't worried about having to meet those standards in old code and having to 
clean up someone else's code. You can take pride in meeting high standards in your code. 
If a project doesn't meet these high standards, it won't pass the quality gate, 
and is therefore not ready to be released.

![phpstan](/public/img/readmy/sonar_03.jpg)


You can use `pull request analysis` and `pull request decoration` to make sure that code meets 
standards before merging. Pull request analysis lets you see your pull request's quality 
gate in the SonarQube UI. You can then decorate your pull requests with SonarQube issues 
directly in your DevOps platform's interface.


SonarQube provides `feedback` through its UI, email, and in decorations on pull or merge 
requests to notify team that there are issues to address. 
Feedback can also be obtained in SonarLint supported IDEs when running in connected mode. 

SonarQube also provides in-depth guidance on the issues telling why each issue is a problem 
and how to fix it, adding a valuable layer of education for developers of all experience 
levels. Developers can then address issues effectively, so code is only promoted when the 
code is clean and passes the quality gate.


#### SonarQube running

```bash
# Run start SonarQube (and don't close):
composer sonar-start
```

![phpstan](/public/img/readmy/sonar_04.jpg)


```bash
# Run scanner SonarQube:
composer sonar-scanner
```

![phpstan](/public/img/readmy/sonar_05.jpg)



---
---

## LICENSE

This code is provided under the [BSD-like license](https://en.wikipedia.org/wiki/BSD_licenses).

You are free to use, modify and distribute the content for non-commercial purposes.
Just mention the original author and provide a link to this repo.



---
---



## Running Psalm Static Analysis

To run the supplied skeleton static analysis, you need to do one of the following:
It is recommended to install the test components from laminas (laminas/laminas-test), 
as this is used in the tests supplied.

```bash
$ composer require --dev vimeo/psalm psalm/plugin-phpunit laminas/laminas-test
```

Once psalm support is present, you can run the static analysis using:

```bash
$ composer static-analysis
```



## Using docker-compose

This skeleton provides a `docker-compose.yml` for use with
[docker-compose](https://docs.docker.com/compose/); it
uses the provided `Dockerfile` to build a docker image 
for the `laminas` container created with `docker-compose`.

Build and start the image and container using:

```bash
$ docker-compose up -d --build
```

At this point, you can visit http://localhost:8080 to see the site running.

You can also run commands such as `composer` in the container.  The container 
environment is named "laminas" so you will pass that value to 
`docker-compose run`:

```bash
$ docker-compose run laminas composer install
```

Some composer packages optionally use additional PHP extensions.  
The Dockerfile contains several commented-out commands 
which enable some of the more popular php extensions. 
For example, to install `pdo-pgsql` support for `laminas/laminas-db`
uncomment the lines:

```sh
# RUN apt-get install --yes libpq-dev \
#     && docker-php-ext-install pdo_pgsql
```

then re-run the `docker-compose up -d --build` line as above.

> You may also want to combine the various `apt-get` and `docker-php-ext-*`
> statements later to reduce the number of layers created by your image.
