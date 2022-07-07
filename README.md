# Radius Manager

Mitul Gadhiya

(c) JAYNATH INFOTECH

# Overview

The  purpose  of  this  project  is  to  provide  an  Administration  and  End  User  GUI  interface  for  FreeRadius  entries  into  the  MySQL  Database.

## Installation for UBUNTU 18.04.XX will be found [here](README_UBUNTU18.md).
## Installation for UBUNTU 20.04.XX
# Login as ROOT

sudo su

# Install GIT, CURL, WGET and ZIP

apt-get install -y git curl wget zip

# PHP 7 (Needed for Laravel 5.8)

apt-get install -y software-properties-common

add-apt-repository -y ppa:ondrej/php

apt-get update

apt-get install -y php7.2 php7.2-fpm php-mysql php7.2-mysql php7.2-mbstring php-doctrine-dbal php7.2-xml php7.2-zip php7.2-curl

sudo -- sh -c "echo 'cgi.fix_pathinfo=0' >> /etc/php/7.2/fpm/php.ini"

sudo -- sh -c "echo 'cgi.fix_pathinfo=0' >> /etc/php/7.2/cli/php.ini"

sudo service php7.2-fpm restart

# Remove PHP 8 just in case auto installed.

apt-get remove php8.*

# Install Composer for Laravel

curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

# Install MySQL Server

apt-get install mysql-server

/etc/init.d/mysql start
mysql_secure_installation

# Install NGINX Server

apt-get install nginx

# Install FreeRadius 3.0

apt-get install -y freeradius

service freeradius start

apt-get install -y freeradius-mysql

service freeradius stop

ln -s /etc/freeradius/3.0/mods-available/sql /etc/freeradius/3.0/mods-enabled/sql

ln -s /etc/freeradius/3.0/sites-available/dynamic-clients /etc/freeradius/3.0/sites-enabled/dynamic-clients

sh /etc/freeradius/3.0/certs/bootstrap

chown -R freerad:freerad /etc/freeradius/3.0/certs

# Create MySQL Database and User for Application

mysql -uroot -p

<< ENTER YOUR MYSQL ROOT PASSWORD WHEN PROMPT >>

CREATE DATABASE radius;
CREATE USER 'radius'@'localhost' IDENTIFIED WITH mysql_native_password BY 'radpass';
GRANT ALL ON radius.* TO radius@localhost;
flush privileges;
exit

# Clone Radius Manager from Github 

cd /var/www/html

git clone -b "master" https://github.com/PrakashGujarati/RadiusManager.git 

chown www-data:www-data -R RadiusManager

cd RadiusManager

composer install --optimize-autoloader --no-dev

cp .env.example .env

php artisan key:generate

php artisan migrate

php artisan db:seed

php artisan radius:install

service freeradius start

# Set NGINX to point to Application

php artisan nginx:install

# Restart NGINX to Apply Config

service nginx restart

# Radius Clean up script

php artisan radius:cleanup

Setup Laravel cron to run every min this will run Radius Cleanup daily midnight and this will clean the logs older then 90 days. 

`crontab -e -u www-data`

If this prompt for Text Editor select/type 1. nano

Copy following line into the editor at the end of the file. 

`* * * * * php /var/www/html/RadiusManager/artisan schedule:run`

