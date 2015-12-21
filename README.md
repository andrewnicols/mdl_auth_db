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

```
  ./bin/console appbundle:moodleconfig
```
