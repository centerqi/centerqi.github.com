---
layout: post
category : scala 
tags : [scala]
---
{% include JB/setup %}


scala 中的下划线让人很头痛。

发现有人总结的不错。

##import all

import everything from a package

    import java.util._
    val date = new Date()


import everything from an object



    import scala.util.control.Breaks._

    breakable {
        for(i <- 0 to 10){if(i == 5) break}
    }


## var initialization to default value

    var i:Int = _

##Placeholder syntax

    val list1 = (1 to 10)
    list1.filter(_ > 5)

##In pattern matching

    def inPatternMatching2(s:String) {
     s match {
            case "foo" =>println("foo !")
            case _=> println("not foo")
        }
    }

##Anonymous parameters

    def f(i:Int):String = i.toString

    def g2 = f _

    g2(5)


##Don't import name in namespace

    import java.util.{
    Date => _, //remove Date from namespace
    _ //import everything from java.util
    }

    val date = new Date() //error
