# 一、语言特性
## 性状(trait)
* 性状是类的`部分实现`（即常量、属性和方法）
* 两个作用
  * 表名`类可以做什么`--像是接口
  * 提供`模块化实现`--像是类
* 可以让两个无关的PHP类具有类似的行为，把模块化的实现方式注入多个无关的类，能促进代码复用
* `DRY原则`
  * Don't repeat yourself（不要重复你自己，简称DRY）
  * 一次且仅一次（once and only once，简称OAOO）
  * 系统中的每一部分，都必须有一个单一的、明确的、权威的代表
  * 不要在多个地方重复编写相同的代码。如果需要修改遵守这个原则的编码，只需在一处修改，改动就能体现到其他地方
* PHP解释器在编译时会把性状复制粘贴到类的`定义体`中，但是不会处理这个操作引入的不兼容问题
* 性状在类的`定义体`引入
```php
<?php
trait MyTrait
{
    //性状的实现
    
}
```
```php
<?php
class MyClass
{
    use Mytrait;
    
    //类的实现
}
```

## 生成器(yield)

### 生成一个范围内的数值
* wrong

```php
<?php
function makeRandom(int $length = 100)
{
    $dataset = [];
    for ($i = 0; $i < $length; $i++) {
        $dataset[] = $i;
    }
    return $dataset;
}
```

* right

```php
<?php
function makeRandom(int $length = 100)
{
    for ($i = 0; $i < $length; $i++) {
        yield $i;
    }
}
```

### 利用生成器处理csv文件
```php
<?php
function getRows($file) 
{
    $handle = fopen($file);
    if ($handle == false) {
        throw new Exception();
    }
    
    while (feof($handle) == false) {
        yield fgetcsv($handle);
    }
    
    fclose($handle);
}

foreach (getRows($file) as $row) {
    print_r($row);
}

```
* 上述示例只会为CSV文件分配一次内存，而不会把4GB的文件都加载到内存
* 生成器是功能多样性和简洁性之间的折中方案
* 只能是向前的迭代器，这意味着不能使用生成器在数据集中执行后退、快进或查找操作，只能让生成器计算并产生下一个值
* 迭代大数据集或数列时适合使用，占的系统内存量极少

## 闭包和匿名函数

## 字节码缓存(Zend OPcache)
* --enable-opcache
* php.ini
  * zend_extension=/path/to/opcache.so
* 找到扩展所在目录
  * php-config --extension-dir
