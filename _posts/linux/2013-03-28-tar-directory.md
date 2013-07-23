---
layout: post
category : linux
tags : [c]
---
{% include JB/setup %}

只打包目录结构,不打包文件

    tar cf dirstructure.tar --no-recursion --files-from <( find . -type d)


[exclude指定的目录](http://stackoverflow.com/questions/4210042/exclude-directory-from-find-command 'exclude 指定的目录')


