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

# 索引操作
## 创建索引
```bash
curl --location --request PUT '192.168.3.70:9200/marhal'
```
**Response**
```json
{
	"acknowledged": true,
	"shards_acknowledged": true,
	"index": "marhal"
}
```

**Properties**

| Properties | Description | Type   | 
| :---:      | :----:      | :----: |
| acknowledged      | 响应结果         | bool    |  
| shards_acknowledged       | 分片结果         | bool  | 
| index       | 索引         | string  | 

**Notice**
* 创建索引库的分片数默认 1 片，在 7.0.0 之前的 Elasticsearch 版本中，默认 5 片
* 如果重复添加索引，会返回错误信息

## 获取索引
```bash
curl --location --request GET '192.168.3.70:9200/marhal'
```
**Response**
```json
{
  "marhal【索引名】": {
    "aliases【别名】": {},
    "mappings【映射】": {},
    "settings【设置】": {
      "index【设置 - 索引】": {
        "routing": {
          "allocation": {
            "include": {
              "_tier_preference": "data_content"
            }
          }
        },
        "number_of_shards【设置 - 索引 - 主分片数量】": "1",
        "provided_name【设置 - 索引 - 名称】": "marhal",
        "creation_date【设置 - 索引 - 创建时间】": "1641464597688",
        "number_of_replicas【设置 - 索引 - 副分片数量】": "1",
        "uuid": "fMAPCbLXQTOhdBoI2lWKQg",
        "version【设置 - 索引 - 版本】": {
          "created": "7160299"
        }
      }
    }
  }
}
```

## 删除索引
```bash
curl --location --request DELETE '192.168.3.70:9200/marhal'
```

**Response**
* success

```json
{
	"acknowledged": true
}
```
* error

```json
{
	"error": {
		"root_cause": [
			{
				"type": "index_not_found_exception",
				"reason": "no such index [marhal2]",
				"resource.type": "index_or_alias",
				"resource.id": "marhal2",
				"index_uuid": "_na_",
				"index": "marhal2"
			}
		],
		"type": "index_not_found_exception",
		"reason": "no such index [marhal2]",
		"resource.type": "index_or_alias",
		"resource.id": "marhal2",
		"index_uuid": "_na_",
		"index": "marhal2"
	},
	"status": 404
}
```

## 获取所有索引
```bash
curl --location --request GET '192.168.3.70:9200/_cat/indices?v'
```

**Response**
```text
health status index            uuid                   pri rep docs.count docs.deleted store.size pri.store.size
green  open   .geoip_databases QtRe7fUKToWeQ1LJYj9vAg   1   0         43            0     40.1mb         40.1mb
yellow open   marhal           fMAPCbLXQTOhdBoI2lWKQg   1   1          0            0       226b           226b
```

**Properties**

|表头|含义|
|---|---|
|health| 当前服务器健康状态：green(集群完整) yellow(单点正常、集群不完整) red(单点不正常)|
|status |索引打开、关闭状态|
|index| 索引名|
|uuid| 索引统一编号|
|pri |主分片数量|
|rep |副本数量|
|docs.count| 可用文档数量|
|docs.deleted| 文档删除状态（逻辑删除）|
|store.size |主分片和副分片整体占空间大小|
|pri.store.size |主分片占空间大小|


# 文档操作
## 创建文档

### Request

```bash
# [POST|PUT] http://ip:port/[索引名]/_doc
curl --location --request POST '192.168.3.70:9200/marhal/_doc' \
--header 'Content-Type: application/json' \
--data '{
 "title":"myCollection",
 "category":"test",
 "price":100.00
}
'
# [POST|PUT] http://ip:port/[索引名]/_create/[自定义主键]
curl --location --request PUT '192.168.3.70:9200/marhal/_create/0001' \
--header 'Content-Type: application/json' \
--data '{
 "title":"myCollection",
 "category":"test",
 "price":100.00
}
'

```

**Response**
```json
{
	"_index": "marhal",
	"_type": "_doc",
	"_id": "g03DL34BwRBFdkf7hkTH",
	"_version": 1,
	"result": "created",
	"_shards": {
		"total": 2,
		"successful": 1,
		"failed": 0
	},
	"_seq_no": 0,
	"_primary_term": 1
}
```

**NOTICE**
* `_create` 相同版本号重复请求会返回错误

## 主键获取文档
```bash
# [GET] http://ip:port/[索引名]/_doc/[主键]
curl --location --request GET '192.168.3.70:9200/marhal/_doc/001'
```
**Response**
* Status Success

```json
{
	"_index": "marhal",
	"_type": "_doc",
	"_id": "001",
	"_version": 8,
	"_seq_no": 8,
	"_primary_term": 1,
	"found": true,
	"_source": {
		"title": "myCollection",
		"category": "test",
		"price": 100
	}
}
```
* Status Error

