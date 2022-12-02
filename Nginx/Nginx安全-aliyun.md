# Nginx后端服务指定的Header隐藏状态服务配置
## 描述
* 隐藏Nginx后端服务X-Powered-By头

## 检查提示
--

### 加固建议
#### 隐藏Nginx后端服务指定Header的状态： 
* 1、打开`conf/nginx.conf`配置文件（或主配置文件中的inlude文件）； 
* 2、在`http`下配置`proxy_hide_header`项； 增加或修改为 `proxy_hide_header X-Powered-By`; `proxy_hide_header Server`;
* 操作时建议做好记录或备份



# Nginx的WEB访问日志记录状态服务配置
## 描述
* 应为每个核心站点启用access_log指令。默认情况下启用。

## 检查提示
--

### 加固建议
#### 开启Nginx的WEB访问日志记录： 
* 1、打开`conf/nginx.conf`配置文件（或主配置文件中的inlude文件）； 
* 2、在http下配置`access_log`项 `access_log logs/host.access.log main`; 
* 3、并在主配置文件，及主配置文件下的`include`文件中 删除off项或配置为适当值
* 操作时建议做好记录或备份


# 禁用或者重命名危险命令 入侵防范
## 描述
* Redis中线上使用`keys *`命令是非常危险的，应该禁用或者限制使用这些危险的命令，可降低Redis写入文件漏洞的入侵风险。

## 检查提示
--

## 加固建议
> 修改 redis.conf 文件，添加
> 
> rename-command FLUSHALL ""
> rename-command FLUSHDB  ""
> rename-command CONFIG   ""
> rename-command KEYS     ""
> rename-command SHUTDOWN ""
> rename-command DEL ""
> rename-command EVAL ""
> 然后重启redis。 重命名为`""` 代表禁用命令，如想保留命令，可以重命名为不可猜测的字符串，如： `rename-command FLUSHALL joYAPNXRPmcarcR4ZDgC`



# 禁用symbolic-links选项服务配置
## 描述
* 禁用符号链接以防止各种安全风险

## 检查提示
--

## 加固建议
* 编辑Mysql配置文件`<conf_path>/my.cnf`，在`[mysqld]` 段落中配置`symbolic-links=0`，5.6及以上版本应该配置为`skip_symbolic_links=yes`，并重启mysql服务。



# 确保没有用户配置了通配符主机名身份鉴别
## 描述
* 避免在主机名中只使用通配符，有助于限定可以连接数据库的客户端，否则服务就开放到了公网

## 检查提示
--

## 加固建议
* 执行SQL更新语句，为每个用户指定允许连接的`host`范围。
* 登录数据库，执行use mysql; ；
* 执行语句`select user,Host from user where Host='%'`;查看`HOST`为`通配符`的用户;
* 删除用户或者修改用户host字段，
	* 删除语句：`DROP USER 'user_name'@'%'`; 。
	* 更新语句：`update user set host = <new_host> where host = '%'`;。
* 执行SQL语句:
	* `OPTIMIZE TABLE user`;
	* `flush privileges`;


# 确保配置了`log-error`选项安全审计
## 描述
* 启用错误日志可以提高检测针对mysql和其他关键消息的恶意尝试的能力，例如，如果错误日志未启用，则连接错误可能会被忽略。

## 检查提示
--

## 加固建议
* 编辑`Mysql`配置文件`<conf_path>/my.cnf`，在`[mysqld_safe]` 段落中配置`log-error`参数，`<log_path>`代表存放日志文件路径，如：`/var/log/mysqld.log`，并重启`mysql`服务：
* `log-error=<log_path>`