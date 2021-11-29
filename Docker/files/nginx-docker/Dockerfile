FROM debian:buster-slim

ENV NG_VERSION nginx-1.21.4
WORKDIR /usr/local/src/

#更换阿里云源
ADD sources.list /etc/apt/

#切换工作目录
WORKDIR /usr/local/src/$NG_VERSION

#避免交互操作
ARG DEBIAN_FRONTEND=noninteractive

#./configure: error: the HTTP rewrite module requires the PCRE library
# libpcre3 libpcre3-dev

# ./configure: error: C compiler cc is not found
# gcc

# ./configure: error: SSL modules require the OpenSSL library.
# openssl libssl-dev

#  ./configure: error: the HTTP gzip module requires the zlib library.
# zlib1g-dev

# ./configure: error: the HTTP XSLT module requires the libxml2/libxslt
# libxml2 libxml2-dev libxslt-dev

#  ./configure: error: the GeoIP module requires the GeoIP library
# libgeoip-dev

# ./configure: error: the HTTP image filter module requires the GD library.
# libgd-dev

# /bin/sh: 1: make: not found
# make

RUN addgroup --system --gid 101 nginx \
    && mkdir /nonexistent \
    && adduser --system --disabled-login --ingroup nginx --no-create-home --home /nonexistent --gecos "nginx user" --shell /bin/false --uid 101 nginx \
    && apt-get update \
    && apt-get install -y wget \
    && apt-get install -y gcc \
    && apt-get install -y libpcre3 libpcre3-dev \
    && apt-get install -y openssl libssl-dev \
    && apt-get install -y zlib1g-dev \
    && apt-get install -y libxml2 libxml2-dev libxslt-dev \
    && apt-get install -y libgd-dev \
    && apt-get install -y libgeoip-dev \
    && apt-get install -y make \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && mkdir -p /var/cache/nginx \
    && mkdir -p /var/log/nginx \
    && mkdir -p /var/www/html \
    && wget https://nginx.org/download/nginx-1.21.4.tar.gz \
    && tar -zxvf nginx-1.21.4.tar.gz \
    && cd nginx-1.21.4 \
    && ./configure --user=nginx --group=nginx --prefix=/usr/local/nginx \
    --error-log-path=/var/log/nginx/error.log \
    --http-log-path=/var/log/nginx/access.log \
    --conf-path=/etc/nginx/nginx.conf \
    --pid-path=/var/run/nginx.pid \
    --lock-path=/var/run/nginx.lock \
    --http-client-body-temp-path=/var/cache/nginx/client_temp \
    --http-proxy-temp-path=/var/cache/nginx/proxy_temp \
    --http-fastcgi-temp-path=/var/cache/nginx/fastcgi_temp \
    --http-uwsgi-temp-path=/var/cache/nginx/uwsgi_temp \
    --http-scgi-temp-path=/var/cache/nginx/scgi_temp \
    --with-file-aio \
    --with-http_ssl_module \
    --with-http_realip_module \
    --with-http_addition_module \
    --with-http_xslt_module \
    --with-http_image_filter_module \
    --with-http_geoip_module \
    --with-http_sub_module \
    --with-http_dav_module \
    --with-http_flv_module \
    --with-http_mp4_module \
    --with-http_gunzip_module  \
    --with-http_gzip_static_module \
    --with-http_auth_request_module \
    --with-http_random_index_module \
    --with-http_secure_link_module \
    --with-http_degradation_module \
    --with-http_stub_status_module \
    && make \
    && make install \
    && apt-get -y remove wget make gcc \
    && rm -rf /usr/local/src/$NG_VERSION \
    && cp /etc/nginx/nginx.conf /etc/nginx/nginx.default.bak

# 自定义配置文件
COPY nginx_conf/nginx.conf /etc/nginx/
COPY nginx_conf/conf.d /etc/nginx/conf.d

# 添加根目录测试访问文件
ADD index.html /var/www/html

#设置容器中要挂在到宿主机的目录
VOLUME ["/var/www/html"]

WORKDIR /var/www/html
#设置sbin环境变量
ENV PATH /usr/local/nginx/sbin:$PATH
#暴露80端口
EXPOSE 80
ENTRYPOINT ["nginx"]
CMD ["-g", "daemon off;"]