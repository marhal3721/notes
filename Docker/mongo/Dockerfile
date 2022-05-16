FROM mongo:4.4

RUN usermod -u 1010 mongodb && groupmod -g 1010 mongodb \
&& ln -sf /usr/share/zoneinfo/Asia/Shanghai /etc/localtime \
&& echo "Asia/Shanghai" > /etc/timezone