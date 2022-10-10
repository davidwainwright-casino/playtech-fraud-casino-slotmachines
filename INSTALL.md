

## wip
while works, still lot of refactor todo, mainly upload for the boyz 

make laravel base:
```bash
git clone https://github.com/laravel/laravel.git casino-dog-base
```

install, make sure to configure and test if working on your base laravel install:
*cache driver 
*queue_driver (redis)
*database (pgsql, mariadb, mysqldb)

in essence it's following steps of (https://laravel.com/docs/9.x/installation)[https://laravel.com/docs/9.x/installation]

after base, you then put these files in .wainwright folder within laravel base/root dir

```bash
cd casino-dog-base
git clone https://github.com/wrenchwright/casino-dog-main.git .wainwright
```

now add to your composer.json manually:
```json
    "repositories": [
        {
            "type": "path",
            "url": ".wainwright/casino-dog"
        },
        {
            "type": "path",
            "url": ".wainwright/nova"
        }
    ],
```

then add to require in composer.json:
```json
        "wainwright/casino-dog": "*",
        "wrenchwright/wainwright_nova_support": "*",
```

ok, almost there - now run commands in following order:

```
composer update --no-cache --no-suggest --no-ansi
php artisan package:discover
php artisan optimize:clear
php artisan nova:install
php artisan migrate
php artisan casino-dog:install
php artisan migrate:fresh
```

you can now make admin user:
```bash 

php artisan nova:admin
```

You can add API key in admin panel, or manually:
``` bash
php artisan casino-dog:add-operator-key
```

You can enter app at https://{APP_URL}/allseeingdavid

