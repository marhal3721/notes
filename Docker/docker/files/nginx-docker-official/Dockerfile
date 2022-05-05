FROM nginx:1.21.6
MAINTAINER marhal "marhal@163.com"

ADD ./conf/nginx.conf   /etc/nginx/nginx.conf
ADD ./conf/conf.d/*     /etc/nginx/conf.d

ADD sources.list /etc/apt/
WORKDIR /var/www/html
RUN set -ex \
    && ln -sf /usr/share/zoneinfo/Asia/Shanghai /etc/localtime \
    && echo "Asia/Shanghai" > /etc/timezone \
    && touch /var/www/html/index.html \
    && echo "hello world" > /var/www/html/index.html

