#!/usr/bin/env bash
if [ "$APP_ENV" != 'prod' ]; then
  composer install --prefer-dist --no-progress --no-interaction
fi

sleep 1

bin/console doctrine:migrations:migrate --no-interaction
bin/console cache:warmup --no-interaction

#setfacl -R -m u:www-data:rwX -m u:"$(whoami)":rwX var
#setfacl -dR -m u:www-data:rwX -m u:"$(whoami)":rwX var

# Set xdebug IP
REMOTE_HOST=`/sbin/ip route|awk '/default/ { print $3 }'`
echo "Setting XDebug host to $REMOTE_HOST"
sed -i "s/XDEBUG_REMOTE_HOST/$REMOTE_HOST/" "$PHP_INI_DIR/conf.d/app.ini"
sed -i "s/XDEBUG_MODE/$XDEBUG_MODE/" "$PHP_INI_DIR/conf.d/app.ini"

# start cron
crond -l 2 -b
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf