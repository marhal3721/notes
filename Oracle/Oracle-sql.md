* [创建用户](#createUser)
* [建表](#createTable)
* [查询所有数据库](#allDatabase)
* [导出](#exp)
* [导入](#imp)
* [表空间扩充](#addTableSapce)


## <a id="createUser">创建用户</a>
	创建用户前必须要先创建临时表空间和数据库表空间两个表空间，否则用系统默认的表空间，会引起其他问题

* 1.创建临时表空间 -- 不需要引号
```sql
# create temporary tablespace '临时表空间名' tempfile '临时表空间位置' size 临时表空间大小 autoextend on next 100m maxsize 10240m extent management local;

SQL> create temporary tablespace MH_TMP tempfile '/u01/app/oracle/oradata/XE/MH_temp.dbf' size 1024m autoextend on next 100m maxsize 10240m extent management local;

```
* 2.创建数据表空间 -- 不需要引号
```sql
# create tablespace '数据表空间名' logging datafile '数据表空间位置' size 1024m autoextend on next 100m maxsize 10240m extent management local;

SQL> create tablespace MH logging datafile '/u01/app/oracle/oradata/XE/MH.dbf' size 1024m autoextend on next 100m maxsize 10240m extent management local;
```
* 3.创建数据库用户并指定表空间
```sql
create user 用户名 identified by 用户密码 default tablespace 所指定的表空间名 temporary tablespace 临时表空间名; 
```

* 删除用户
```sql
# 如果用户拥有数据表，则不能直接删除，要用上关键字cascade
DROP USER MAHAO CASCADE;
```

* 查看当前登录用户
```sql
SQL> select * from v$version;
SQL> show user
#  USER is "MAHAO"
```

## <a id="allDatabase">查询所有数据库</a>
```sql
# 查询表空间(需要一定权限)
select * from v$tablespace;

# 查询当前数据库中所有表名
select * from user_tables;

# 查询指定表中的所有字段名,表名要全大写
select column_name from user_tab_columns where table_name = 'STUDENTS';

# 查询指定表中的所有字段名和字段类型,表名要全大写
select column_name, data_type from user_tab_columns where table_name = 'STUDENTS';

# 查询所有用户的表,视图等
select * from all_tab_comments;

# 查询本用户的表,视图等
select * from user_tab_comments;

# 查询所有用户的表的列名和注释
select * from all_col_comments;

# 查询本用户的表的列名和注释
select * from user_col_comments;

# 查询所有用户的表的列名等信息
select * from all_tab_columns;

# 查询本用户的表的列名等信息
select * from user_tab_columns;

# 查询某用户的表的列名等信息
select * from all_tab_comments WHERE OWNER = 'MARHAL';
```


## <a id="createTable">建表demo</a>
```sql
create table MARHAL.DM_GY_USER
(
  user_id       CHAR(32),
  username      VARCHAR2(100) not null,
  password      VARCHAR2(255) not null,
  email         VARCHAR2(100),
  active        CHAR(1) not null,
  register_time DATE,
  emp_num       VARCHAR2(50),
  sex           CHAR(1),
  xsxh          NUMBER(8),
  cellphone     VARCHAR2(50)
)
tablespace USERS  --表放在USERS表空间
  pctfree 10 --保留10%空间给更新该块数据使用
　--PCTFREE：默认是10，表示当数据块的可用空间低于10%后，当一个block剩余空间低于10%，就不可以被insert了，只能被用于update；
		-- 即：当使用一个block时，在达到pctfree之前，该block是一直可以被插入的，这个时候处在上升期。
　--PCTUSED：是指当块里的数据低于多少百分比时，又可以重新被insert，一般默认是40,即40%，
		-- 即：当数据低于40%时，又可以写入新的数据，这个时候处在下降期。
  --假设你一个块可以存放100个数据，而且PCTFREE 是10，PCTUSED是40，
  		-- 则：不断的向块中插入数据，如果当存放到90个时，就不能存放新的数据，这是受pctfree来控制，预留的空间是给UPDATE用的。
  --当你删除一个数据后，再想插入个新数据行不行？不行，必须是删除41个，即低于40个以后才能插入新的数据的，这是受pctused来控制的
  initrans 1 --初始化事物槽的个数
　--每个block都有一个块首部。
	-- 这个块首部中有一个事务表(Interested Transaction List)。
	-- 事务表中会建立一些条目来描述哪些事务将块上的哪些行/元素锁定。
	-- 这个事务表的初始大小由对象的INITRANS 设置指定
　--(Interested Transaction List)事物槽列表是Oracle数据块内部的一个组成部分，
	-- 它是由一系列的ITS(Interested Transaction Slot,事物槽)组成,
	-- 其初始的ITL Slot数量由INITRANS决定的，
	-- 如果有足够的剩余空间，oracle也会根据需要动态的分配这些slot,直到受到空间限制或者达到MAXTRANS,10g以后MAXTRANS被废弃,默认为255。
　--事物槽列表用来来记录该块所有发生的事务，
	-- 一个itl可以看作是一个记录，在一个时间，可以记录一个事务（包括提交或者未提交事务）。
	-- 当然，如果这个事务已经提交，那么这个itl的位置就可以被反复使用了，因为itl类似记录，所以，有的时候也叫itl槽位。

  maxtrans 255 --最大事务槽的个数
  storage --存储参数
  (
    initial 64K --区段一次扩展64k
    next 1M
    minextents 1 --最小区段数
    maxextents unlimited --最大区段无限制
  )
--数据库的逻辑结构如下：数据库是由一系列表空间(tablespace)组成，表空间由若干段(segment)组成，段由若干区(extent)组成，区由若干块(block)组成
--当在表空间中创建表时，系统先分配一个初始空间，这个空间大小由initial这个参数决定，此处为64KB,
	-- minextents 表示建好表后至少要分配几个区，这里是1个
	-- maxextents 表示表空间最多能分配几个区，这里是无限制
nologging;
-- Add comments to the table 
comment on table MARHAL.DM_GY_USER
  is '用户表';
-- Add comments to the columns 
comment on column MARHAL.DM_GY_USER.user_id
  is '用户id';
comment on column MARHAL.DM_GY_USER.username
  is '姓名';
comment on column MARHAL.DM_GY_USER.password
  is '密码';
comment on column MARHAL.DM_GY_USER.email
  is '邮箱';
comment on column MARHAL.DM_GY_USER.active
  is '状态|1启动0禁用2注销';
comment on column MARHAL.DM_GY_USER.register_time
  is '注册事件';
comment on column MARHAL.DM_GY_USER.emp_num
  is '员工编号';
comment on column MARHAL.DM_GY_USER.sex
  is '性别||0:男 1：女';
comment on column MARHAL.DM_GY_USER.telephone
  is '固定电话号码';
comment on column MARHAL.DM_GY_USER.xsxh
  is '显示序号';
```

## <a id="exp">导出</a>
```bash
# 帮助
root@oracle:/# exp help=y

# 将整个数据库内容导出，但是操作时需要有特殊权限
## exp 用户名/密码 buffer=32000 file=导出的目录 full=y
root@oracle:/# exp marhal/123456 buffer=32000 file=/1.dmp log=oradb.log full=y compress=y direct=y full=y 
## ①　Dmp格式：.dmp是二进制文件，可跨平台，还能包含权限，效率好， 
## ②　Sql格式：.sql格式的文件，可用文本编辑器查看，通用性比较好，效率不如第一种，适合小数据量导入导出。尤其注意的是表中不能有大字段 （blob,clob,long），如果有，会报错 
## ③　Pde格式：.pde格式的文件，.pde为PL/SQL Developer自有的文件格式，只能用PL/SQL Developer工具导入导出，不能用文本编辑器查看

# 将指定用户的所有对象进行导出
root@oracle:/# exp marhal/123456 buffer=32000 file=/1.dmp log=oradb.log full=y compress=y direct=y owner=marhal

# 将用户的所有表数据进行导出
root@oracle:/# exp marhal/123456 buffer=32000 file=/1.dmp log=oradb.log full=y compress=y direct=y owner=marhal tables=(marhal)
## COMPRESS参数将在导出的同时合并碎块，尽量把数据压缩到initial的EXTENT里，默认是N。建议使用
## DIRECT参数将告诉EXP直接读取数据，而不像传统的EXP那样，使用SELECT来读取表中的数据，这样就减少了SQL语句处理过程。建议使用

```
## <a id="imp">导入</a>
```bash
# 帮助
root@oracle:/# imp help=y

root@oracle:/# imp 用户名/密码 file=dmp文件路径 log=输出日志路径full=y ignore=y; 

```
## <a id="addTableSapce">表空间扩充</a>

### 查看当前数据库的表空间情况
```sql
select a.tablespace_name,a.bytes/1024/1024 "sum MB",  (a.bytes-b.bytes)/1024/1024 "used MB",b.bytes/1024/1024 "free MB",round (((a.bytes-b.bytes)/a.bytes)*100,2) "used%" from (select tablespace_name,sum(bytes) bytes from dba_data_files group by tablespace_name) a, (select tablespace_name,sum(bytes) bytes,max (bytes) largest from dba_free_space group by tablespace_name)b where a.tablespace_name=b.tablespace_name order by ((a.bytes-b.bytes)/a.bytes) desc;
```
```text
# TABLESPACE_NAME 		   sum MB    used MB	free MB      used%
# ------------------------------ ---------- ---------- ---------- ----------
# SYSTEM				      360   352.6875	 7.3125      97.97
# SYSAUX				      640	605	         35          94.53
# UNDOTBS1			       	  25    19.6875	     5.3125      78.75
# USERS				      	  100   2.6875	     97.3125     2.69
```

### 查看表空间中数据文件存放的位置
```sql
select tablespace_name, file_id, file_name, round(bytes/(1024*1024),0) total_space from dba_data_files order by tablespace_name;
```

### 表空间扩充
#### 方法1.直接增大表空间的大小
```sql
alter database datafile '/u01/app/oracle/oradata/XE/undotbs1.dbf' resize 4000m
```

#### 方法2.增加数据文件的个数
```sql
alter tablespace SPS_DATA add datafile '/u01/app/oracle/oradata/XE/undotbs2.dbf' size 2000m 
```

#### 方法3.设置表空间自动扩展
```sql
alter database datafile '/u01/app/oracle/oradata/XE/undotbs2.dbf' autoextend on next 100m maxsize 10000m
```

#### 方法4.结合使用,当不确定导入文件最终大小时
```sql
alter tablespace SPS_DATA add datafile '/u01/app/oracle/oradata/XE/undotbs2.dbf' size 2000m autoextend on next 200M maxsize 12000M;
```
