FROM debian:buster-slim
ENV PHP_VERSION php-7.4.26
ADD sources.list /etc/apt/
COPY php-7.4.26.tar.gz /usr/local/src/
COPY cacert.pem /usr/lib/ssl/
ARG DEBIAN_FRONTEND=noninteractive

RUN useradd www -u 1200 -M -s /sbin/nologin  \
    && apt-get update  \
    && apt-get install -y wget make gcc libpcre3 libpcre3-dev g++ openssl libssl-dev libxslt-dev libgd-dev libcurl4-openssl-dev libonig-dev libsystemd-dev libzip-dev \
    && mkdir -p /usr/local/$PHP_VERSION/etc \
    && mkdir -p /usr/local/$PHP_VERSION/etc/conf.d \
    && cd /usr/local/src/ && tar -zxvf php-7.4.26.tar.gz && cd /usr/local/src/$PHP_VERSION/ \
    && ./configure \
    --bindir=/usr/bin \
    --sbindir=/usr/local/sbin \
    --prefix=/usr/local/$PHP_VERSION \
    --with-config-file-path=/usr/local/$PHP_VERSION/etc \
    --with-config-file-scan-dir=/usr/local/$PHP_VERSION/etc/conf.d \
    --enable-fpm \
    --with-fpm-user=www \
    --with-fpm-group=www \
    --without-pdo-sqlite \
    --without-sqlite3  \
    --enable-mysqlnd  \
    --enable-re2c-cgoto  \
    --with-mysqli=mysqlnd  \
    --with-pdo-mysql=mysqlnd \
    --with-iconv-dir \
    --with-zlib \
    --with-zip \
    --without-pear \
    --enable-phar \
    --with-gettext \
    --with-curl \
    --with-zlib-dir  \
    --with-xsl \
    --enable-mbregex \
    --enable-shmop \
    --enable-sysvsem \
    --enable-intl \
    --enable-gd \
    --enable-xml \
    --enable-bcmath \
    --enable-inline-optimization \
    --enable-mbstring \
    --enable-sockets \
    --enable-soap \
    --enable-exif \
    --enable-pcntl \
    --enable-session \
    --enable-opcache \
    --enable-fast-install  \
    --enable-fileinfo \
    --disable-rpath \
    --disable-debug \
    --with-mhash \
    --with-openssl  \
    --with-iconv \
    --with-xmlrpc \
    && make -j6 \
    && make install \
    && cp php.ini* /usr/local/$PHP_VERSION/etc/  \
    && cp php.ini-development /usr/local/$PHP_VERSION/etc/php.ini \
    && sed -i "s/;openssl.cafile=/openssl.cafile=\/usr\/lib\/ssl\/cacert.pem/g" /usr/local/php-7.4.26/etc/php.ini \
    && cp /usr/local/$PHP_VERSION/etc/php-fpm.conf.default /usr/local/$PHP_VERSION/etc/php-fpm.conf \
    && cp /usr/local/$PHP_VERSION/etc/php-fpm.d/www.conf.default /usr/local/$PHP_VERSION/etc/php-fpm.d/www.conf \
    && php -r "copy('https://install.phpcomposer.com/installer', 'composer-setup.php');" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer  \
    && chmod +x /usr/local/bin/composer \
    && composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/ \
    && composer self-update \
    && apt-get -y remove wget make \
    && apt-get -y autoremove \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && rm -rf /usr/local/src/$PHP_VERSION

ENV PATH /usr/local/$PHP_VERSION/sbin/:$PATH
ENTRYPOINT ["php-fpm","-F"]

VOLUME ["/var/www/html"]
WORKDIR /var/www/html

STOPSIGNAL SIGQUIT
EXPOSE 9000
