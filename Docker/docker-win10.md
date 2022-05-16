# win10安装docker

* 启用虚拟化
    * 进入任务管理器（ctrl+alt+delete），
    * 点击性能->cpu ,查看虚拟化是否已启用，
    * 如果虚拟化是已禁用，重启电脑进入bios开启虚拟化
    * 进入电脑的控制面板->程序->启用或关闭Windows功能->勾选Hyper-v确定
    * 重启电脑
* 启用适用于Linux的Windows子系统
    * 方法一：PowerShell：Enable-WindowsOptionalFeature -Online -FeatureName Microsoft-Windows-Subsystem-Linux
    * 方法二：控制面板->启用或关闭Windows功能->启用适用于Linux的Windows子系统打勾->确定
* Microsoft Store 下载 ubuntu
* 下载并安装 [Docker for Windows](https://docs.docker.com/docker-for-windows/install/#download-docker-for-windows)

# 子系统安装docker
```bash
sudo apt-get remove docker docker-engine  docker.io
sudo apt-get update
sudo apt-get install apt-transport-https ca-certificates curl gnupg lsb-release
curl -fsSL https://mirrors.aliyun.com/docker-ce/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
# 官方源
# $ curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
echo \
 "deb [arch=amd64 signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://mirrors.aliyun.com/docker-ce/linux/ubuntu \
  $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
# 官方源
# $ echo \
#   "deb [arch=amd64 signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu \
#   $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
sudo apt-get update
sudo apt-get install docker-ce docker-ce-cli containerd.io
```

# 联动 [参考文档](https://www.cnblogs.com/xiaoliangge/p/9134585.html)

## win10
* 打开docker面板 点击右上角设置 -> General -> 勾选 `Expose daemon on tcp://localhost:2375 without TLS` 和 `Use the WSL 2 based engine`

## 子系统
```bash
penk@DESKTOP-4M3N2SI:~$ sudo service docker status
* Docker is not running

penk@DESKTOP-4M3N2SI:~$ vim ~/.bashrc

#在文末写入 ：export DOCKER_HOST=tcp://127.0.0.1:2375

# 开启tcp连接
penk@DESKTOP-4M3N2SI:~$ vim  /lib/systemd/system/docker.service

#在文末写入 ：ExecStart=/usr/bin/dockerd -H unix:///var/run/docker.sock -H tcp://0.0.0.0:2375
```

## 测试
```bash
# 在win10执行
docker run -d -p 80:80 docker/getting-started
# 在子系统查看
docker ps -a
# 出现了windows上拉的镜像
# penk@DESKTOP-4M3N2SI:~$ docker ps -a
# CONTAINER ID   IMAGE                    COMMAND                  CREATED         STATUS         PORTS                NAMES
# 7e22f5e5cfba   docker/getting-started   "/docker-entrypoint.…"   5 seconds ago   Up 4 seconds   0.0.0.0:80->80/tcp   nervous_sanderson
```

## docker-compose
* 下载 https://github.com/docker/compose/releases/download/v2.2.1/docker-compose-windows-x86_64.exe
* windowsPowerShell

```bash
PS C:\Users\penk009> docker-compose -v
# Docker Compose version v2.1.1
PS C:\Users\penk009> wsl -l -v
#  NAME                   STATE           VERSION
#* docker-desktop-data    Running         2
#  Ubuntu-20.04           Running         1
#  docker-desktop         Running         2

PS C:\Users\penk009> wsl --set-version Ubuntu-20.04 2
# 正在进行转换，这可能需要几分钟时间...
# 有关与 WSL 2 的主要区别的信息，请访问 https://aka.ms/wsl2
# 转换完成。
```
* 子系统会关闭
* 打开DockerDesktop setting->Resources->WSL Integration
* 勾选Ubuntu-20.04和 Enable integration with my default WSL distro
* 子系统

```bash 
sudo curl -L "https://github.com/docker/compose/releases/download/2.2.1/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
docker-compose -v
Docker Compose version v2.1.1
```
