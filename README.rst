Database migrations for php-mysql inspired by South (Django)
============================================================

Light weight Database Migrations for your PHP-Mysql application.

This tool was originally developed for and in the code base of
`Kodeplay's`_ product `Kodemall`_.


Original Contributors
---------------------

* `Vineet Naik`_ 
* `Jimit Modi`_


Motivation
----------

Managing database schema changes in a project, particularly made by
multiple developers is always a hassle specially when working on code
that does not have any support for database migrations eg. wordpress,
opencart and many other useful opensource projects written in php. Crude
methods such as maintaining a `db_changes.sql` file in version control
are not quite convenient.

This tool is light weight and uses minimalist approach for solving
this problem. You can use it with any php application even if it
doesn't have any kind of ORM or Model Classes. You just need to write
and maintain sql queries to be applied `forwards` and `backwards` in
your VCS. It provides a command line interface for creating new
migration files and applying them with support for a few options that
cover commonly observed use cases.

It's working is pretty much inspired by `South`_, one of the killer `Django`_
apps.


Installation and Setup
----------------------

I am thinking of writing this tool in such a way that it can be used
both by executing it in a system wide manner as well as by dropping it
in individual PHP applications. This part is still a WIP!

For now just drop this library somewhere inside your application.

Create a directory in which the migration files will be created ::

    $ mkdir myproject/phpDbMigrations/migrations

Open the ``phpDbMigrations/bootstrap.php`` and change the
``MIGRATIONS_DIR`` constant definition to this path.

Next, specify the database details in the bootstrap file ::

    <?php

    // ...

    define('PDBM_HOST', 'localhost');
    define('PDBM_USER', 'myuser');
    define('PDBM_PASS', 'mypassword');
    define('PDBM_NAME', 'mydb');
    define('PDBM_PRE', '');

    // ...

If your application has it's own database config file, then you can
reuse it instead of defining the constants again as follows. ::

    <?php

    // ...

    require('/path/to/your/project/config/database.php');

    define('PDBM_HOST', DB_HOSTNAME);
    define('PDBM_USER', DB_USERNAME);
    define('PDBM_PASS', DB_PASSWORD);
    define('PDBM_NAME', DB_DATABASE);
    define('PDBM_PRE', DB_PREFIX);

    // ...

If your app stores database config in some other way, do the
needful. Get the idea right?


Usage
-----

Getting Help
~~~~~~~~~~~~

Run the ``manage.php`` file from the command line as follows, ::

    $ php manage.php

and it will show help


Creating migrations
~~~~~~~~~~~~~~~~~~~

As an example, suppose you have a table called `books` to which you wish
to add a column `isbn`. Instead of going to phpmyadmin or your
favourite mysql client to do this, create a migration file as follows, ::

    $ php manage.php create -n add__column__isbn__books


This command will create a php file inside the ``MIGRATIONS_DIR`` you
have defined in the bootstrap file. This file already has
boilerplate code added for you. Just fill in the ``forwards`` and
``backwards`` functions. ::

    <?php
    
    // ...

    function forwards() {
        // Replace the query below with your db change query
        PDOWrapper::exec_query('ALTER TABLE `books` ADD `isbn` VARCHAR( 16 ) NOT NULL');
    }
 
    function backwards() {
        PDOWrapper::exec_query('ALTER TABLE `books` DROP `isbn`');
    }

Feel free to write code for any other things such as adding default
isbn for existing rows etc. Now run the ``migrate`` command to apply
this schema change. ::

    $ php manage.php migrate -n add__column__isbn__books

If you are not sure which all migrations are yet to be applied in your
database, run the migrate command without specifying any thing, ::

    $ php manage.php migrate

All `forwards` migrations that are already applied to your database
are skipped and only the new ones are applied. Note that a lot of
times, this is the recommended way to run the migrations as specifying
the migration sometimes results in migrations getting reversed/undone
(backwards migration). Read ahead to learn more about this.


Running backwards migrations
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

What if you want to undo a migration. Lets say following migrations
are already applied to our database ::

    add__table__books
    add__column__books__isbn
    add__column__books__num_reviews

Suppose we want to undo the last migration ie ``add__column__books__num_reviews``,
run the following command ::

    $ php manage.php migrate -n add__column__books__isbn

Here we are specifying a migration but since it is already applied and
there are more migrations applied after it, the ones which are applied
later will be undone ie their ``backwards`` functions will be invoked.

Think of this as analogous to the ``get checkout <commithash>``
command.


Faking migrations
~~~~~~~~~~~~~~~~~

If you forget to create a migration file and manually run a schema
query on the database instead, you will need to fake migrations. In
order to do this, create a migration file normally and add your
queries to it. Then run the migrate command with the --fake flag as
follows, ::

    $ php manage.php migrate --fake

This will make sure that the query is not executed again but schema
change is recorded. 


Recovering from failures
~~~~~~~~~~~~~~~~~~~~~~~~

From personal experience, I have observed that many it happens that
you need to clear all data in your db for which you run truncate
queries resulting in emptying of the table in that keeps a record of
all applied migrations (``db_migrationhistory`` if you stick
with the default config) In such case, run the migrate command with
the --recover flag ::

    $ php manage.php migrate --recover


It will fail silently on the sql errors and move on with the next
migrations.


Please Note! If you use --fake and --recover together, only --fake will
take effect.


Todo
----

1. Add tests

2. To be able to run the commands globally without having to include
   the source code in every php application



Contributions and Feedback are greatly appreciated.


.. _`Kodeplay's`: http://kodeplay.com
.. _`Kodemall`: http://kodemall.com
.. _`Jimit Modi`: https://github.com/jimymodi
.. _`Vineet Naik`: https://github.com/naiquevin
.. _`South`: http://south.aeracode.org/
.. _`Django`: https://www.djangoproject.com/

