FROM registry.cn-hangzhou.aliyuncs.com/marhal/nginx:0.1.4

COPY . /var/www/html/

RUN ls -l /var/www/html/