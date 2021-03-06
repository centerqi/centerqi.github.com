---
layout: post
category : git 
tags : [git]
---
{% include JB/setup %}

## git init 

	git init
	git remote add origin https://gitlab.abc.com/abc.hu/dd.git
	git add .
	git commit -m "Initial commit"
	git push -u origin master


## git 多账户切换解决方案

一个公司有两套 gitlab, git 默认会记住用户名和密码，如果有两套，系统记住会非常麻烦，所以通过如下设置，可以不记住用户名和密码

	git config --local credential.helper ""


## 分支重命名

[分支重命名](https://stackoverflow.com/questions/30590083/how-do-i-rename-both-a-git-local-and-remote-branch-name)


## push 到指定的分支

	git push origin branch-1


## git 删除当前本地分支

	git branch -d localBranchName

[git 删除当前本地分支](https://chinese.freecodecamp.org/news/how-to-delete-a-git-branch-both-locally-and-remotely/)


## git 从远程拉分支到本地并创建分支名

	git fetch <remote> <rbranch>:<lbranch>
	git checkout <lbranch>
	git fetch origin Need_to_modify:Need_to_modify


## git 版本合并

[git 版本合并](https://git-scm.com/book/zh/v2/Git-%E5%88%86%E6%94%AF-%E5%88%86%E6%94%AF%E7%9A%84%E6%96%B0%E5%BB%BA%E4%B8%8E%E5%90%88%E5%B9%B6)


## git 常用操作

https://www.cnblogs.com/springbarley/archive/2012/11/03/2752984.html

http://marklodato.github.io/visual-git-guide/index-zh-cn.html

https://rogerdudler.github.io/git-guide/

https://docs.github.com/cn/github/getting-started-with-github/managing-remote-repositories

