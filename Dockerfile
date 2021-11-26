FROM registry.cn-hangzhou.aliyuncs.com/marhal/nginx:latest

COPY . /var/www/html/

RUN ls -l /var/www/html/