#!/bin/bash

sudo service elasticsearch start;
echo "es  start";
php artisan es:company;
php artisan zcache:utils;
php artisan queue:work;
