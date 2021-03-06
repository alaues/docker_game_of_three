# Game of Three

This is containerized PHP application based on Laravel 5.7 for task Game of Three

## Requirements for installation
* Docker, with docker-compose tool
* Mysql server, with pre-created database and `REFERENCES` privileges user

## Installation
1. clone this repository
```
git clone https://github.com/alaues/php_task.git
```
2. you shoud have docker-compose tool, go to cloned project and start the container
```
$ cd php_task
$ docker-compose up -d
```
it will start docker container with PHP 7.2 and Apache listening on 2121 port, I hope it's not used :)

check that container is started and listening on 2121 port
```
root@almat:/var/www/php_task# docker ps
CONTAINER ID        IMAGE                  COMMAND                  CREATED             STATUS              PORTS                            NAMES
406fbf475e47        php_task_apache        "docker-php-entrypoi…"   25 seconds ago      Up 6 seconds        2121/tcp, 0.0.0.0:2121->80/tcp   php_task_apache_1
```

3. Fill `DB_*` parameters in  `html/.env` file (if you don't have available mysql server, you can use credentials that I sent you in email)

```
DB_CONNECTION=mysql
DB_HOST=x.x.x.x
DB_PORT=3306
DB_DATABASE=xxxx
DB_USERNAME=xxx
DB_PASSWORD=xxx
```
make sure that 
```
BROADCAST_DRIVER=pusher
```

also fill `PUSHER_APP_*` parameters 
```
PUSHER_APP_KEY=""
PUSHER_APP_CLUSTER="eu"
PUSHER_APP_SECRET=""
PUSHER_APP_ID=""
```
with parameters that I sent you in email

4. Next, install the dependencies with composer in `html` directory
```
$ docker exec -it php_task_apache_1 bash
$ php composer.phar install
```
5. Execute database migrations and exit
```
$ php artisan migrate
exit
```
6. Change owner of `storage` directory to `www-data`

```
 sudo chown www-data.www-data storage -R
 ```

7. Now you can start using application! Navigate your browser to http://yoursite:2121 to see the application

### Tests

Few tests of `App\Game` class are available and can be run with next command

```
docker exec -it php_task_apache_1 bash
vendor/bin/phpunit tests
```
