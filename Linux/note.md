* [Linux](#Linux)
* [win10下ubuntu子系统与windows的目录影射](#win10-Linux)
* [ubuntu时区设置](#ubuntu-date)

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


