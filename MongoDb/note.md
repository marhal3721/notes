* [MongoDb](#mongo)


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