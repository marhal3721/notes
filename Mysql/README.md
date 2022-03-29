- ## [mysql](#mysql)
    - [更改字段非空](#更改字段非空)
    - [删除/增加主键](#删除/增加主键)
    - [设置主键自增](#设置主键自增)
    - [添加字段](#添加字段)
    - [添加索引](#添加索引)
    - [导入备份数据库](#导入备份数据库)
    - [设置最允许导入值](#设置最允许导入值)
    - [导出](#导出)
    - [全文索引](#全文索引)
    - [查看数据库中那些表有主键](#查看数据库中那些表有主键)
    - [查看数据库中那些表没有主键](#查看数据库中那些表没有主键)
    - [查看库容量](#查看库容量)
    - [不连续索引](#不连续索引)


## <a id="mysql">mysql</a>

* <a id="更改字段非空">更改字段非空</a>
```sql
ALTER TABLE `tableNname` ALTER COLUMN `columnName` int(11) NOT NULL;
```
* <a id="删除/增加主键">删除/增加主键</a>
```sql
ALTER TABLE `tableNname` DROP CONSTRAINT PK_name(主键名字);
```
```sql
ALTER TABLE `tableNname` ADD CONSTRAINT PK_name primary key(`columnName`);
```
* <a id="设置主键自增">设置主键自增</a>
```sql
ALTER TABLE `tableNname` CHANGE `columnName` `columnName` INT(11) AUTO_INCREMENT;
```
* <a id="添加字段">添加字段</a>
```sql
ALTER TABLE `tableNname` ADD `columnName` tinyint(1) NOT NULL DEFULT 1;
```

* <a id="添加索引">添加索引</a>
```sql
ALTER TABLE `tableNname` ADD INDEX idx_name ( `columnName` )
```

* <a id="导入备份数据库">导入备份数据库</a>
```sql
source /home/abc/abc.sql;
```

* <a id="设置最允许导入值">设置最允许导入值</a>
```sql
show global variables like 'max_allowed_packet';
set global max_allowed_packet = 52428800(1024*1024*50)
```

* <a id="导出">导出命令 多个表用空格断开</a>
```bash
mysqldump -uroot -p  dbname tablename1 tablename2 > tablename.sql

```

```bash
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

```sql
CREATE FULLTXT INDEX ft_idx_name ON `tableNname`(`columnName`);
```

* 全文索引使用 ngram 解释器
    * 查找字符长度受ngram_token_size影响

```sql
CREATE FULLTXT INDEX ft_idx_name ON `tableNname`(`columnName`) WITH PARSER NGRAM;
```

* 全文索引查询语法

```sql
SELECT * FROM comments WHERE MATCH (contents) AGAINST ('+47 +90' IN BOOLEAN MODE);
SELECT * FROM comments WHERE MATCH (contents) AGAINST ('47 90' IN NATURAL LANGUAGE MODE);
```

* <a id="查看数据库中那些表有主键">查看数据库中那些表有主键</a>

```sql
select t1.table_schema,t1.table_name from information_schema.tables t1 
left outer join
information_schema.TABLE_CONSTRAINTS t2   
on t1.table_schema = t2.TABLE_SCHEMA  and t1.table_name = t2.TABLE_NAME  and t2.CONSTRAINT_NAME in
('PRIMARY') 
where t2.table_name is not null and t1.TABLE_SCHEMA not in ('information_schema','performance_schema','test','mysql', 'sys');
```

* <a id="查看数据库中那些表没有主键">查看数据库中那些表没有主键</a>
```sql
select t1.table_schema,t1.table_name from information_schema.tables t1 
left outer join
information_schema.TABLE_CONSTRAINTS t2   
on t1.table_schema = t2.TABLE_SCHEMA  and t1.table_name = t2.TABLE_NAME  and t2.CONSTRAINT_NAME in
('PRIMARY') 
where t2.table_name is null and t1.TABLE_SCHEMA not in ('information_schema','performance_schema','test','mysql', 'sys');
```
```sql
SELECT
table_name
FROM
    information_schema. TABLES
WHERE
    table_schema = 'test'
AND TABLE_NAME NOT IN (
    SELECT
        table_name
    FROM
        information_schema.table_constraints t
    JOIN information_schema.key_column_usage k USING (
        constraint_name,
        table_schema,
        table_name
    )
    WHERE
        t.constraint_type = 'PRIMARY KEY'
    AND t.table_schema = 'your database name'
);
```

* <a id="查看库容量">查看库容量</a>

* 1.查看所有数据库的容量

```sql
select
table_schema as '数据库',
sum(table_rows) as '记录数',
sum(truncate(data_length/1024/1024, 2)) as '数据容量(MB)',
sum(truncate(index_length/1024/1024, 2)) as '索引容量(MB)'
from information_schema.tables
group by table_schema
order by sum(data_length) desc, sum(index_length) desc;

```

* 2.查看指定数据库、表的容量

```sql
select
table_schema as '数据库',
table_name as '表名',
table_rows as '记录数',
truncate(data_length/1024/1024, 2) as '数据容量(MB)',
truncate(index_length/1024/1024, 2) as '索引容量(MB)'
from information_schema.tables
where table_schema='数据库名' and table_name='表名'
order by data_length desc, index_length desc;
```

* 3.查看指定数据库的容量

```sql
select
table_schema as '数据库',
sum(table_rows) as '记录数',
sum(truncate(data_length/1024/1024, 2)) as '数据容量(MB)',
sum(truncate(index_length/1024/1024, 2)) as '索引容量(MB)'
from information_schema.tables
where table_schema='数据库名';

```

* 4.查看所有库各表的容量

```sql
select
table_schema as '数据库',
table_name as '表名',
table_rows as '记录数',
truncate(data_length/1024/1024, 2) as '数据容量(MB)',
truncate(index_length/1024/1024, 2) as '索引容量(MB)'
from information_schema.tables
order by data_length desc, index_length desc;
```

* <a id="不连续索引">不连续索引</a>
```sql
select id from (select id from `company_user` order by id asc) t where not exists (select 1 from `company_user` where id=t.id-1)
```