FROM registry.cn-hangzhou.aliyuncs.com/marhal/nginx-official:default-v1.0.0

COPY . /var/www/html/

RUN ls -l /var/www/html/