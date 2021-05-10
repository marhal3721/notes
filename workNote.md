* [MySql](#mysql)
    * [更改字段非空](#更改字段非空)
    * [删除/增加主键](#删除/增加主键)
    * [设置主键自增](#设置主键自增)
    * [添加字段](#添加字段)
    * [添加索引](#添加索引)
    * [导入备份数据库](#导入备份数据库)
    * [设置最允许导入值](#设置最允许导入值)
    * [导出](#导出)
    * [全文索引](#全文索引)
* [Curl](#curl)
* [Git](#git)
    * [创建并切换分支](#创建并切换分支) 
    * [查看远程分支](#查看远程分支)
    * [查看所有分支](#查看所有分支)
    * [删除本地分支](#删除本地分支)
    * [删除远程分支](#删除远程分支)
    * [Tag](#Tag)
    * [撤销commit](#撤销commit)
    * [撤销add](#撤销add)
    * [BranchExample](#BranchExample)
    * [撤销远程push](#撤销远程push)
    * [Git添加秘钥](#Git)
* [SSH](#ssh)
* [GitHub](#GitHub)
* [Docker](#Docker)
* [k8s](#k8s)
* [Linux](#Linux)
* [NPM](#npm)
* [MongoDb](#mongo)
* [Composer](#Composer)

## <a id="mysql">mySql</a>

* <a id="更改字段非空">更改字段非空</a>
```mysql
ALTER TABLE `tableNname` ALTER COLUMN `columnName` int(11) NOT NULL;
```
* <a id="删除/增加主键">删除/增加主键</a>
```mysql
ALTER TABLE `tableNname` DROP CONSTRAINT PK_name（主键名字）;
```
```mysql
ALTER TABLE `tableNname` ADD CONSTRAINT PK_name primary key(`columnName`);
```
* <a id="设置主键自增">设置主键自增</a>
```mysql
ALTER TABLE `tableNname` CHANGE `columnName` `columnName` INT(11) AUTO_INCREMENT;
```
* <a id="添加字段">添加字段</a>
```mysql
ALTER TABLE `tableNname` ADD `columnName` tinyint(1) NOT NULL DEFULT 1;
```

* <a id="添加索引">添加索引</a>
```mysql
ALTER TABLE `tableNname` ADD INDEX idx_name ( `columnName` )
```

* <a id="导入备份数据库">导入备份数据库</a>
```mysql
source /home/abc/abc.sql;
```

* <a id="设置最允许导入值">设置最允许导入值</a>
```mysql
show global variables like 'max_allowed_packet';
set global max_allowed_packet = 52428800(1024*1024*50)
```

* <a id="导出">导出命令 多个表用空格断开</a>
```bash
mysqldump -uroot -p  dbname tablename1 tablename2 > tablename.sql

```


```
# 导出到xlsx/csv文件
SELECT * FROM `tablename` into outfile '/var/lib/mysql-files/tablename.xlsx';
# 执行上述命令可能会提示下面的错误
ERROR 1290 (HY000): The MySQL server is running with the --secure-file-priv option so it cannot execute this statement
# 查看保存路径
## secure_file_priv=null 不允许文件的导入导出
## secure_file_priv=xxx 文件导入导出到某路径
## secure_file_priv=/ 文件可导入到任意路径
SHOW VARIABLES LIKE "secure_file_priv";

# 更改权限为mysql用户
sudo chown -R mysql:mysql /var/lib/mysql-files/
```


* <a id="全文索引">全文索引</a> [官方文档](https://dev.mysql.com/doc/refman/5.7/en/fulltext-boolean.html)
    * 查找字符长度受innodb_ft_max_token_size和innodb_ft_min_token_size影响  
```mysql
CREATE FULLTXT INDEX ft_idx_name ON `tableNname`(`columnName`);
```

* 全文索引使用 ngram 解释器
    * 查找字符长度受ngram_token_size影响  
```mysql
CREATE FULLTXT INDEX ft_idx_name ON `tableNname`(`columnName`) WITH PARSER NGRAM;
```

* 全文索引查询语法
```mysql
SELECT * FROM comments WHERE MATCH (contents) AGAINST ('+47 +90' IN BOOLEAN MODE);
SELECT * FROM comments WHERE MATCH (contents) AGAINST ('47 90' IN NATURAL LANGUAGE MODE);
```

* 查看数据库中那些表有主键
```
select t1.table_schema,t1.table_name from information_schema.tables t1 
left outer join
information_schema.TABLE_CONSTRAINTS t2   
on t1.table_schema = t2.TABLE_SCHEMA  and t1.table_name = t2.TABLE_NAME  and t2.CONSTRAINT_NAME in
('PRIMARY') 
where t2.table_name is not null and t1.TABLE_SCHEMA not in ('information_schema','performance_schema','test','mysql', 'sys');
```

* 查看数据库中那些表没有主键
```
select t1.table_schema,t1.table_name from information_schema.tables t1 
left outer join
information_schema.TABLE_CONSTRAINTS t2   
on t1.table_schema = t2.TABLE_SCHEMA  and t1.table_name = t2.TABLE_NAME  and t2.CONSTRAINT_NAME in
('PRIMARY') 
where t2.table_name is null and t1.TABLE_SCHEMA not in ('information_schema','performance_schema','test','mysql', 'sys');
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
curl -b cookie.txt http://oa.nanyang.com/api/resourceCatalogType?page=1&limit=10&type=MA
```

## <a id="git">git</a>

* 切换分支
```bash
git checkout [branch name] 
```

* <a id="创建并切换分支">创建并切换分支</a>
```bash
git checkout -b [branch name] 
```

* 关联分支
```bash
git branch --set-upstream-to=origin/dev [branch name] 
```

* 推送分支
```bash
git push -u origin [branch name]
```

* 忘记切换分支已经更改了代码
* 1. 把当前未提交到本地（和服务器）的代码推入到 Git 的栈中
```bash
git stash
```
* 2. 切换分支
```bash
git checkout [branch name]
```
* 3. 将栈里面存放的代码应用回来
```bash
# 保留栈里面数据
git stash apply
```
或
```bash
# 删除栈里面数据
git stash pop
```
* 4. 清空栈
```bash
git stash clear
```

* <a id="查看远程分支">查看远程分支</a>
```bash
git branch -r
```

* <a id="查看所有分支">查看所有分支</a>
```bash
git branch -a
```

* 创建本地分支
```bash
git branch [branch name]
```

* <a id="删除本地分支">删除本地分支</a>

```bash
# 删除一个已被终止的分支
git branch -d [branch name]

# 删除一个正打开的分支---强制删除
git branch -D [branch name]
```

* <a id="删除远程分支">删除远程分支</a>
```bash
git push origin :[branch name]
git push origin --delete [branch name]
```
* <a id="Tag">Tag</a>

```bash
# tag 列表
git tag
# 创建tag
git tag -a v0.9.0 -m "release 0.9.0 version"
# 推送tag
git push origin [branch name] --tag
# 删除本地tag
git tag -d 'v0.1.0'
# 删除远程tag
git push origin :refs/tags/v0.1.0
```

* <a id="撤销commit">撤销已经commit但未push的commit</a>
```
git reset --soft HEAD^ 
```

* <a id="撤销add">撤销add</a>
```
git reset HEAD
```

* <a id="BranchExample">Branch Example</a>
```
git checkout -b feature-#90707-resourceCata 
git push -u origin feature-#90707-resourceCata
git commit -m '推送备注'
git push
git checkout dev && git pull && git merge feature-\#90707-resourceCata && git push
```
* <a id="撤销远程push">撤销远程push</a>
```bash
# 查看日志，获取需要回退的版本号
git log
# 方式一：重置到指定版本的提交，达到撤销提交的目的
git reset --soft <版本号>
# 方式二：撤销commit，同时将代码恢复到对应ID的版本
git reset --hard <commitId>
# 强制提交到当前版本号
git push origin <分支名称> --force
```

* <a id="Git">Git添加秘钥</a>

```bash
ssh-keygen -t rsa -C "junwuji555@sina.com"
cat .ssh/id_rsa.pub
ssh-add .ssh/id_rsa

# linux 下解决仍需要输入账号密码的问题
git config --global credential.helper store
# 测试连接
ssh -T git@github.com
# git配置信息
git config --list
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


## <a id="Docker">Docker Command</a>

```bash
# 显示指定的列
docker ps -a --format "table {{.ID}}\t{{.Names}}\t{{.Ports}}\t{{.Status}}"
# 日志
docker -if ny-backend-phpfpm
# 进入容器
docker exec -it ny-spider-phpfpm bash
# 停止并删除容器、清除网络
docker stop $(docker ps -a -q) && docker rm $(docker ps -a -q) && docker network prune
# 创建网络
docker network create database && docker network create application
# docker-compose启动
docker-compose up -d
# 删除已经退出的容器
docker rm $(docker ps -qf status=exited)
# 本地文件、文件夹复制到容器
docker cp /var/www/html 96f7f14e99ab:/www/
# 容器文件、文件夹复制到本地
docker cp  96f7f14e99ab:/www /tmp/
# 强制删除
docker rm -f $(docker ps -a -q)
# 输入多个命令
docker exec ny-backend-phpfpm bash -c "composer clear && composer update"
# 容器输入中文
kubectl exec -it hzlh-mysql-0 -n=hzlh-pre env LANG=C.UTF-8 /bin/bash
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
```

## <a id="npm">NPM Command</a>

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

## <a id="mongo">MongoDb Command</a>

```bash
# 连接
mongo 127.0.0.1:27017
mongo --host 127.0.0.1 -u "huizhonglianhe" --authenticationDatabase "admin" -p
# 查看db列表
show dbs
# 切换库
use resourceCatalog
# 删除当前库
db.dropDatabase()
# 查看全部表
show collections
# 查看表数量
db.collectionName.count()
# 查找【pretty 只是美观展示，相当于mysql的\G】
db.collectionName.find().pretty() 
# 查询json数据二级数据
db.collectionName.find({"mobile.number":"18888888888"}).pretty()
# 等于条件查询
db.collectionName.find({"_id" : ObjectId("5e7a0ae837b3d100aa06f275"),"authType" : 2}).pretty()
# in查询，参数一为条件，参数二为字段
db.collectionName.find({"service_id":{$in:["342","209","10"]}},{"service_id":1,"_id":1,"title":1}).pretty();
# 删除一条数据
db.collectionName.remove({"id":"bar"})  //删除一条数据
# 删除collectionName中的所有记录，但是collectionName还存在
db.collectionName.remove()
# 删除collection，Mongodb不会自动释放文件空间
db.collectionName.drop()
# 把不需要的空间释放出来
db.repairDatabase()

# 备份单个表
mongodump -u  superuser -p 123456  --port 27017 --authenticationDatabase admin -d myTest -c d -o /backup/mongodb/myTest_d_bak_201507021701.bak

# 备份单个库
mongodump  -u  superuser -p 123456 --port 27017  --authenticationDatabase admin -d myTest -o  /backup/mongodb/

# 备份所有库
mongodump  -u  superuser -p 123456 --authenticationDatabase admin  --port 27017 -o /root/bak 

# 备份所有库推荐使用添加--oplog参数的命令，这样的备份是基于某一时间点的快照，只能用于备份全部库时才可用，单库和单表不适用：
mongodump -h 127.0.0.1 --port 27017   --oplog -o  /root/bak 

# 同时，恢复时也要加上--oplogReplay参数，具体命令如下(下面是恢复单库的命令)：
mongorestore  -d swrd --oplogReplay  /home/mongo/swrdbak/swrd/

# 恢复单个库：
mongorestore  -u  superuser -p 123456 --port 27017  --authenticationDatabase admin -d myTest   /backup/mongodb/

# 恢复所有库：
mongorestore   -u  superuser -p 123456 --port 27017  --authenticationDatabase admin  /root/bak

# 恢复单表
mongorestore -u  superuser -p 123456  --authenticationDatabase admin -d myTest -c d /backup/mongodb/myTest_d_bak_201507021701.bak/myTest/d.bson

cd /usr/bin
# 导出到csv
mongoexport --port 30000 -d db -c collection -q {} -f _id,name --type=csv > ./1.csv
# 导出到json
mongoexport --port 30000 -d db -c collection -q {"moduleId" : "5638a07a45cece11ce3f106a"} -f _id,name --type=json --file> ./1.json
# 根据条件查询
mongoexport --port 30000 -uuser_backup -ppasswd -d dbname -c collection --type=json -o /data/service/backup_mongodblog/collection.json --query='{"moduleId" : "5638a07a45cece11ce3f106a"}' --limit=5
```
## <a id="Composer">Composer Command</a>

```bash
# 不用修改php.ini配置文件，临时解禁composer运行内存限制
php -d memory_limit=-1 /usr/local/bin/composer require/install/update

# 忽略版本匹配
composer install --ignore-platform-reqs

# 当 composer.json 被修改后，需要重新加载一次
composer dump-autoload
```

* 设置镜像

```bash
# composer查看全局设置
composer config -gl

# 设置阿里云镜像
composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

# 设置国内镜像
composer config -g repo.packagist composer https://packagist.phpcomposer.com

# 设置国际镜像
composer config -g repo.packagist composer https://packagist.org

# 取消配置
composer config -g --unset repos.packagist

# 清空缓存
composer clear-cache

# 输出详细的信息
composer -vvv require alibabacloud/sdk

```

* 问题解决

```bash
# 将Composer版本升级到最新
composer self-update

# 执行诊断命令
composer diagnose

# 清除缓存
composer clear
```