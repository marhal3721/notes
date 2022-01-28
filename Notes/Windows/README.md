# windows 批量重命名文件夹

## 获取文件名到指定文件
* get_name.bat

```bat
DIR *.* /B >LIST.TXT;
```
## 去除文件的空格
* remove_space.bat

```bat
@echo off&setlocal enabledelayedexpansion
for /f "delims=" %%i in ('dir /s/b *.*') do (
    set "foo=%%~nxi"
    set foo=!foo: =!
    set foo=!foo: =!
    ren "%%~fi" "!foo!"
)
exit
```

## 去除文件的括号
* remove_brackets.bat

```bat
@Echo Off&SetLocal ENABLEDELAYEDEXPANSION
FOR %%a in (*) do (
echo 正在处理 %%a
set "name=%%a"
set "name=!name:(=!"
set "name=!name:)=!"
ren "%%a" "!name!"
)
exit
```


## 批量重命名
* rename.bat
* 编辑完，文件格式保存为 `ANSI`

```bat
cd "D:\BaiduNetdiskDownload\MyCAT+MySQL 搭建高可用企业级数据库集群[Dmz社区 DmzSheQu.Com]\第3章 MYCAT核心配置详解"
D:
ren [Dmz社区DmzSheQu.Com]第10课table标签_batch.mp4 第10课table标签_batch.mp4
ren [Dmz社区DmzSheQu.Com]第11课dataNode标签_batch.mp4 第11课dataNode标签_batch.mp4
ren [Dmz社区DmzSheQu.Com]第12课dataHost标签_batch.mp4 第12课dataHost标签_batch.mp4
ren [Dmz社区DmzSheQu.Com]第13课dataHost标签属性_batch.mp4 第13课dataHost标签属性_batch.mp4
ren [Dmz社区DmzSheQu.Com]第14课heartbeat标签.mp4 第14课heartbeat标签.mp4
ren [Dmz社区DmzSheQu.Com]第15课writehost标签_batch.mp4 第15课writehost标签_batch.mp4
ren [Dmz社区DmzSheQu.Com]第16课schema总结_batch.mp4 第16课schema总结_batch.mp4
ren [Dmz社区DmzSheQu.Com]第1课章节综述_batch.mp4 第1课章节综述_batch.mp4
ren [Dmz社区DmzSheQu.Com]第2课常用配置文件间的关系_batch.mp4 第2课常用配置文件间的关系_batch.mp4
ren [Dmz社区DmzSheQu.Com]第3课_batch.mp4 第3课_batch.mp4
ren [Dmz社区DmzSheQu.Com]第4课_batch.mp4 第4课_batch.mp4
ren [Dmz社区DmzSheQu.Com]第5课_batch.mp4 第5课_batch.mp4
ren [Dmz社区DmzSheQu.Com]第6课常用分片算法上_batch.mp4 第6课常用分片算法上_batch.mp4
ren [Dmz社区DmzSheQu.Com]第7课_batch.mp4 第7课_batch.mp4
ren [Dmz社区DmzSheQu.Com]第8课_batch.mp4 第8课_batch.mp4
ren [Dmz社区DmzSheQu.Com]第9课_batch.mp4 第9课_batch.mp4
pause
```
