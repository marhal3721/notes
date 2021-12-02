* [Docker Command](#Docker)
* [Docker run 参数解析](#docker-run)
* [Mac下docker访问主机服务](#MacDocker)
* [docker 构建镜像并推送](#Dockerfile-push)

## <a id="Docker">Docker Command</a>

```bash
# 显示指定的列
docker ps -a --format "table {{.ID}}\t{{.Names}}\t{{.Ports}}\t{{.Status}}"
# 日志
docker logs -f ny-backend-phpfpm
# 进入容器
docker exec -it ny-spider-phpfpm bash
# 停止并删除容器、清除网络
docker stop $(docker ps -a -q) && docker rm $(docker ps -a -q) && docker network prune
# 创建网络
docker network create database && docker network create application
# docker-compose启动
docker-compose up -d
# 删除已经退出的容器
docker rm $(docker ps -qf status=exited)
# 强制删除所有容器
docker rm -f $(docker ps -a -q)
# 本地文件、文件夹（-r）复制到容器
docker cp -r /var/www/html 96f7f14e99ab:/www/
# 容器文件、文件夹复制到本地
docker cp -r 96f7f14e99ab:/www /tmp/
# 强制删除
docker rm -f $(docker ps -a -q)
# 输入多个命令
docker exec ny-backend-phpfpm bash -c "composer clear && composer update"
# 容器输入中文
kubectl exec -it hzlh-mysql-0 -n=hzlh-pre env LANG=C.UTF-8 /bin/bash
# 通过docker-conposer 拉取镜像
docker pull
# 重新构建镜像
docker-compose up -d --force-recreate
# 容器mynginx将访问日志指到标准输出，连接到容器查看访问信息
# --sig-proxy=false来确保CTRL-D或CTRL-C不会关闭容器
docker attach --sig-proxy=false mynginx


# 删除未运行的容器（正在运行的删除会）
docker rm $(docker ps -a -q) 

# 删除停止的容器
docker rm `docker ps -a|grep Exited|awk '{print $1}'`
docker rm $(docker ps -qf status=exited)
docker container prune

# 删除所有关闭的容器
docker ps -a | grep Exit | cut -d ' ' -f 1 | xargs docker rm

# 仅仅清除没有被容器使用的镜像文件
docker image prune -af
# 清除多余的数据，包括停止的容器、多余的镜像、未被使用的volume等等
docker system prune -f

# dockerfile 构建,在dockerfile的目录下执行
docker build . -t 2004-nginx
# 启动
docker run -itd --name m-nginx -p 8086:80 2004-nginx
docker run -itd --name mydockerName -p 80:80 myimageName

# 查看镜像的构建过程
docker history [REPOSITORY]:[TAG]
# 查看镜像的完整构建过程
docker history [REPOSITORY]:[TAG] --no-trunc

# 查询僵尸文件
docker volume ls -qf dangling=true

# 删除所有dangling数据卷（即无用的Volume，僵尸文件）
docker volume rm $(docker volume ls -qf dangling=true)

# 删除所有dangling镜像（即无tag的镜像）
docker rmi $(docker images | grep "^<none>" | awk "{print $3}")



```
## <a id="docker-run">Docker run 参数解析</a>
* -a stdin: 指定标准输入输出内容类型，可选 STDIN/STDOUT/STDERR 三项
* -d: 后台运行容器，并返回容器ID
* -i: 以交互模式运行容器，通常与 -t 同时使用
* -P: 随机端口映射，容器内部端口随机映射到主机的端口
* -p: 指定端口映射，格式为：主机(宿主)端口:容器端口
* -t: 为容器重新分配一个伪输入终端，通常与 -i 同时使用
* --name="nginx-lb": 为容器指定一个名称
* --dns 8.8.8.8: 指定容器使用的DNS服务器，默认和宿主一致
* --dns-search example.com: 指定容器DNS搜索域名，默认和宿主一致
* -h "mars": 指定容器的hostname
* -e username="ritchie": 设置环境变量
* --env-file=[]: 从指定文件读入环境变量
* --cpuset="0-2" or --cpuset="0,1,2": 绑定容器到指定CPU运行
* -m :设置容器使用内存最大值
* --net="bridge": 指定容器的网络连接类型，支持 bridge/host/none/container: 四种类型
* --link=[]: 添加链接到另一个容器
* --expose=[]: 开放一个端口或一组端口
* --volume , -v: 绑定一个卷 (`/data:/data`主机的目录 /data 映射到容器的 /data)

## <a id="MacDocker">Mac下docker访问主机服务</a>
```php
# 172.16.0.201 host.docker.internal
# 172.16.0.201 gateway.docker.internal
# 将host设置为docker.for.mac.host.internal(host.docker.internal)即可
# 官方文档：https://docs.docker.com/desktop/mac/networking/
$connect = oci_connect('MARHAL', '123456', 'docker.for.mac.host.internal:49161/XE', 'UTF8');
```

## <a id="Dockerfile-push">Dockerfile 构建镜像并推送</a>
```bash
docker build . -t nginx-1
docker run docker run -itd --name nginx-a -p 8061:80 nginx-1
# 登录远程仓库并输入密码
docker login --username=junwuji555@sina.com registry.cn-hangzhou.aliyuncs.com
# 打标签
docker tag [imageId] [registryName]:[version] 
docker tag deaac4270050 registry.cn-hangzhou.aliyuncs.com/marhal/nginx:latest 
# 推送
docker push [registryName]:[version] 
docker push registry.cn-hangzhou.aliyuncs.com/marhal/nginx:latest
```

