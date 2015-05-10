#!/bin/sh
if [ "$WHATSAPP_NUMBER" == false ]; then
  vendor/bin/heroku-php-apache2 public
else
  php artisan whatsapp:start
fi