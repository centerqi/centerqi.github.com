---
layout: post
category : hadoop
tags : [spark]
---
{% include JB/setup %}


scala 的 case class特别方便，特意看了一下他的文档;

很多人都在问他存在的意义。

1. 相对于java bean来说，是一种优化，省略了很多烦人的代码。

2. 也是case class设置的目标，用来做pattern match.  

###case class 的特点

1. 构造函数的参数变成了一个公共属性.

2. 样本类基于构造函数的参数，自动地实现了 toString(), equals(), hashCode()。

3. 实例化一个case class不用new参数。


定义:

    case class Person(firstName: String, lastName: String)
     
    val me = Person("Daniel", "Spiewak")
    val first = me.firstName
    val last = me.lastName
     
    if (me == Person(first, last)) {
      println("Found myself!")
      println(me)
    }


###模式匹配

这个是case class设计的目标，但是反而感觉用的比较少。


    case class Calculator(brand: String, model: String)
    val hp20b = Calculator("hp", "20B")
    val hp30b = Calculator("hp", "30B")

    def calcType(calc: Calculator) = calc match {
      case Calculator("hp", "20B") => "financial"
      case Calculator("hp", "48G") => "scientific"
      case Calculator("hp", "30B") => "business"
      case Calculator(_, _) => "Calculator of unknown type"
    }


[basic](https://twitter.github.io/scala_school/zh_cn/basics2.html 'https://twitter.github.io/scala_school/zh_cn/basics2.html')

[case-classes-are-cool](http://www.codecommit.com/blog/scala/case-classes-are-cool 'http://www.codecommit.com/blog/scala/case-classes-are-cool')

