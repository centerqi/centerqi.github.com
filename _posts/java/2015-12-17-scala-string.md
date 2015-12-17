---
layout: post
category : java 
tags : [scala]
---
{% include JB/setup %}


打算总结一下Scala中经常用到的几种数据结构，先从String开始。

##字符串追加

    scala> val buf = new StringBuilder //可以定义成val，没必要定义成var,因为StringBuilder当做mutable对象处理
    buf: StringBuilder =

    scala> buf += 'a' //字符追加可以用 +=
    res3: buf.type = a

    scala> buf ++= "bcdef" //字符串追加得用 ++= 或者append
    res4: buf.type = abcdef

    scala> buf.toString
    res5: String = abcdef

其实StringBuilder就是对java 的StringBuilder的包装，特意看了一下他的源代码实现。

    type StringBuilder = scala.collection.mutable.StringBuilder //对StringBuilder做了一下别名

    final class StringBuilder(private val underlying: JavaStringBuilder) //JavaStringBuilder其实就是java.lang的StringBuilder
            extends AbstractSeq[Char]
            with java.lang.CharSequence
            with IndexedSeq[Char]
            with StringLike[StringBuilder]
            with Builder[Char, String]
            with Serializable {}


##在String中扩展方法

其实用implicit就可以搞定
    
    implicit class StringImprovements(s:String){def increment = s.map(c=>(c+1).toChar)}
    val koudai="koudai"
    scala> koudai.increment
    res7: String = lpvebj


##模板变量

    scala> val templateStr=s"Hello ${koudai} "
    templateStr: String = "Hello koudai ""
