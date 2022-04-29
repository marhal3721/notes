# install

```bash
# yum install epel-release

yum install python3-pip 

dnf install -y python3

dnf install python3-paramiko

touch /home/root/.pip/pip.conf

vim /home/root/.pip/pip.conf
```
```editorconfig
[global]
index-url= https://pypi.tuna.tsinghua.edu.cn/simple
 
[install]
trusted-host=pypi.tuna.tsinghua.edu.cn
```
```bash
pip3 install docker-compose  

docker-compose --version
```