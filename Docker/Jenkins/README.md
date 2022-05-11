## Docker install（本地测试用这个就够了）

### 
```bash
docker build . -t jenkins-1
docker run -itd --name jenkins-a -p 8082:8080 jenkins-1
# 初始密码
docker exec jenkins-a bash -c "cat /var/jenkins_home/secrets/initialAdminPassword"
```

## Centos install with jar

### 1.下载java环境
```bash
yum install java -y
```
### 2.下载war包

```bash
wget http://mirrors.jenkins-ci.org/war-stable/2.332.3/jenkins.war
```

### 3.启动Jenkins
```bash
java -jar ./jenkins.war –httpPort=8080
```

### 4.解除网络限制
```bash
sed -i 's/http:\/\/updates.jenkins-ci.org\/update-center.json/http:\/\/mirror.xmission.com\/jenkins\/updates\/update-center.json/' ./jenkins/hudson.model.UpdateCenter.xml

sed -i "s#https://updates.jenkins.io/update-center.json#https://mirrors.tuna.tsinghua.edu.cn/jenkins/updates/update-center.json#g" ./jenkins/hudson.model.UpdateCenter.xml

```
```text
- <url>http://updates.jenkins-ci.org/update-center.json</url>
+ <url>http://mirror.xmission.com/jenkins/updates/update-center.json</url>
```

```bash
cp ./jenkins/updates/default.json ./jenkins/updates/default.json.bak

# sed -i 's/http:\/\/www.google.com\//http:\/\/www.baidu.com\//' ./jenkins/updates/default.json
sed -i 's#http://www.google.com#https://www.baidu.com#g' ./jenkins/updates/default.json
sed -i 's#https://updates.jenkins.io/download#https://mirrors.tuna.tsinghua.edu.cn/jenkins#g' ./jenkins/updates/default.json
sed -i 's#http://updates.jenkins-ci.org/download#https://mirrors.tuna.tsinghua.edu.cn/jenkins#g' ./jenkins/updates/default.json
```
### 5.浏览器访问安装
```text
http://172.16.1.32:8080/
```

## Centos install with yum （线上因为要处理别的事情应该要直接部署）
```bash
wget -O /etc/yum.repos.d/jenkins.repo https://pkg.jenkins.io/redhat-stable/jenkins.repo
rpm --import https://pkg.jenkins.io/redhat-stable/jenkins.io.key
yum install jenkins -y
systemctl start jenkins
systemctl enable jenkins
sed -i 's/http:\/\/updates.jenkins-ci.org\/download/https:\/\/mirrors.tuna.tsinghua.edu.cn\/jenkins/g' /var/lib/jenkins/updates/default.json 
sed -i 's/http:\/\/www.google.com/https:\/\/www.baidu.com/g' /var/lib/jenkins/updates/default.json

```

## 插件
* Localization: Chinese (Simplified)
* Localization Support Plugin
* Locale plugin
* Authentication Tokens API Plugin
* Rebuilder
* Role-based Authorization Strategy
* SSH Build Agents plugin
* SSH plugin
* Generic Webhook Trigger Plugin
* Web for Blue Ocean
* Pipeline Utility Steps
* SSH Pipeline Steps
* git parameter


