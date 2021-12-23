* [Linux](#Linux)
* [win10下ubuntu子系统与windows的目录影射](#win10-Linux)
* [ubuntu时区设置](#ubuntu-date)
* [Curl](#curl)
* [SSH](#ssh)
* [k8s](#k8s)
* [NPM](#npm)
* [virtualbox 配置ubuntu固定ip](#virtualbox-ubuntu-ip)
* [virtualbox挂载目录](#virtualbox-ubuntu-mount)

## <a id="Linux">Linux Command</a>

```bash
## 查看开机自启项
systemctl list-unit-files --type=service|grep enabled
## 关闭开机自启项
sudo systemctl disable apache2.service
sudo systemctl disable nginx.service
## 查看文件前几行
head -n 10 /test.sql
## 查看文件后几行
tail -n 10 /test.sql
## 将文件的前/后几行输出到指定文件
head/tail -n 10 /test.sql >> /test10.sql
## 从第3000行开始，显示1000行（显示3000~3999行）
cat filename.txt | tail -n +3000 | head -n 1000
## 显示1000行到3000行
cat filename.txt | head -n 3000 | tail -n +1000 
sed -n '1000,3000p' filename.txt


## nohup
### 默认输出日志在nohup.out
nohup [command] & 
### 指定输出日志文件
nohup [command] > [logPath] 2>&1 & 

## 查看nohup输出
tail - 200f nohup.out

## 查看任务
jobs

## 查看进程
ps -aux | grep php

# 查看当前目录文件的大小
du -sh *

# 文件内容替换
## 替换文件中的内容 text->replace 
sed -i 's/[text]/[replaceText]/' [file]
sed -i "s/;openssl.cafile=/openssl.cafile=\/usr\/lib\/ssl\/cacert.pem/g" /usr/local/php/etc/php.ini

## 移除文件中的空白行
sed '/^$/d' [file]
```

## <a id="win10-Linux">win10下ubuntu子系统与windows的目录影射</a>
```
WSL和Windows主系统之间的文件系统是可以互相访问的

如果在WSL中访问Windows系统的文件，可在根目录下/mnt/看到对应Windows盘符字母的文件夹，通过这些文件夹即可访问Windows的文件系统。

如果在Windows系统中访问WSL的文件,Windows系统中找到已安装Linux发行版的应用数据文件夹，所有Linux系统的数据都在那个文件夹(C:\Users\{你的用户名}\AppData\Local\Packages\{Linux发行版包名}\LocalState\rootfs)
C:\Users\Administrator\AppData\Local\Packages\CanonicalGroupLimited.UbuntuonWindows_79rhkp1fndgsc\LocalState\rootfs
```

## <a id="ubuntu-date">ubuntu时区设置</a>
```bash
# 查看当前系统时间
marhal@marhal:~$ date -R 
# Wed, 08 Dec 2021 09:57:52 +0000

# 设置时区
marhal@marhal:~$ tzselect
# 依次选择`亚洲(Asia)`,`中国(China)`,选择`北京(Beijing)`,选择 `是(YES)`
# 依次输入 4           10               1                  1


marhal@marhal:~$ sudo cp /usr/share/zoneinfo/Asia/Shanghai  /etc/localtime
marhal@marhal:~$ date -R 
# Wed, 08 Dec 2021 17:59:07 +0800
```






## <a id="curl">curl</a>

* 使用POST登录保存cookie文件
```bash
curl -k -X POST -c cookie.txt --header 'Content-Type: application/json' -d{"name"="18800000000","psd"="Admin123"} http://aa.com
```

* 使cookie文件POST登录

```bash
curl 
	-XPOST 
	-d '{"cellphone":"18800000000","password":"PXpassword0000"}' 
	--header "Content-Type: application/json;charset=UTF-8" 
	-c cookie_xx  
	http://oa.credit.com/api/signIn
```

* 使cookie文件GET查询
```bash
curl -b cookie.txt http://xxx.com
```

## <a id="ssh">SSH</a>

* ssh连接远程服务器
```bash
ssh -p 1211 marhal@47.93.45.242
```

* ssh中文乱码
```bash
export LANG=C
export LC_ALL=zh_CN.utf-8
export LANG=zh_CN.utf-8
```



## <a id="k8s">k8s Command</a>

```bash
ssh -D 127.0.0.1:8080 dev@47.96.157.236 -p 17456 -i /Users/apple/Documents/id_rsa_credit_sanbox

ssh -p 17456 172.25.1.1

# 获取命名空间
kubectl get namespaces;
# 获取命名空间下的容器
kubectl get pods -n=credit-ll
# 构建
kubectl edit deployment/credit-backend -n=credit-ll
# 进入容器
kubectl exec -it credit-backend -n=credit-ll -c=phpfpm bash
# 日志
kubectl logs -f credit-portal-6557fc89bf-x6l2t -n=credit-ty -c=phpfpm
```


## <a id="npm">NPM Command</a>virtualbox-ubuntu-ip

```bash
# 注册模块镜像
npm set registry https://registry.npm.taobao.org 
yarn config set registry https://registry.npm.taobao.org/
# node-gyp 编译依赖的 node 源码镜像
npm set disturl https://npm.taobao.org/dist 
# 以下选择添加
## chromedriver 二进制包镜像
npm set chromedriver_cdnurl http://cdn.npm.taobao.org/dist/chromedriver
## operadriver 二进制包镜像
npm set operadriver_cdnurl http://cdn.npm.taobao.org/dist/operadriver
## phantomjs 二进制包镜像
npm set phantomjs_cdnurl http://cdn.npm.taobao.org/dist/phantomjs
## node-sass 二进制包镜像
npm set sass_binary_site http://cdn.npm.taobao.org/dist/node-sass
## electron 二进制包镜像
npm set electron_mirror http://cdn.npm.taobao.org/dist/electron/ 
# 清空缓存
npm cache clean
```

## <a id="virtualbox-ubuntu-ip">virtualbox 配置ubuntu固定ip</a>
```bash
marhal@marhal:~$ cd /etc/netplan
marhal@marhal:~$ sudo cp /etc/netplan/00-installer-config.yaml /etc/netplan/00-installer-config.yaml.bak
marhal@marhal:~$ sudo vim 00-installer-config.yaml
```
替换内容
```yaml
network:
  ethernets:
    enp0s3:
      dhcp4: no
      addresses: [172.16.0.188/23]
      gateway4: 172.16.0.1
      nameservers:
              addresses: [172.16.0.1, 144.144.144.144]
  version: 2
```
```bash
marhal@marhal:~$ sudo netplan apply
marhal@marhal:~$ sudo vim /etc/resolv.conf
```

修改 nameserver

```text
nameserver 114.114.114.114
```
```bash
marhal@marhal:~$ source ~/.bashrc
```

## <a id="virtualbox-ubuntu-mount">virtualbox挂载目录</a>

### 在主界面设置好挂载信息

![img.png](img/img.png)

### 命令
```bash
### 格式
### sudo mount -t vboxsf 共享文件夹名称（在设置页面设置的） 挂载的目录
:~$ sudo mount -t vboxsf test /var/www/html/test
```

