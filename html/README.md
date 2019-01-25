# Game of Three

This is my coding challenge task named as Game of Three

## Installation
clone this repository

make sure your web server is configured to ```/public``` directory

fill DB_* params in the .env file, f.e.
```
DB_CONNECTION=mysql
DB_HOST=1.2.3.4
DB_PORT=3306
DB_DATABASE=dbname
DB_USERNAME=dbuser
DB_PASSWORD=dbpass
```
execute 
```
composer install
```
go to application root and execute (make sure your db user has `REFERENCES` privileges)
```
php artisan migrate 
```

application is ready
