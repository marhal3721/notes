FROM registry.cn-hangzhou.aliyuncs.com/marhal/nginx:0.0.1
#WORKDIR /var/www/html
#COPY composer.json /var/www/html/
#RUN composer install --no-dev && composer dump-autoload --optimize
COPY . /var/www/html/