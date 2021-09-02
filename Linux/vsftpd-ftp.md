# ubuntu 使用vsftpd 创建FTP服务

## 准备工作
```bash
# 下载 vsftpd
sudo apt-get install vsftpd
# 备份配置文件
sudo cp /etc/vsftpd.conf /etc/vsftpd.conf.bak
#  建立用户配置目录
mkdir -p /etc/vsftpd/userconf
# 编辑配置文件
sudo vim /etc/vsftpd.config
```

## 开启或者增加以下配置
```
local_enable=YES
write_enable=YES
local_umask=022
xferlog_file=/var/log/vsftpd.log
xferlog_std_format=YES
ftpd_banner=Welcome Lincoln Linux FTP Service.
chroot_list_enable=YES
chroot_list_file=/etc/vsftpd.chroot_list
pam_service_name=ftp # 原来是vsftpd
utf8_filesystem=YES
userlist_enable=YES
userlist_deny=NO
userlist_file=/etc/vsftpd.user_list
allow_writeable_chroot=YES
user_config_dir=/etc/vsftpd/userconf
```

## 创建用户

```bash
# 创建ftp目录
sudo mkdir /var/www/html/myWeb
# 添加用户
sudo useradd -d /var/www/html/myWeb -s /bin/bash myWebUser
# 设置用户密码
sudo passwd myWebUser
# 设置ftp目录用户权限
sudo chown -R myWebUser:myWebUser /var/www/html/myWeb
# 添加vsftpd 登录用户
sudo touch /etc/vsftpd.chroot_list
# 编辑
sudo vim /etc/vsftpd.chroot_list

# 写入用户名，一行一个
# myWebUser
# 保存退出

# 添加用户配置文件 myWebUser为用户名
sudo vi /etc/vsftpd/userconf/myWebUser
# 写入目录
local_root=/var/www/html/myWeb # (具体目录)
# 保存退出

# 重启退出
sudo service vsftpd restart
```


