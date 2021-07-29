* [Linux](#Linux)

## <a id="Linux">Linux Command</a>

```bash
## 查看开机自启项
systemctl list-unit-files --type=service|grep enabled
## 关闭开机自启项
sudo systemctl disable apache2.service
sudo systemctl disable nginx.service
## 查看文件前几行
head -n 10 /test.sql
## 查看文件后几行
tail -n 10 /test.sql
## 将文件的前/后几行输出到指定文件
head/tail -n 10 /test.sql >> /test10.sql
## 从第3000行开始，显示1000行（显示3000~3999行）
cat filename.txt | tail -n +3000 | head -n 1000
## 显示1000行到3000行
cat filename.txt | head -n 3000 | tail -n +1000 
sed -n '1000,3000p' filename.txt
```

