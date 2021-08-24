# docker 安装 Oracle

```bash

# 搜索镜像
docker search docker-oracle-xe-11g

# 安装镜像
docker pull deepdiver/docker-oracle-xe-11g

# 安装oracle容器
docker run -d -p 1521:1521 --name oracle11g deepdiver/docker-oracle-xe-11g
# 或
docker run -h "oracle" --name "oracle" -d -p 49160:22 -p 49161:1521 -p 49162:8080 deepdiver/docker-oracle-xe-11g

# 进入容器
docker exec -it oracle11g bash

# 通过sqlplus进入Oracle
root@727414867a10:/# sqlplus system/oracle

# 查看数据库用户名和密码
SQL> select username,password from dba_users;
# USERNAME		       PASSWORD
# ------------------------------ ------------------------------
# SYS
# ANONYMOUS
# AUTOTEST
# SYSTEM
# APEX_PUBLIC_USER
# APEX_040000
# OUTLN
# XS$NULL
# FLOWS_FILES
# MDSYS
# CTXSYS
# 
# USERNAME		       PASSWORD
# ------------------------------ ------------------------------
# XDB
# HR
# 
# 13 rows selected.

# 创建用户MARHAL密码为123456
SQL> CREATE USER MARHAL IDENTIFIED BY 123456;

# 查看所有用户
SQL> SELECT * FROM ALL_USERS;

# 赋权
SQL> GRANT CONNECT, RESOURCE, DBA TO MARHAL;
# ******* DBA: 拥有全部特权，是系统最高权限，只有DBA才可以创建数据库结构。
# ******* RESOURCE:拥有Resource权限的用户只可以创建实体，不可以创建数据库结构。
# ******* CONNECT:拥有Connect权限的用户只可以登录Oracle，不可以创建实体，不可以创建数据库结构。
# ******* 对于普通用户：授予connect, resource权限。
# ******* 对于DBA管理用户：授予connect，resource, dba权限。
# ******* 系统权限只能由DBA用户授出：sys, system(最开始只能是这两个用户)
# 退出
SQL> exit;

# 登录
root@727414867a10:/# sqlplus
# Enter user-name: MARHAL
# Enter password: 
# 
# Connected to:
# Oracle Database 11g Express Edition Release 11.2.0.2.0 - 64bit Production
# 
# SQL> 
```

# Navicat 连接
<img src='./img/client.png' />

```sql
# 建表
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