## install
```bash
sudo apt install supervisor 
```

## Command
```bash
sudo service supervisor status
sudo service supervisor start
sudo service supervisor stop
sudo service supervisor restart
sudo service supervisor reload
sudo service supervisor force-reload
sudo supervisord -c /etc/supervisord.conf
```
```bash
# 查看所有进程的状态
sudo supervisorctl status
# 停止xx(all管理配置中的所有进程)
sudo supervisorctl stop xx
# 启动
sudo supervisorctl start xx
# 重启
sudo supervisorctl restart xx
# 配置文件修改后使用该命令加载新的配置
sudo supervisorctl update xx
# 重新启动配置中的所有程序
sudo supervisorctl reload xx
```

## 配置

### 主配置文件
* /etc/supervisor/supervisord.conf
* supervisor的配置文件默认是不全的，不过在大部分默认的情况下，基本功能已经满足

#### 文件说明
```editorconfig
[unix_http_server]
file=/var/run/supervisor.sock   ;UNIX socket 文件，supervisorctl 会使用
;chmod=0700                 ;socket文件的mode，默认是0700
;chown=nobody:nogroup       ;socket文件的owner，格式：uid:gid
 
;[inet_http_server]         ;HTTP服务器，提供web管理界面
;port=127.0.0.1:9001        ;Web管理后台运行的IP和端口，如果开放到公网，需要注意安全性
;username=user              ;登录管理后台的用户名
;password=123               ;登录管理后台的密码
 
[supervisord]
logfile=/var/log/supervisor/supervisord.log ;日志文件，默认是 $CWD/supervisord.log
pidfile=/var/run/supervisord.pid ;pid 文件
childlogdir=/var/log/supervisor

logfile_maxbytes=50MB        ;日志文件大小，超出会rotate，默认 50MB，如果设成0，表示不限制大小
logfile_backups=10           ;日志文件保留备份数量默认10，设为0表示不备份
loglevel=info                ;日志级别，默认info，其它: debug,warn,trace
nodaemon=false               ;是否在前台启动，默认是false，即以 daemon 的方式启动
minfds=1024                  ;可以打开的文件描述符的最小值，默认 1024
minprocs=200                 ;可以打开的进程数的最小值，默认 200
 
[supervisorctl]
serverurl=unix:///var/run/supervisor.sock ;通过UNIX socket连接supervisord，路径与unix_http_server部分的file一致
;serverurl=http://127.0.0.1:9001 ; 通过HTTP的方式连接supervisord
 
;; [program:xx]是被管理的进程配置参数，xx是进程的名称
;[program:xx]
;command=/opt/apache-tomcat-8.0.35/bin/catalina.sh run  ; 程序启动命令
;autostart=true       ; 在supervisord启动的时候也自动启动
;startsecs=10         ; 启动10秒后没有异常退出，就表示进程正常启动了，默认为1秒
;autorestart=true     ; 程序退出后自动重启,可选值：[unexpected,true,false]，默认为unexpected，表示进程意外杀死后才重启
;startretries=3       ; 启动失败自动重试次数，默认是3
;user=tomcat          ; 用哪个用户启动进程，默认是root
;priority=999         ; 进程启动优先级，默认999，值小的优先启动
;redirect_stderr=true ; 把stderr重定向到stdout，默认false
;stdout_logfile_maxbytes=20MB  ; stdout 日志文件大小，默认50MB
;stdout_logfile_backups = 20   ; stdout 日志文件备份数，默认是10
;; stdout 日志文件，需要注意当指定目录不存在时无法正常启动，所以需要手动创建目录（supervisord 会自动创建日志文件）
;stdout_logfile=/opt/apache-tomcat-8.0.35/logs/catalina.out
;stopasgroup=false     ;默认为false,进程被杀死时，是否向这个进程组发送stop信号，包括子进程
;killasgroup=false     ;默认为false，向进程组发送kill信号，包括子进程
 
;;包含其它配置文件
[include]
files = /etc/supervisor/conf.d/*.conf    ;可以指定一个或多个以.conf结束的配置文件

```

### 子配置文件
* /etc/supervisor/conf.d/
* 默认子进程配置文件为ini格式，可在supervisor主配置文件中修改

#### 文件说明
```editorconfig
#项目名
[program:blog]
#脚本目录
directory=/opt/bin
#脚本执行命令
command=/usr/bin/python /opt/bin/test.py

#supervisor启动的时候是否随着同时启动，默认True
autostart=true
# 当程序exit的时候，这个program不会自动重启,默认unexpected，
# 设置子进程挂掉后自动重启的情况，有三个选项，false,unexpected和true。
# 如果为false的时候，无论什么情况下，都不会被重新启动，
# 如果为unexpected，只有当进程的退出码不在下面的exitcodes里面定义的
autorestart=false
#这个选项是子进程启动多少秒之后，此时状态如果是running，则我们认为启动成功了。默认值为1
startsecs=1

#脚本运行的用户身份 
;user = test

#日志输出 
stderr_logfile=/tmp/blog_stderr.log 
stdout_logfile=/tmp/blog_stdout.log 
#把stderr重定向到stdout，默认 false
redirect_stderr = true
#stdout日志文件大小，默认 50MB
stdout_logfile_maxbytes = 20MB
#stdout日志文件备份数
stdout_logfile_backups = 20

```



