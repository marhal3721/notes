* [Docker](#Docker)

## <a id="Docker">Docker Command</a>

```bash
# 显示指定的列
docker ps -a --format "table {{.ID}}\t{{.Names}}\t{{.Ports}}\t{{.Status}}"
# 日志
docker -if ny-backend-phpfpm
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
# 本地文件、文件夹复制到容器
docker cp /var/www/html 96f7f14e99ab:/www/
# 容器文件、文件夹复制到本地
docker cp  96f7f14e99ab:/www /tmp/
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
```