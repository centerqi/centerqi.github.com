---
layout: post
category : java 
tags : [java]
---
{% include JB/setup %}

在用flume的时候，要对一分钟之类的文件做merge，每一个文件大小在 100MB级别的。

做法一 把一个文件直接append
