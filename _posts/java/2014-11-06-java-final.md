---
layout: post
category : java
tags : [java]
---
{% include JB/setup %}

在看并发方面的文章，当把一个ArrayList对象给一个final变量的时候，

此时的ArrayList是否是线程安全的？


查了不少文章，发现这种可变对象不是线程安全的。


###关于final的重要知识点

final关键字可以用于成员变量、本地变量、方法以及类。

final成员变量必须在声明的时候初始化或者在构造器中初始化，否则就会报编译错误。

你不能够对final变量再次赋值。

本地变量必须在声明时赋值。

在匿名类中所有变量都必须是final变量。

final方法不能被重写。

final类不能被继承。

final关键字不同于finally关键字，后者用于异常处理。

final关键字容易与finalize()方法搞混，后者是在Object类中定义的方法，是在垃圾回收之前被JVM调用的方法。

接口中声明的所有变量本身是final的。

final和abstract这两个关键字是反相关的，final类就不可能是abstract的。

final方法在编译阶段绑定，称为静态绑定(static binding)。

没有在声明时初始化final变量的称为空白final变量(blank final variable)，它们必须在构造器中初始化，或者调用this()初始化。不这么做的话，编译器会报错“final变量(变量名)需要进行初始化”。


[深入理解Java中的final关键字](http://www.importnew.com/7553.html '深入理解Java中的final关键字')

[深入理解Java内存模型（六）——final](http://www.infoq.com/cn/articles/java-memory-model-6 '深入理解Java内存模型（六）——final')

[Thread-safety with the Java final keyword](http://www.javamex.com/tutorials/synchronization_final.shtml 'Thread-safety with the Java final keyword')


    

