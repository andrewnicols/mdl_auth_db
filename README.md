mdl_auth_db
===========

Installing
----------

Create a new database (preferably postgres)

```
  git clone git://github.com/andrewnicols/mdl_auth_db.git
  composer install
```

Creating data
-------------
```
  ./bin/console appbundle:createtestdata [usercount] [coursecount] [maxuserspercourse]
```

Setting up in Moodle
--------------------

Copy configuration generated from the following command to your config.php:
```
  ./bin/console appbundle:moodleconfig
```

Running Moodle User Syncs
-------------------------

```
  php auth/db/cli/sync.php --verbose
  php enrol/database/cli/sync.php --verbose
```
