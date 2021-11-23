FROM ubuntu:20.04
# AS builder

ENV NG_VERSION nginx-1.21.4
WORKDIR /usr/local/src/

#更换阿里云源
ADD sources.list /etc/apt/

#创建nginx用户
RUN useradd -M -s /sbin/nologin nginx 

#切换工作目录
WORKDIR /usr/local/src/$NG_VERSION

#避免交互操作
ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get update

RUN apt-get install -y wget \
&& apt-get install -y make \
&& apt-get install -y gcc \
&& apt-get install -y libc6-dev \
&& apt-get install -y libpcre3 libpcre3-dev \
&& apt-get install -y build-essential \
&& apt-get install -y autoconf \
&& apt-get install -y g++ \
&& apt-get install -y openssl \
&& apt-get install -y libssl-dev \
&& apt-get install -y libxml2 libxml2-dev libxslt-dev \
&& apt-get clean \
&& rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN apt-get update
RUN apt-get install -y libgeoip-dev \
  && apt-get install -y libgd-dev \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN wget http://www.zlib.net/zlib-1.2.11.tar.gz
RUN tar -zxvf zlib-1.2.11.tar.gz
RUN cd zlib-1.2.11 && ./configure && make && make install

RUN mkdir -p /var/cache/nginx
RUN mkdir -p /var/log/nginx
RUN mkdir -p /var/www/html

WORKDIR /usr/local/src/$NG_VERSION
RUN wget https://nginx.org/download/nginx-1.21.4.tar.gz
RUN tar -zxvf nginx-1.21.4.tar.gz
RUN cd nginx-1.21.4 && ./configure --user=nginx --group=nginx --prefix=/usr/local/nginx \
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
    && make install

RUN rm -rf /usr/local/src/$NG_VERSION
# 备份配置文件
RUN cp /etc/nginx/nginx.conf /etc/nginx/nginx.default.bak
# 自定义配置文件
COPY nginx_conf/nginx.conf /etc/nginx/
COPY nginx_conf/conf.d /etc/nginx/conf.d

# 添加根目录测试访问文件
ADD index.html /var/www/html
#设置容器中要挂在到宿主机的目录
#VOLUME /usr/local/nginx/html

WORKDIR //var/www/html
#设置sbin环境变量
ENV PATH /usr/local/nginx/sbin:$PATH
#暴露80端口
EXPOSE 80
ENTRYPOINT ["nginx"]
CMD ["-g", "daemon off;"]