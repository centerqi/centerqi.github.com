---
layout: post
category : java 
tags : [java]
---
{% include JB/setup %}

在读 hadoop pig 源码的时候，特别是在built in function 的时候，有很多如下的代码

    private static final Log log = LogFactory.getLog(Top.class);

特意查了一下手册，发现这就是所谓的 反射，可以获取 class 的一些元数据  

也就是可以获取class 的一些定义信息 

这个类的名字叫 Class ，如这样可以得到类的名称 

    Class t = Top.class;
    System.out.println(t.getName())

