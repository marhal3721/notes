## 微信多开

```
nohup /Applications/WeChat.app/Contents/MacOS/WeChat > /dev/null 2>&1 &
```

## 完全卸载phpstorm

```
rm /Users/apple/Library/Preferences/jetbrains.*
rm -rf /Users/apple/Library/Caches/com.jetbrains.PhpStorm
rm -rf /Users/apple/Library/Caches/JetBrains
rm -rf /Users/apple/Library/Application Support/JetBrains
rm -rf /Users/apple/Library/Logs/JetBrains
```

## ping

```bash
nc -vz -w 2 127.0.0.1[ip] 80[port]
```

## 安装brew
```bash
/usr/bin/ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"
```

## 安装redis 后面@接版本号可指定版本

```bash
brew install redis
```

## 启动redis

```bash
# 启动/暂停/重启
brew services start/stop/restart redis
```

## 开机自启动

```bash
ln -sfv /usr/local/opt/redis/*.plist ~/Library/LaunchAgents
```

## 使用配置文件启动

```
redis-server /usr/local/etc/redis.conf
```

## 卸载

```
brew uninstall redis 
rm ~/Library/LaunchAgents/homebrew.mxcl.redis.plist
```

## 查看所有redis进程

```
ps aux|grep redis
```

## 查看端口占用

```
lsof -i:4990
```

## 添加环境变量

```
~ vim ~/.bash_profile
```

* export + 自定义名字（GO） = 路径名称
```
~ export GO=/usr/local/go/bin/
```
* export PATH=$PATH:$+自定义名字（GO）
```
~ export PATH=$PATH:$GO
```
* 生效
```
~ source ~/.bash_profile
```
* 查看环境变量
```
~ echo $PATH
```

## 添加命令别名

```bash
~ vim ~/.bash_profile
```

```text
alias gst='git status'
alias drmf = 'docker rm -f $(docker ps -a -q)'
alias drm = 'docker rm -f $(docker ps -a -q)'
```
