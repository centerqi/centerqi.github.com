---
layout: post
category : python
tags : [python]
---
{% include JB/setup %}

python class 中的类属于与实例(对象)属性，一直没具体搞得太明白，总结如下


        class C():
            version=1.2

        c=C()
        C.version #1.2
        c.version #1.2
        C.version+=1 #2.2
        c.version #2.2

        c.version+=2 #4.2
        C.version #2.2

1. 任何对实例属性赋值都会创建一个新的属性(如果不存在的话).  

2. 类属性的更新只能能过类来更新(有点绕)

