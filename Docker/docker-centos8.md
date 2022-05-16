# install
```bash
# 安装依赖
yum install -y yum-utils

# 配置docker的源地址
yum-config-manager --add-repo http://mirrors.aliyun.com/docker-ce/linux/centos/docker-ce.repo

# 安装docker
yum install -y docker-ce docker-ce-cli containerd.io

# 启动
systemctl enable docker --now


```

# config
### 配置镜像加速的地址（用于快速的拉取docker镜像）
```bash
mkdir -p /etc/docker

tee /etc/docker/daemon.json <<-'EOF'
{
  "registry-mirrors": ["https://o2wpcbk0.mirror.aliyuncs.com"],#此位置换成服务器合适的地址
  "exec-opts": ["native.cgroupdriver=systemd"],
  "log-driver": "json-file",
  "log-opts": {
    "max-size": "100m"
  },
  "storage-driver": "overlay2"
}
EOF

sudo systemctl daemon-reload

sudo systemctl restart docker
```

# error

### 安装docker时提示 podman 冲突，版本不对等问题
#### error
```text
Docker CE Stable - x86_64                                                                                                                                                        2.2 kB/s |  23 kB     00:10
Error:
 Problem 1: problem with installed package podman-2:4.0.2-1.module_el8.7.0+1106+45480ee0.x86_64
  - package podman-2:4.0.2-1.module_el8.7.0+1106+45480ee0.x86_64 requires runc >= 1.0.0-57, but none of the providers can be installed
```

#### 解决
```bash
yum erase podman buildah
```