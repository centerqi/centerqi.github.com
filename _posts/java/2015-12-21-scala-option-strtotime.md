---
layout: post
category : java 
tags : [scala]
---
{% include JB/setup %}



Scala中有什么优雅的方式把一个String转变成Int？

突然发现Option用上了。

先来用java的方式去实现

    def toInt(s: String): Int = {
      try {
        s.toInt
      } catch {
        case e: Exception => 0
      }
    }


用scala的思想去实现，应该是这样子的

    def toInt(s: String): Option[Int] = {
      try {
        Some(s.toInt)
      } catch {
        case e: Exception => None
      }
    }
     


最近要实现php中那个strtotime的功能，用scala可以如此实现



    import org.joda.time._
    import org.joda.time.format.{DateTimeFormat, DateTimeFormatter}

    def strTotimestamp(requesttime:String):Option[Long]={
        try{
          val format:DateTimeFormatter  = DateTimeFormat .forPattern("yyyy-MM-dd HH:mm:ss")
          val timestamp = DateTime.parse(requesttime,format).getMillis
          Some(timestamp)
        } catch {
          case e:Exception =>None
        }
    }

