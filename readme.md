## Installing


    $ composer install
    $ php artisan migrate


## Config

Update .env and change these settings

    ARCHIVE_GROUP=1415192401896193
    ARCHIVE_TOKEN="EAAAA...."


## Run

Service mode: this will loop forever and keep sync with facebook feed every (20-60) seconds

    $ php artisan facebook:index service

All mode: This will fetch all posts from facebook

    $ php artisan facebook:index all

New mode: This will fetch only new posts (compare with current db)

    $ php artisan facebook:index new




