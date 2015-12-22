---
layout: post
category : java 
tags : [scala]
---
{% include JB/setup %}


在读Spark源码时，发现有三个方法用的较多，分别是 classOf, isInstanceOf, asInstanceOf

其实与java中的一些方法存在对应关系。

<table>
<tr><td>Scala 方法</td><td>Java中对应的实现</td></tr>
<tr><td>classOf[T]</td><td>T.class</td></tr>
<tr><td>obj.isInstanceOf[T]</td><td>obj instanceof T</td></tr>
<tr><td>obj.asInstanceOf[T]</td><td>(T)obj</td></tr>
</table>

[http://itang.iteye.com/blog/1128707](http://itang.iteye.com/blog/1128707 'http://itang.iteye.com/blog/1128707')
