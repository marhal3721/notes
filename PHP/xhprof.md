## 环境
* ubuntu20.04
* php7.4

## 安装

```bash
sudo apt-get install php-pear
sudo apt-get install php-dev
sudo pecl channel-update pecl.php.net
sudo pecl install xhprof
#sudo vim /etc/php/7.4/cli/php.ini
#sudo vim /etc/php/7.4/fpm/php.ini
# 增加 extension=xhprof.so
# 增加 xhprof.output_dir=/var/www/html/xhprof/output_dir

sudo vim /etc/php/7.4/mods-available/xhprof.ini
sudo ln -s /etc/php/7.4/mods-available/xhprof.ini /etc/php/7.4/fpm/conf.d/20-xhprof.ini
sudo ln -s /etc/php/7.4/mods-available/xhprof.ini /etc/php/7.4/cli/conf.d/20-xhprof.ini

# 检验
php-fpm7.4 -m | grep xhprof
php -m | grep xhprof
```

## 配置
```bash
sudo cp -r /tmp/pear/download/xhprof-2.3.5/xhprof_html /var/www/html/xhprof/
sudo cp -r /tmp/pear/download/xhprof-2.3.5/xhprof_lib /var/www/html/xhprof/
sudo vim /var/www/html/xhprof/auto_prepend_file.php
```
```php
//根据参数控制是否开启xhprof
if (!empty($_GET['xhprof'])) {
    //开启xhprof
    xhprof_enable(XHPROF_FLAGS_NO_BUILTINS | XHPROF_FLAGS_CPU | XHPROF_FLAGS_MEMORY);
    //在程序结束后收集数据
    register_shutdown_function(function() {
        //保存xhprof数据
        if (function_exists("xhprof_disable")) {
            $xhprof_data = xhprof_disable();

            define("DEBUG_LIB", "/var/www/html/xhprof/xhprof_lib"); //根据你存放的目录修改
            include_once DEBUG_LIB . "/utils/xhprof_lib.php";
            include_once DEBUG_LIB . "/utils/xhprof_runs.php";
            $xhprof_runs = new XHProfRuns_Default();

            $uri = $_GET['xhprof'].'-'.$_SERVER['HTTP_HOST'].$_SERVER['PATH_INFO'];
            $uri = str_replace(['/', '.', '\\', '|'], '_', $uri);
            $xhprof_runs->save_run($xhprof_data, $uri);
        }
    });
}
```
```bash
sudo vim /etc/php/7.4/fpm/php.ini
sudo vim /etc/php/7.4/cli/php.ini
# 每次请求执行之前php-fpm都先加载并运行此auto_append_file配置的文件文件
# auto_append_file = /var/www/html/xhprof/auto_prepend_file.php
```

* nginx

```bash
sudo vim /etc/nginx/sites-available/xhprof
sudo ln -s /etc/nginx/sites-available/xhprof /etc/nginx/sites-available/
```
```conf
server {
        listen 8888 default_server;
        listen [::]:8888 default_server;
        root /var/www/html/xhprof/xhprof_html;
        index index.html index.php;
        server_name xhprof.local.com localhost;
        location / {
            try_files $uri $uri/ =404;
        }
        location ~ \.php$ {
            include snippets/fastcgi-php.conf;
            fastcgi_split_path_info  ^((?U).+\.php)(/?.+)$;
            fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
            fastcgi_param  PATH_INFO  $fastcgi_path_info;
            fastcgi_param  PATH_TRANSLATED  $document_root$fastcgi_path_info;
            include        fastcgi_params;
            fastcgi_pass unix:/run/php/php7.4-fpm.sock;
        }        
}
```

## 请求

* index.php

```php
<?php

for ($i=0; $i<=9; $i++) {
    var_dump($i);
} 
```

* curl 127.0.0.1/index.php?xhprof=1
* 此时在`/var/www/html/xhprof/output_dir` 下会产生 以 `xhprof`结尾的文件
* 用 刚才配置的访问 http://172.16.0.245:8888/
* 输出

```text
No XHProf runs specified in the URL.
Existing runs:
6216f78b5cd24.1-172_16_0_245:18081.xhprof 2022-02-24 03:12:11
```

## 分析工具
```bash
sudo apt install graphviz
```






























## 错误

### php notice

```text
Notice: Trying to access array offset on value of type bool in PEAR/REST.php on line 187
PHP Notice:  Trying to access array offset on value of type bool in /usr/share/php/PEAR/REST.php on line 187
```

```bash
sudo vim /usr/share/php/PEAR/REST.php
```
```php
- 187         if (time() - $cacheid['age'] < $cachettl) {
+ 187         if (time() - intval($cacheid['age']) < $cachettl) {
188             return $this->getCache($url);
189         }
```
