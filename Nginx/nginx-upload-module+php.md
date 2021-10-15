
# Nginx模块上传

## 配置

### 环境
* Mac
* Nginx1.21.1
* php8.0.9(NTS)


### 相关文档
* [nginx-upload-module](https://www.nginx.com/resources/wiki/modules/upload/#upload-set-form-field)
* [vkholodkov/nginx-upload-module](https://github.com/vkholodkov/nginx-upload-module/tree/2.3.0)
* [nginx-download](http://nginx.org/download/)


### 1.准备
```bash
# upload_store散列存储路径
cd /var/www/html/ \
&& mkdir -p tmp/0 tmp/1 tmp/2 tmp/3 tmp/4 tmp/5 tmp/6 tmp/7 tmp/8 tmp/9 \
&& chmod -R 777  /var/www/html/tmp

# php文件存储位置
cd /var/www/html/ mkdir upload && chmod -R 777  /var/www/html/upload

# 查看nginx原编译
nginx -V

# 重新编译nginx 
cd /usr/local/Cellar/nginx/1.21.1/src && https://github.com/vkholodkov/nginx-upload-module.git
cd nginx源码包目录
./configure --prefix=/usr/local/Cellar/nginx/1.21.1 \
--sbin-path=/usr/local/Cellar/nginx/1.21.1/bin/nginx (忽略中间h很多参数) \
--add-module=/usr/local/Cellar/nginx/1.21.1/src/nginx-upload-module-2.3.0
make
make install #会覆盖原配置
```

### 2.nginx配置文件
```
# upload_progress 模块参数
# 语法：upload_progress <zone_name> <zone_size>;
# 上下文：http
# 作用：声名nginx server使用upload progress module，引用名为zone_name，并分配zone_size bytes的空间存放上传状态信息
upload_progress proxied 2m;

server {
    listen       8889;
    server_name  127.0.0.1;

    client_max_body_size 400m;
    client_body_buffer_size 1024k;

    real_ip_header X-Real-IP;

    fastcgi_intercept_errors on;
    error_page 500 502 503 504  /server/500.html;
    error_page 404  /server/404.html;
    
    root /var/www/html;

    location = /server/500.html {
        internal;
    }

    location = /server/404.html {
        internal;
    }

    location = /favicon.ico {
        log_not_found off;
        access_log off;
    }

    location ~.*\.(png|jpg|jpeg|bmp|zip|rar|gz|doc|docx|xlsx|xsl|pdf|ppt|txt|mp3|wav|wmv|mp4|flv|avi|gif|mpeg|m3u8|rm|rmvb)$ {
        access_log   off;
        root /var/www/html/attachment/;
        autoindex on;
        if ($request_uri ~ \.(jpeg|png|jpg|bmp)\?x-qxy-process=image){
            proxy_pass http://$host:$server_port/api/outputImage?$query_string&resource=$uri;
        }
    }

    location / {
        index  index.html index.php;
        try_files $uri $uri index.php?$args;
    }

    location ~ /\. {
        deny all;
    }

    location /auth {
        internal;
        proxy_pass_request_body off;
        proxy_set_header Content-Length "";
        proxy_set_header X-Original-URI $request_uri;
        proxy_pass http://$host:$server_port/api/auth?$query_string;
    }

    location ^~ /progress {
        # 语法：report_uploads <zone_name>
        # 上下文：location
        # 作用：允许一个location响应上传状态，响应内容默认为一个javascript的new object语句对象，有四种状态响应：
        report_uploads proxied;
    }

    location /upload {

        # 先校验权限
        auth_request /auth;
        #auth_request_set $user $upstream_http_x_forwarded_user;
        #proxy_set_header X-Forwarded-User $user;

        #后续处理的后端地址。文件中的字段将被分离和取代，包含必要的信息处理上传文件。
        upload_pass   @test; 
        # 打开开关，意思就是把前端脚本请求的参数会传给后端的脚本语言，比如：
        upload_pass_args on;
        # 指定上传文件存放地址(目录)。目录可以散列，在这种情况下，在nginx启动前，所有的子目录必须存在。
        upload_store /var/www/html/tmp 1; 
        # 上传文件的访问权限，user:r是指用户可读
        upload_store_access user:r; 
        # 上传软限制 超过此大小的文件会被忽略
        upload_max_file_size 30m;


        # 这里写入http报头，pass到后台页面后能获取这里set的报头字段
        upload_set_form_field $upload_field_name.name "$upload_file_name";# HTML表单内的file字段名
        upload_set_form_field $upload_field_name.content_type "$upload_content_type"; # 文件MIME类型
        upload_set_form_field $upload_field_name.path "$upload_tmp_path";# 临时文件绝对路径
        upload_set_form_field $upload_field_name.filename "$upload_file_name";# 不带路径的原始文件名

        # Upload模块自动生成的一些信息，除去$upload_file_size, $upload_file_number这两个变量外，会耗费额外的cpu和资源
        upload_aggregate_form_field "$upload_field_name.md5" "$upload_file_md5";# 上传文件的MD5哈希值(小写)
        upload_aggregate_form_field "$upload_field_name.md5_uc" "$upload_file_md5_uc";# 上传文件的MD5哈希值(大写)
        upload_aggregate_form_field "$upload_field_name.size" "$upload_file_size";# 上传文件的大小(bytes)
        upload_aggregate_form_field "$upload_field_name.number" "$upload_file_number";# 上传文件的序号(从1开始)
        upload_aggregate_form_field "$upload_field_name.crc32" "$upload_file_crc32";# 上传文件的CRC32校验码(十六进制)
        upload_aggregate_form_field "$upload_field_name.sha1" "$upload_file_sha1";# 上传文件的SHA1哈希值(小写)
        upload_aggregate_form_field "$upload_field_name.sha1_uc" "$upload_file_sha1_uc";# 上传文件的SHA1哈希值(大写)
        upload_aggregate_form_field "$upload_field_name.sha256" "$upload_file_sha256";# 上传文件的SHA256哈希值(小写)
        upload_aggregate_form_field "$upload_field_name.sha256_uc" "upload_file_sha256_uc";# 上传文件的SHA256哈希值(大写)
        upload_aggregate_form_field "$upload_field_name.sha512" "$upload_file_sha512";# 上传文件的SHA512哈希值(小写)
        upload_aggregate_form_field "$upload_field_name.sha512_uc" "upload_file_sha512_uc";# 上传文件的SHA512哈希值(大写)

        # 允许的字段，允许全部可以 "^.*$"
        # 默认情况下,本模块并不会将file类型字段以外的input字段发送给下一阶段处理，,除非在此显式声明字段名称(非类型)
        upload_pass_form_field "^submit$|^description$";
        # 其等同于
        # pload_pass_form_field "submit";
        # upload_pass_form_field "description";

        # 每秒字节速度控制，0表示不受控制，默认0, 128K
        upload_limit_rate 0;

        # 如果pass页面是以下状态码，就删除此次上传的临时文件
        upload_cleanup 400 404 499 500-505;

        # 语法：track_uploads <zone_name> <timeout>;
        # 上下文：location
        # 作用：声名此location使用upload_progress模块记录文件上传，这条指令必须位于location配置的最后。
        track_uploads proxied 2s;
    }

    location @test {
        proxy_pass   http://$host:$server_port/upload.php;
    }

    location ~ \.php$ {
         fastcgi_index  index.php;
         fastcgi_pass   127.0.0.1:4888;
         fastcgi_param  SCRIPT_FILENAME $document_root/$fastcgi_script_name;
         fastcgi_param  REQUEST_ID $request_id;
         include        /usr/local/etc/nginx/fastcgi_params;
    }
    
    fastcgi_buffers 8 512k;
    fastcgi_buffer_size 512k;
    fastcgi_busy_buffers_size 1024k;
    fastcgi_temp_file_write_size 1024k;
}
```
## html代码
```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title></title>
    <style type="text/css">
        .bar {
            width: 300px;
        }

        #progress {
            background: #eee;
            border: 1px solid #222;
            margin-top: 20px;
        }

        #progressbar {
            width: 0px;
            height: 24px;
            background: #333;
        }
    </style>
</head>
<body>
<form id="upload" enctype="multipart/form-data" action="http://oss.base.qixinyun.com/upload" method="post"
      onsubmit="openProgressBar(); return true;">
    <input type="hidden" name="MAX_FILE_SIZE" value="30000000"/>
    <input name="file" type="file" label="fileupload"/>
    <input name="indexName" type="hidden" value="file" />
    <input type="submit" value="Send File"/>
</form>

<div>
    <div id="progress" style="width: 400px; border: 1px solid black">
        <div id="progressbar" style="width: 1px; background-color: black; border: 1px solid white">&nbsp;</div>
    </div>
    <div id="tp">(progress)</div>
</div>

<script type="text/javascript">

    interval = null;

    function openProgressBar() {
        /* generate random progress-id */
        uuid = "";
        for (i = 0; i < 32; i++) {
            uuid += Math.floor(Math.random() * 16).toString(16);
        }
        /* patch the form-action tag to include the progress-id */
        document.getElementById("upload").action = "http://oss.base.qixinyun.com/upload?X-Progress-ID=" + uuid + "&token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJvYS5iYXNlLnFpeGlueXVuLmNvbSIsImF1ZCI6Im9hLmJhc2UucWl4aW55dW4uY29tIiwiaWF0IjoxNjM0Mjk4MDAyLCJuYmYiOjE2MzQyOTgwMDIsImV4cCI6MTYzNDI5ODYwMiwiZGF0YSI6eyJpZGVudGlmeSI6ImMwNWJhODU1ODQ0MjQzZDhlN2I3NTNkNTU0NWFjZDExIn19.eSQAL276QCSPcYTy3RNbaEZGEssYCN9Y8mLRC7g7UUw";

        /* call the progress-updater every 1000ms */
        interval = window.setInterval(
            function () {
                fetch(uuid);
            },
            1000
        );
    }

    function fetch(uuid) {
        req = new XMLHttpRequest();
        req.open("GET", "http://oss.base.qixinyun.com/progress", 1);
        req.setRequestHeader("X-Progress-ID", uuid);
        req.onreadystatechange = function () {
            console.log(req);
            if (req.readyState == 4) {
                if (req.status == 200) {
                    /* poor-man JSON parser */
                    var upload = eval(req.responseText);

                    document.getElementById('tp').innerHTML = upload.state;

                    /* change the width if the inner progress-bar */
                    if (upload.state == 'done' || upload.state == 'uploading') {
                        bar = document.getElementById('progressbar');
                        w = 400 * upload.received / upload.size;
                        bar.style.width = w + 'px';
                    }
                    /* we are done, stop the interval */
                    if (upload.state == 'done') {
                        window.clearTimeout(interval);
                    }
                }
            }
        }
        req.send(null);
    }

</script>

</body>
</html>
```
## php代码
```php
<?php

header('content-type:text/html;charset=utf-8');

$indexName = $_POST['indexName'];
$temppath = $_POST[$indexName."_path"];
$name = $_POST[$indexName."file_name"];

$final_file_path = getPath() ."/{$name}";


var_dump(rename($temppath,$final_file_path));

function getPath()
{

	$dir = '/var/www/html/'.date('Y').'/'.date('m');

    if (!is_dir($dir)) {
     	mkdir($dir, 0777, true);
 	}

    return $dir;
}
```

* auth.php
```php
public function auth(): bool
    {
        $originalUrl =$this->getOriginalUrl();

        $token = $this->getToken($originalUrl);

        if ($this->verifyToken($token)) {
            return true;
        }

        header('WWW-Authenticate: Basic realm="file Server"');
        header('HTTP/1.1 401 Unauthorized');
        return false;
    }

    /**
     * @SuppressWarnings(PHPMD)
     */
    protected function getOriginalUrl() : string
    {
        return Server::get('HTTP_X_ORIGINAL_URI', '');
    }

    /**
     * 获取传入的token值
     * @param string $originalUrl X-Progress-ID=becefe5c50f87ba37f6e5dfaf32602c1&token=123456
     * @return string
     */
    protected function getToken(string $originalUrl) : string
    {
        $originalUrl = explode('&', $originalUrl);
        foreach ($originalUrl as $item) {
            if (str_contains($item, 'token')) {
                $token = explode('=', $item);
                if (isset($token[1])) {
                    return $token[1];
                }
            }
        }

        return '';
    }

    public function verifyToken(string $token = '') : bool
    {
        if (empty($token)) {
            return false;
        }
        try {
            JWT::$leeway = 60;
            JWT::decode($token, Core::$container->get('jwt.key'), ['HS256']);

            return true;
        } catch (\UnexpectedValueException $e) {
            unset($e);
            return false;
        }
    }

    public function createToken() : bool
    {
        $nowTime = time();

        $token = [
            'iss' => Core::$container->get('jwt.iss'), //签发者
            'aud' => Core::$container->get('jwt.aud'), //jwt所面向的用户
            'iat' => $nowTime, //签发时间
            'nbf' => $nowTime + Core::$container->get('jwt.nbf'), //在什么时间之后该jwt才可用
            'exp' => $nowTime + Core::$container->get('jwt.exp'), //过期时间+10min
            'data' => [
                'identify' => $this->generateIdentify()
            ]
        ];

        $jwt = JWT::encode($token, Core::$container->get('jwt.key'));

        $this->render(new AuthTokenView($jwt));
        return true;
    }

    /**
     * [generateIdentify 生成登录标识]
     * @return string [string]  [返回类型]
     */
    protected function generateIdentify() : string
    {
        return md5(serialize(Server::get('marmot')).time());
    }

```

## 参数结果上传比对

|nginx <br>`client_max_body_size`| nginx <br>`upload_max_file_size` |php <br>`post_max_size`|php <br>`upload_max_filesize`|size|status|time(s)|
|:---|:---|:---|:---|:---|:---|:---|
30m |0(不限制)| 300M |2048m|14.3m| 200 |< 1|
30m |0(不限制)| 300M |2048m|20.5m| 200 |< 1|
30m |0(不限制)| 300M |2048m|23m| 200 |< 1|
30m |0(不限制)| 300M |2048m|28.2m| 200 |< 1|
30m |0(不限制)| 300M |2048m|29m| 200 |< 1|
30m |0(不限制)| 300M |2048m|30.5m|200|< 1|
30m |0(不限制)| 300M |2048m|30.8m|200|< 1|
30m |0(不限制)| 300M |2048m|31.5m| 413 |< 1|
300m|0(不限制)| 500M |2048m|31.5m| 200| <=9|
300m|0(不限制)| 500M |2048m|34.5m| 200| <=2|
300m|0(不限制)| 500M |2048m|199.1m| 200| <=25|
300m|0(不限制)| 500M |2048m|233.9m| 200 | <=25|
500m|0(不限制)| 500M |2048m|391.7m| 200 | <=38|
500m|0(不限制)| 100M |2048m|391.7m| 200 | <=38|
500m|0(不限制)| 100M |8m|391.7m| 200 | 20-38|
500m |0(不限制)| 30M | 8m |76.5m| 200 |< 1|
500m |30m| 30M | 8m |49.2m| 200 |文件被忽略未上传|

### 结论
* 上传大小限制与nginx的`client_max_body_size`有关，与phpfpm相关参数无关
* 当上传文件大小超过nginx的`upload_max_file_size`时，nginx返回200，但是该文件被忽略没有进行上传，php接不到post体为空
* `client_max_body_size`的大小限制 以配置文件的最小单元为准（例：server外和server里同时定义，最终以server里的值为准）

## 待解决问题
### 1.Mac上传文件后，php`rename`后，文件无权限访问（文件夹权限已经是777）
```
total 1013656
-r--------  1 nobody  wheel   27010695  9 27 16:43 test.pdf
-r--------  1 nobody  wheel  391688684  9 27 16:42 test.zip
-r--------  1 nobody  wheel   76527485  9 27 16:43 test.pdf
```
#### 处理方式
* 暂无

### 1.Mac上传文件后，php`rename`后，中文被转义
#### 原文件
```
《程序员的职业素养》.pdf
黑客与画家(中文版).docx
实现模式.pdf
```
#### 结果
```
total 1013656
-r--------  1 nobody  wheel   27010695  9 27 16:43 &#12298;&#31243;&#24207;&#21592;&#30340;&#32844;&#19994;&#32032;&#20859;&#12299;.pdf
-r--------  1 nobody  wheel  391688684  9 27 16:42 &#23454;&#29616;&#27169;&#24335;.pdf
-r--------  1 nobody  wheel   76527485  9 27 16:43 &#40657;&#23458;&#19982;&#30011;&#23478;(&#20013;&#25991;&#29256;).docx
```

#### 建议处理方式
* 不保留原文件名，重新命名文件


## 代理上传测试

### 准备
```bash
echo "127.0.0.1 upload.local.com" > /etc/hosts
mkdir /var/www/html/upload
touch /var/www/html/upload/index.html
vim /usr/local/etc/nginx/conf.d/upload_proxy.conf
```
#### upload_proxy.conf
```
server {
    listen       8890;
    server_name  upload.local.com;

    client_max_body_size 400m;
    client_body_buffer_size 1024k;

    real_ip_header X-Real-IP;

    fastcgi_intercept_errors on;
    error_page 500 502 503 504  /server/500.html;
    error_page 404  /server/404.html;
    
    root /var/www/html/upload;

    location = /server/500.html {
        internal;
    }

    location = /server/404.html {
        internal;
    }

    location = /favicon.ico {
        log_not_found off;
        access_log off;
    }

    location ~.*\.(js|css|html|png|jpg)$ {
            access_log   off;
    }

    location / {
        index index.html index.php;
    }

    location ~ /\. {
        deny all;
    }

    location /upload {
        proxy_pass   http://127.0.0.1:8889/upload;
    }
    
    fastcgi_buffers 8 512k;
    fastcgi_buffer_size 512k;
    fastcgi_busy_buffers_size 1024k;
    fastcgi_temp_file_write_size 1024k;
}
```

#### index.html
```
<html>
  <head>
    <title>UploadProxy</title>
  </head>
  <body>
    <h2>Select files to upload</h2>
    <form name="upload" method="POST" enctype="multipart/form-data" action="http://127.0.0.1:8890/upload">
      <input type="file" name="file"><br>
      <input type="submit" name="submit" value="Upload">
      <input type="hidden" name="description" value="xxxxx">
    </form>
  </body>
</html>

```

#### 上传
* 访问 `http://upload.local.com:8090/index.html` 选择文件并上传
* 在`/var/www/html/2021/09` 出现上传的文件

## 测试结果
* 测试成功，可进行代理上传