```json
{
	"_index": "marhal",
	"_type": "_doc",
	"_id": "004",
	"found": false
}
```

## 获取所有文档
```bash
# [GET] http://ip:port/[索引名]/_search
curl --location --request GET '192.168.3.70:9200/marhal/_search'

# [GET] http://ip:port/[索引名]/_search
curl --location --request GET '192.168.3.70:9200/marhal/_search' \
--header 'Content-Type: application/json' \
--data '{
    "query" :{
        "match_all" :{
          
        }
    }
}'
```

**Response**
```json
{
	"took": 3,
	"timed_out": false,
	"_shards": {
		"total": 1,
		"successful": 1,
		"skipped": 0,
		"failed": 0
	},
	"hits": {
		"total": {
			"value": 3,
			"relation": "eq"
		},
		"max_score": 1,
		"hits": [
			{
				"_index": "marhal",
				"_type": "_doc",
				"_id": "g03DL34BwRBFdkf7hkTH",
				"_score": 1,
				"_source": {
					"title": "myCollection",
					"category": "test",
					"price": 100
				}
			},
			{
				"_index": "marhal",
				"_type": "_doc",
				"_id": "001",
				"_score": 1,
				"_source": {
					"title": "myCollection",
					"category": "test",
					"price": 100
				}
			},
			{
				"_index": "marhal",
				"_type": "_doc",
				"_id": "002",
				"_score": 1,
				"_source": {
					"title": "myCollection",
					"category": "test",
					"price": 100
				}
			}
		]
	}
}
```

## 更新文档

### 完全覆盖
```bash
# [PUT|POST] http://ip:port/[索引名]/_doc/[主键]
curl --location --request PUT '192.168.3.70:9200/marhal/_doc/001' \
--header 'Content-Type: application/json' \
--data '{
    "a":1,
    "b":2
}'
```

### 局部更新

```bash
curl --location --request POST '192.168.3.70:9200/marhal/_update/001' \
--header 'Content-Type: application/json' \
--data '{
    "doc" :{
        "a":2222222
    }
}'
````
**Response**
```json
{
	"_index": "marhal",
	"_type": "_doc",
	"_id": "001",
	"_version": 9,
	"result": "updated",
	"_shards": {
		"total": 2,
		"successful": 1,
		"failed": 0
	},
	"_seq_no": 10,
	"_primary_term": 1
}
```

## 删除文档
```bash
# [DELETE] http://ip:port/[索引名]/_doc/[主键]
curl --location --request PUT '192.168.3.70:9200/marhal/_doc/001'
```

**Response**
```json
{
	"_index": "marhal",
	"_type": "_doc",
	"_id": "g03DL34BwRBFdkf7hkTH",
	"_version": 2,
	"result": "deleted",
	"_shards": {
		"total": 2,
		"successful": 1,
		"failed": 0
	},
	"_seq_no": 18,
	"_primary_term": 1
}
```

## 条件查询
```bash
# [GET] http://ip:port/[索引名]/_search?q=[key]:[value]
curl --location --request GET '192.168.3.70:9200/marhal/_search?q=a:1'

# [GET] http://ip:port/[索引名]/_search
curl --location --request GET '192.168.3.70:9200/marhal/_search' \
--header 'Content-Type: application/json' \
--data '{
    "query" :{
        "match" :{
            "a" : 1
        }
    }
}'
```

## 分页查询
* `from = (pageNum - 1) * size`
```bash
# [GET] http://ip:port/[索引名]/_search
curl --location --request GET '192.168.3.70:9200/marhal/_search' \
--header 'Content-Type: application/json' \
--data '{
    "query" :{
        "match_all" :{
          
        },
        "from": 0,
        "size": 2
    }
}'
```

## 查询排序
```bash
curl --location --request GET '192.168.3.70:9200/marhal/_search' \
--header 'Content-Type: application/json' \
--data '{
    "query" :{
        "match_all" :{
          
        },
        "from": 0,
        "size": 2,
        "_source【获取的字段】" : ["a","b"],
        "sort【排序】" : {
          "a【key】" : {
            "order【排序规则】" : "desc|asc"
          }
        }
    }
}'

curl --location --request GET '192.168.3.70:9200/marhal/_search' \
--header 'User-Agent: Apipost client Runtime/+https://www.apipost.cn/' \
--header 'Content-Type: application/json' \
--data '{
    "query" :{
        "match" :{
            "a【key】" : "1【value】"
        },
        
    },
    "_source【查询指定字段】" : ["a"],
    "sort【排序】": [
        {
           "a【key】": {
               "order【排序规则】": "desc|asc"
           }
        }
    ],
    "from【页数】": 0,
    "size【条数】": 2
}'
```

