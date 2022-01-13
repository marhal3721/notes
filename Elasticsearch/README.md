# Install-ubuntu
[OfficeDocs](https://www.elastic.co/guide/en/elasticsearch/reference/7.16/deb.html#deb-repo)
```bash
wget -qO - https://artifacts.elastic.co/GPG-KEY-elasticsearch | sudo apt-key add -
sudo apt-get install apt-transport-https
echo "deb https://artifacts.elastic.co/packages/7.x/apt stable main" | sudo tee /etc/apt/sources.list.d/elastic-7.x.list
sudo apt-get update && sudo apt-get install elasticsearch

# 自启
sudo systemctl daemon-reload
sudo systemctl enable elasticsearch.service
# 启动
sudo systemctl start elasticsearch.service
# 内网测试
curl localhost:9200

```

## 外网访问
```bash
su root
vim elasticsearch.yml
# 添加配置
# network.host: 0.0.0.0
# cluster.initial_master_nodes: ["node-1", "node-2"]

vim /etc/sysctl.conf
# 添加配置
# vm.max_map_count=655360

sysctl -p

# 重启
sudo service elasticsearch restart
# 测试
curl 192.168.3.70:9200
```

# Installing Logstash
```bash
wget -qO - https://artifacts.elastic.co/GPG-KEY-elasticsearch | sudo apt-key add -
sudo apt-get install apt-transport-https
echo "deb https://artifacts.elastic.co/packages/7.x/apt stable main" | sudo tee -a /etc/apt/sources.list.d/elastic-7.x.list
sudo apt-get update && sudo apt-get install logstash

```
# Installing  kibana

## APT 安装
* 启动不了，原因未知

```bash
wget -qO - https://artifacts.elastic.co/GPG-KEY-elasticsearch | sudo apt-key add -
sudo apt-get install apt-transport-https
echo "deb https://artifacts.elastic.co/packages/7.x/apt stable main" | sudo tee -a /etc/apt/sources.list.d/elastic-7.x.list
sudo apt-get update && sudo apt-get install kibana
# 开机自启
sudo update-rc.d kibana defaults 95 10
# 启动
sudo systemctl start kibana.service
sudo -i service kibana start
# 停止
sudo systemctl stop kibana.service
sudo -i service kibana stop
```

## tar.gz 安装
```bash
wget https://artifacts.elastic.co/downloads/kibana/kibana-7.16.2-linux-x86_64.tar.gz
tar zxvf kibana-7.16.2-linux-x86_64.tar.gz
cd kibana-7.16.2-linux-x86_64/bin
vim ../config/kibana.yml
## kibana 7 中官方加入了中文的选项
# i18n.locale: "zh-CN"
# server.host: "0.0.0.0" # 外网访问
# elasticsearch.hosts: ["http://127.0.0.1:9200"]
# server.publicBaseUrl: "http://172.31.240.57:5601"
nohup ./kibana > /home/marhal/kibana-nohup.log 2>&1 &
```

# 概念

## 释义
|EN|CN|remark|
|---|---|---|
|Index| 索引|DB数据库|
|Type| 类型|数据表 7.X 中, Type 的概念已经被删除了|
|Document |文档|表中一条记录|
|Field|字段|记录中的每个列属性|
|Shard|分片|对索引进行分片，分布于集群各个节点上，降低单个节点的压力|
|Replica|备份|拷贝分片就完成了备份|

## 遵循 `RESTFUL` 架构约束条件和原则
* GET
* PUT(幂等)
* HEAD
* DELETE
