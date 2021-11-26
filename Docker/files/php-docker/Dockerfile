FROM ubuntu:20.04
ENV PHP_VERSION php-7.4.26
ADD sources.list /etc/apt/
WORKDIR /usr/local/src/$PHP_VERSION
ARG DEBIAN_FRONTEND=noninteractive
RUN useradd www -u 1200 -M -s /sbin/nologin
RUN apt-get update
RUN apt-get install -y wget
RUN apt-get install -y make
RUN apt-get install -y gcc
RUN apt-get install -y libc6-dev
RUN apt-get install -y libpcre3 libpcre3-dev
RUN apt-get install -y build-essential
RUN apt-get install -y autoconf
RUN apt-get install -y g++
RUN apt-get install -y openssl
RUN apt-get install -y libssl-dev
RUN apt-get install -y libxml2 libxml2-dev libxslt-dev
RUN apt-get install -y libgeoip-dev
RUN apt-get install -y libgd-dev
RUN apt-get install -y libsqlite3-dev # No package 'sqlite3' found
RUN apt-get install -y libcurl4-openssl-dev # No package 'libcurl' found
RUN apt-get install -y libonig-dev # No package 'oniguruma' found
RUN apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN mkdir -p /usr/local/php-7.4.26/etc && mkdir -p /usr/local/php-7.4.26/conf.d
#RUN wget -O php-7.4.26.tar.gz https://www.php.net/distributions/php-7.4.26.tar.gz \
#    && tar -zxvf php-7.4.26.tar.gz -C . \
ADD php-7.4.26.tar.gz .
RUN cd php-7.4.26 \
    && ./configure \
    --prefix=/usr/local/php7.4.26 \
    --with-config-file-path=/usr/local/php-7.4.26/etc \
    --with-config-file-scan-dir=/usr/local/php-7.4.26/conf.d \
    --with-fpm-user=www \
    --with-fpm-group=www \
    --with-mysql=mysqlnd \
    --with-mysqli=mysqlnd \
    --with-pdo-mysql=mysqlnd \
    --with-iconv-dir \
    --with-zlib \
    --with-libxml-dir=/usr \
    --with-pear  \
    --with-gettext \
    --with-curlwrappers \
    --with-curl \
    --with-zlib-dir  \
    --with-libXML-dir \
    --with-xsl \
    --enable-mbregex \
    --enable-shmop \
    --enable-sysvsem \
    --enable-intl \
    --enable-gd \
    --enable-safe-mode \
    --enable-xml \
    --enable-zip \
    --enable-bcmath \
    --enable-inline-optimization \
    --enable-mbstring \
    --enable-sockets \
    --enable-soap \
    --enable-exif \
    --enable-magic-quotes \
    --enable-fastCGI \
    --enable-fpm \
    --enable-force-CGI-redirect \
    --enable-pcntl \
    --enable-session \
    --enable-opcache \
    --enable-fast-install  \
    --enable-fileinfo \
    --disable-rpath \
    --disable-debug \
    --with-mcrypt \
    --with-mhash \
    --with-openssl  \
    --with-pcre-dir=/usr/local/bin/pcre-config \
    --with-iconv \
    && make \
    && make install

RUN mkdir -p /var/www/html
RUN rm -rf /usr/local/src/$PHP_VERSION/php-7.4.26 && rm /usr/local/src/$PHP_VERSION/php-7.4.26.tar.gz
VOLUME ["/var/www/html"]
WORKDIR /var/www/html

CMD /usr/local/php-7.4.26/sbin/php-fpm -F
EXPOSE 9000
