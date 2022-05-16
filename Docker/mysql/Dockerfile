FROM mysql:5.7
COPY ./mysql.cnf /etc/mysql/conf.d/
COPY ./mysqld.cnf /etc/mysql/mysql.conf.d/

RUN usermod -u 1020 mysql && groupmod -g 1020 mysql \
&& ln -sf /usr/share/zoneinfo/Asia/Shanghai /etc/localtime \
&& echo "Asia/Shanghai" > /etc/timezone