* 配置 [Documents](https://www.php.net/manual/zh/opcache.configuration.php)
  * opcache.validate_timestamps=1 //为0时,察觉不到php脚本的变化，必须手动清空Zend OPcache缓存的字节码
  * opcache.revalidate_freq=0 //validate_timestamps=1,revalidate_freq=0 启动自动重新验证缓存功能
  * opcache.memory_consumption=64
  * opcache.interned_strings_buffer=16
  * opcache.max_accelerated_files=4000
  * opcache.fast_shutdown=1

## 内置的Http服务器

* 启动

```bash
php -S localhost:4000
```

* 配置

```bash
php -S localhost:4000 -c /app/config/php.ini
```

* 判断
```text
php_sapi_name() == 'cli-server'
```

# 二、良好实践
## PSR
* [PSR-1](https://www.php-fig.org/psr/psr-1/) : 基本的代码风格
* [PSR-2](https://www.php-fig.org/psr/psr-2/) : 严格的代码风格(已废弃，新版为PSR-12)
  * [PHP_Code_Sniffer](http://bit.ly/phpsniffer)
* [PSR-3](https://www.php-fig.org/psr/psr-3/) : 日志记录接口
  * [monolog](https://packagist.org/packages/monolog/monolog)
* [PSR-4](https://www.php-fig.org/psr/psr-4/) : 自动加载
  * __autoload()
  * spl_autoload_register()
  * [Twig](https://packagist.org/packages/twig/twig)
* ...
* [PSR-18](https://www.php-fig.org/psr/psr-18/) ：HTTP 客户端

## 组件 Packagist
* [Packagist](https://packagist.org/)
* [Composer](https://getcomposer.org/)
  * composer require 安装并生成composer.lock
  * composer install 安装composer.lock中的版本
  * composer update 升级组件到最新版并更新composer.lock
  * 私有库
    * composer config (--global) xxx.org your-username your-password
    * auth.json

```json
{
  "http-basic": {
    "example.org": {
      "username": "",
      "password": ""
    }
  }
}
```

* 附：编写php命令行脚本
  * https://www.php.net/manual/zh/wrappers.php.php
  * https://www.php.net/manual/zh/reserved.variables.argc.php
  * https://www.php.net/manual/zh/reserved.variables.argv.php

## 过滤、验证、转义
* [htmlentities()](https://www.php.net/manual/zh/function.htmlentities.php) 特殊字符转HTML
  * htmlentities('', ENT_QUOTES, 'UTF-8')//转义单引号和双引号
* SQL 查询使用 PDO 预处理
* [filter_var()](https://www.php.net/manual/zh/function.filter-var.php) 使用特定的过滤器过滤一个变量
* [filter_input()](https://www.php.net/manual/zh/function.filter-input.php) 通过名称获取特定的外部变量，并且可以通过过滤器处理它
  * 组件
    * [aura/filter](https://packagist.org/packages/aura/filter)
    * [respect/validation](https://packagist.org/packages/respect/validation)
    * [symfony/validator](https://packagist.org/packages/symfony/validator)

## 密码
* [原生密码hash API](https://www.php.net/manual/zh/book.password.php)

## 日期、时间和时区
* 设置默认时区
  * php.ini
    * date.timezone='Asia/Shanghai'
  * php
    * date_default_timezone_set('Asia/Shanghai')
* 组件
  * [nesbot/carbon](https://packagist.org/packages/nesbot/carbon)

## PDO 扩展

## 字符编码
* php.ini
  * `default_charset="UTF-8"`
* php
  * `header('Content-Type: application/json;charset=utf-8')`
* html
  * `<meta charset="UTF-8">`
  
## 流
* [流封装协议](https://www.php.net/manual/zh/wrappers.php)
  * 函数
    * file_get_contents($url)
    * fopen()
    * fwrite()
    * fclose()
  * file://流封装协议
    * 隐式使用
      * fopen('/etc/hosts', 'rb')
    * 显示使用
      * fopen('file::///etc/hosts', 'rb')
  * php://流封装协议
    * php://stdin 
      * 只读PHP流，数据来自标准输入，接收命令行传入脚本的信息
    * php://stdout
      * 把数据写入当前的输出缓冲区
      * 只能写，无法读或寻址
    * php://memory
      * 从系统内存中读取数据，或者把数据写入系统内存
      * 可用内存有限
      * php://temp更安全
    * php://temp
      * 没有内存时，php把数据写入文件
  * 流上下文：定制流的行为
    * file_get_contents() 发送http post 请求
  * 流过滤器

```php
//file_get_contents() 发送http post 请求
$requestBody = '{"username":"Tom"}';
$context = stream_context_create(array(
    'http'=>array(
        'method' => 'POST',
        'header' => 'Content-Type: application/json;charset=utf-8;\r\n"Content-Length:"'. mb_strlen($requestBody),
        'content' => $requestBody
    )
));
set_exception_handler()
$response = file_get_contents('xxx.com', false, $context);
```

## 错误和异常

### 异常
* [PHP标准库](https://www.php.net/manual/zh/book.spl.php)
* [PHP标准库 exceptions](https://www.php.net/manual/zh/spl.exceptions.php)
* 异常处理程序

```php
set_exception_handler(function (Exception $exception) {
    //记录并处理异常
});

//还原成之前的异常程序
restore_exception_handler();
```
### 错误
#### Dev
* 显示错误
  * display_startup_errors=On
  * display_errors=On
* 报告所有错误
  * error_reporting=1
* 记录错误
  * log_errors=On

#### Pro
* 不显示错误
  * display_startup_errors=Off
  * display_errors=Off
* 除了注意事项之外，报告其他所有错误
  * error_reporting=E_LL& ~E_NOTICE
* 记录错误
  * log_errors=On

#### 错误处理
```php
/**
 * $errno 错误等级
 * $errstr 错误消息
 * $errfile 发生错误的文件名
 * $errline 发生错误的文件所在行
 */
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    //处理错误
});

restore_error_handler();
```
# 三、部署、测试、调优

## PHP-FPM

### 全局配置
* [php-fpm.conf](http://php.net/manual/zh/install.fpm.configuration.php)
* emergency_restart_threshold=10
  * 指在指定一段时间内，如果失效的php-fpm子进程超过这个值，php-fpm主进程就优雅重启
* emergency_restart_interval=1m
  * 设定 emergency_restart_threshold 设置采用的时间跨度

### 配置进程池
* www.conf
* `user = www`
  * 拥有这个php-fpm进程池中子进程的系统用户
* `group = www`
  * 拥有这个php-fpm进程池中子进程的系统用户组
* `listen = 127.0.0.1:9000`
  * php-fpm 进程池监听的IP地址和端口号，php-fpm只接受nginx从这里传入的请求
  * 可用格式为：'ip:port'，'port'，'/path/to/unix/socket'。
  * 每个进程池都需要设置
* `listen.allow_clients = 127.0.0.1`
  * 可以像这个php-fpm进程池发送请求的ip地址(一个或多个)
  * 仅对 TCP 监听起作用。
  * 每个地址是用逗号分隔，如果没有设置或者为空，则允许任何服务器请求连接。默认值：any
* `pm`
  * 设置进程管理器如何管理子进程。可用值：static，ondemand，dynamic
  * static - 子进程的数量是固定的（pm.max_children）
  * ondemand - 进程在有需求时才产生（当请求时才启动。与 dynamic 相反，在服务启动时 pm.start_servers 就启动了
  * dynamic - 子进程的数量在下面配置的基础上动态设置：pm.max_children，pm.start_servers，pm.min_spare_servers，pm.max_spare_servers。
* `pm.max_children = 51`
  * 设定任何时间点php-fpm 进程池中最多能有多少个进程
  * php-fpm进程池分配的可用内存(512M)/每个进程使用(10M)
* `pm.start_servers=3`
  * 设置启动时创建的php-fpm进程池中立即可用的子进程数目。仅在 pm 设置为 dynamic 时使用
* `pm.min_spare_servers=2`
  * php应用空闲时php-fpm进程池中可以存在的进程数量最小值
  * 一般与start_servers值一样
* `pm.max_spare_servers=4`
  * php应用空闲时php-fpm进程池中可以存在的进程数量最小值
  * 一般比start_servers值大一点
* `pm.max_requests=1000`
  * 回收进程之前，php-fpm进程池中各个进程最多能处理的http请求数量
  * 有助于避免php扩展或库因编写拙劣导致不断泄露内存
  * 设置为 '0' 则一直接受请求，等同于 PHP_FCGI_MAX_REQUESTS 环境变量。默认值：0
* `slowlog=/path/to/slow.log`
  * 绝对路径
  * 记录处理时间超过n秒的http请求信息
* `request_slowlog_timeout=5s`
  * 当前http请求处理时间超过指定值，将回溯信息写入slowlog设定的日志文件

## 调优
* [PHP Iniscan 工具](https://github.com/psecio/iniscan)
### **memroy_limit**
* 默认128M
* 如果运行微型PHP应用可以降低值，节省系统资源
* 设置依据
  * 一共能分配给php多少内存？
  * 单个php进程平均消耗多少内存？
    * top实时统计
    * memory_get_peak_usage()
  * 能负担得起多少个php-fpm进程？
    * 分配了512M，单个进程平均消耗15M，可以负担起34个php-fpm进程
  * 有足够的系统资源吗
    * 压测工具
      * [Apache Bench](https://httpd.apache.org/docs/2.2/programs/ab.html)
      * [Seige](http://www.joedog.org/)

### **Zend OPcache**
* `opcache.memory_consumption=64`
  * 为操作码缓存分配的内存量，单位：MB
* `opcache.interned_strings_buffer=16`
  * 用来驻留字符串的内存量，单位：MB
  * 默认4MB
* `opcache.max_accelerated_files=4000`
  * 操作码缓存中最多能存储多少个php脚本
  * 200~100000
  * 这个值一定要比php的文件数大
* `opcache.validate.timestamps=1`
  * 1:经过一段时间后，php会检查一次php脚本的内容是否有变化，时间间隔由opcache.revalidate_freq决定
  * 0：不检查
* `opcache.revalidate_freq=0`
  * 设置php多久检查一次php脚本的内容是否有变化
* `opcache.fast_shutdown=1 `
  * 能让操作码使用更快的停机步骤，把对象析构和内存释放交给Zend Engine的内存管理器完成

### **最长执行时间**


## 测试

### 单元测试 PHPUnit

### 测试驱动开发 TDD

### 行为驱动开发 BDD
* SpecBDD
* StoryBDD