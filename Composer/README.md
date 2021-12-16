- [Composer](#Composer)

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
## Command "self-update" is not defined.错误解决
######
#1.删除原有的composer
sudo apt-get purge composer
#2.安装新的composer
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');";
#3.赋予相应的权限
sudo php composer-setup.php --install-dir=/usr/bin --filename=composer;
#4.升级
composer self-update;
######

# 执行诊断命令
composer diagnose

# 清除缓存
composer clear
```