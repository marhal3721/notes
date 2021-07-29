* [Curl](#curl)
* [SSH](#ssh)
* [GitHub](#GitHub)
* [k8s](#k8s)
* [NPM](#npm)

## <a id="curl">curl</a>

* 使用POST登录保存cookie文件
```bash
curl -k -X POST -c cookie.txt --header 'Content-Type: application/json' -d{"name"="18800000000","psd"="Admin123"} http://aa.com
```

* 使cookie文件POST登录

```bash
curl 
	-XPOST 
	-d '{"cellphone":"18800000000","password":"PXpassword0000"}' 
	--header "Content-Type: application/json;charset=UTF-8" 
	-c cookie_xx  
	http://oa.credit.com/api/signIn
```

* 使cookie文件GET查询
```bash
curl -b cookie.txt http://xxx.com
```

## <a id="ssh">SSH</a>

* ssh连接远程服务器
```bash
ssh -p 1211 marhal@47.93.45.242
```

* ssh中文乱码
```bash
export LANG=C
export LC_ALL=zh_CN.utf-8
export LANG=zh_CN.utf-8
```



## <a id="k8s">k8s Command</a>

```bash
ssh -D 127.0.0.1:8080 dev@47.96.157.236 -p 17456 -i /Users/apple/Documents/id_rsa_credit_sanbox

ssh -p 17456 172.25.1.1

# 获取命名空间
kubectl get namespaces;
# 获取命名空间下的容器
kubectl get pods -n=credit-ll
# 构建
kubectl edit deployment/credit-backend -n=credit-ll
# 进入容器
kubectl exec -it credit-backend -n=credit-ll -c=phpfpm bash
# 日志
kubectl logs -f credit-portal-6557fc89bf-x6l2t -n=credit-ty -c=phpfpm
```


## <a id="npm">NPM Command</a>

```bash
# 注册模块镜像
npm set registry https://registry.npm.taobao.org 
yarn config set registry https://registry.npm.taobao.org/
# node-gyp 编译依赖的 node 源码镜像
npm set disturl https://npm.taobao.org/dist 
# 以下选择添加
## chromedriver 二进制包镜像
npm set chromedriver_cdnurl http://cdn.npm.taobao.org/dist/chromedriver
## operadriver 二进制包镜像
npm set operadriver_cdnurl http://cdn.npm.taobao.org/dist/operadriver
## phantomjs 二进制包镜像
npm set phantomjs_cdnurl http://cdn.npm.taobao.org/dist/phantomjs
## node-sass 二进制包镜像
npm set sass_binary_site http://cdn.npm.taobao.org/dist/node-sass
## electron 二进制包镜像
npm set electron_mirror http://cdn.npm.taobao.org/dist/electron/ 
# 清空缓存
npm cache clean
```


