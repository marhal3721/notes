# 1.mysql 启动失败
* su: warning: cannot change directory to /nonexistent: No such file or directory

## 原因
* 一般是 mysql 服务器异常关机导致

## 解决
* ubuntu
```bash
sudo service mysql stop
sudo usermod -d /var/lib/mysql/ mysql
sudo service mysql start
```

* centos
```bash
sudo systemctl stop mysql.service
sudo usermod -d /var/lib/mysql/ mysql
sudo systemctl start mysql.service
```

---