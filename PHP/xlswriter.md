# Docs
[文档|Documents](https://xlswriter-docs.viest.me/zh-cn)

[GitHub](https://github.com/viest/php-ext-xlswriter)
# Install
```bash
marhal@marhal:~$ sudo apt-get install -y zlib1g-dev php-dev
marhal@marhal:~$ sudo pecl install xlswriter
```
# Config
```bash
marhal@marhal:~$ php --ini
# Configuration File (php.ini) Path: /etc/php/7.4/cli
# Loaded Configuration File:         /etc/php/7.4/cli/php.ini
# Scan for additional .ini files in: /etc/php/7.4/cli/conf.d
# Additional .ini files parsed:      /etc/php/7.4/cli/conf.d/10-mysqlnd.ini,
# /etc/php/7.4/cli/conf.d/10-opcache.ini,
# /etc/php/7.4/cli/conf.d/10-pdo.ini,
# /etc/php/7.4/cli/conf.d/15-xml.ini,
# /etc/php/7.4/cli/conf.d/20-bcmath.ini,
# /etc/php/7.4/cli/conf.d/20-calendar.ini,
# /etc/php/7.4/cli/conf.d/20-ctype.ini,
# /etc/php/7.4/cli/conf.d/20-curl.ini,
# /etc/php/7.4/cli/conf.d/20-dom.ini,
# /etc/php/7.4/cli/conf.d/20-exif.ini,
# /etc/php/7.4/cli/conf.d/20-ffi.ini,
# /etc/php/7.4/cli/conf.d/20-fileinfo.ini,
# /etc/php/7.4/cli/conf.d/20-ftp.ini,
# /etc/php/7.4/cli/conf.d/20-gd.ini,
# /etc/php/7.4/cli/conf.d/20-gettext.ini,
# /etc/php/7.4/cli/conf.d/20-iconv.ini,
# /etc/php/7.4/cli/conf.d/20-igbinary.ini,
# /etc/php/7.4/cli/conf.d/20-json.ini,
# /etc/php/7.4/cli/conf.d/20-mbstring.ini,
# /etc/php/7.4/cli/conf.d/20-msgpack.ini,
# /etc/php/7.4/cli/conf.d/20-mysqli.ini,
# /etc/php/7.4/cli/conf.d/20-pdo_mysql.ini,
# /etc/php/7.4/cli/conf.d/20-phar.ini,
# /etc/php/7.4/cli/conf.d/20-posix.ini,
# /etc/php/7.4/cli/conf.d/20-readline.ini,
# /etc/php/7.4/cli/conf.d/20-redis.ini,
# /etc/php/7.4/cli/conf.d/20-shmop.ini,
# /etc/php/7.4/cli/conf.d/20-simplexml.ini,
# /etc/php/7.4/cli/conf.d/20-soap.ini,
# /etc/php/7.4/cli/conf.d/20-sockets.ini,
# /etc/php/7.4/cli/conf.d/20-sysvmsg.ini,
# /etc/php/7.4/cli/conf.d/20-sysvsem.ini,
# /etc/php/7.4/cli/conf.d/20-sysvshm.ini,
# /etc/php/7.4/cli/conf.d/20-tokenizer.ini,
# /etc/php/7.4/cli/conf.d/20-xhprof.ini,
# /etc/php/7.4/cli/conf.d/20-xlswriter.ini,
# /etc/php/7.4/cli/conf.d/20-xmlreader.ini,
# /etc/php/7.4/cli/conf.d/20-xmlwriter.ini,
# /etc/php/7.4/cli/conf.d/20-xsl.ini,
# /etc/php/7.4/cli/conf.d/20-zip.ini,
# /etc/php/7.4/cli/conf.d/25-memcached.ini

marhal@marhal:~$ sudo vim /etc/php/7.4/cli/conf.d/20-xlswriter.ini
# extension=xlswriter.so

marhal@marhal:~$ php -m
# [PHP Modules]
# bcmath
# ...
# xml
# xmlreader
# xmlwriter
# xsl
# Zend OPcache
# zip
# zlib
# 
# [Zend Modules]
# Zend OPcache

marhal@marhal:~$ php-fpm7.4 -m
# [PHP Modules]
# bcmath
# ...
# xml
# xmlreader
# xmlwriter
# xsl
# Zend OPcache
# zip
# zlib
# 
# [Zend Modules]
# Zend OPcache
```
# Use

## import

## export