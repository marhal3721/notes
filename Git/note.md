- [Git](#git)
    - [创建并切换分支](#创建并切换分支)
    - [查看远程分支](#查看远程分支)
    - [查看所有分支](#查看所有分支)
    - [删除本地分支](#删除本地分支)
    - [删除远程分支](#删除远程分支)
    - [更新分支情况](#更新分支情况)
    - [Tag](#Tag)
    - [撤销commit](#撤销commit)
    - [撤销add](#撤销add)
    - [BranchExample](#BranchExample)
    - [撤销远程push](#撤销远程push)
    - [Git添加秘钥](#Git添加秘钥)
    - [Git同时推送到多个仓库](#gitPushMoreRepository)

## <a id="git">git</a>

* 切换分支
```bash
git checkout [branch name] 
```

* <a id="创建并切换分支">创建并切换分支</a>
```bash
git checkout -b [branch name] 
```

* 关联分支
```bash
git branch --set-upstream-to=origin/dev [branch name] 
```

* 推送分支
```bash
git push -u origin [branch name]
```

* 忘记切换分支已经更改了代码
* 1. 把当前未提交到本地（和服务器）的代码推入到 Git 的栈中
```bash
git stash
```
* 2. 切换分支
```bash
git checkout [branch name]
```
* 3. 将栈里面存放的代码应用回来
```bash
# 保留栈里面数据
git stash apply
```
或
```bash
# 删除栈里面数据
git stash pop
```
* 4. 清空栈
```bash
git stash clear
```

* <a id="查看远程分支">查看远程分支</a>
```bash
git branch -r
```

* <a id="查看所有分支">查看所有分支</a>
```bash
git branch -a
```

* 创建本地分支
```bash
git branch [branch name]
```

* <a id="删除本地分支">删除本地分支</a>

```bash
# 删除一个已被终止的分支
git branch -d [branch name]

# 删除一个正打开的分支---强制删除
git branch -D [branch name]
```

* <a id="删除远程分支">删除远程分支</a>
```bash
git push origin :[branch name]
git push origin --delete [branch name]
```

* <a id="更新分支情况">更新分支情况</a>
```bash
# 远程删了分支本地还在的处理
git fetch origin --prune

# 1.追踪本地分支与仓库的关系
git remote show origin
## track 已同步的分支
## new (next fetch will store in ...) 仓库里的新分支，本地没有
## stale (use 'git remote prune' to remove) 本地有但是仓库已经删除

# 2.将仓库中已删除的分支与本地分支的追踪关系删除掉
git remote prune origin

# 3.本地分支删除
git branch -D [branch_name]

# 4.本地仓库添加仓库新分支trace
git fetch origin [branch_name]
```

* <a id="Tag">Tag</a>

```bash
# tag 列表
git tag
# 创建tag
git tag -a v0.9.0 -m "release 0.9.0 version"
# 推送tag
git push origin [branch name] --tag
# 删除本地tag
git tag -d 'v0.1.0'
# 删除远程tag
git push origin :refs/tags/v0.1.0
```

* <a id="撤销commit">撤销已经commit但未push的commit</a>
```
git reset --soft HEAD^ 
```

* <a id="撤销add">撤销add</a>
```
git reset HEAD
```

* <a id="BranchExample">Branch Example</a>
```
git checkout -b feature-#90707-resourceCata 
git push -u origin feature-#90707-resourceCata
git commit -m '推送备注'
git push
git checkout dev && git pull && git merge feature-\#90707-resourceCata && git push
```
* <a id="撤销远程push">撤销远程push</a>
```bash
# 查看日志，获取需要回退的版本号
git log
# 方式一：重置到指定版本的提交，达到撤销提交的目的
git reset --soft <版本号>
# 方式二：撤销commit，同时将代码恢复到对应ID的版本
git reset --hard <commitId>
# 强制提交到当前版本号
git push origin <分支名称> --force
```

* <a id="Git添加秘钥">Git添加秘钥</a>

```bash
ssh-keygen -t rsa -C "junwuji555@sina.com"
cat .ssh/id_rsa.pub
ssh-add .ssh/id_rsa

# linux 下解决仍需要输入账号密码的问题
git config --global credential.helper store
# 测试连接
ssh -T git@github.com
# git配置信息
git config --list
```

* <a id="gitPushMoreRepository">Git同时推送到多个仓库</a>
```bash
# 1.给origin添加一个远程push地址，gitUrl 为新增的git仓库地址
git remote set-url --add origin gitUrl
# 2.验证是否多了一条push地址
git remote -v 

# 强制推送
git push origin master -f

# 删除仓库 
git remote set-url --delete origin 地址
```

* <a id="">查看最后一次提交者</a>
```bash
git show -s --format=%an
```