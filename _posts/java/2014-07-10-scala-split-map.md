---
layout: post
category : scala 
tags : [scala]
---
{% include JB/setup %}

今天在处理日志的时候，会现有一个很有意思的问题。

原始日志的格式如下：

    requestTime:2014-07-09 00:00:00"|"requesturl:http://125.39.222.155/abc.do"|"x-real-ip:0.0.0.0"|"reqMethod:POST"|"machineName:iPhone5,2


一般的处理方式是把它转换成 key,value对，然后返回此结构。

保存的结构一般是用 hashMap之类的（在java中）, 在scala中，直接用Map就可以了。


用java不多不少写了快 30行代码，用scala基本上一行就可以搞定


    val aumap = audi.split("""\|""").toList.map(x=>x.split(""":""",2)).map(x=>Tuple2(x(0),x(1))).toMap

scala中的split因为可以传正则表达示，所以有些字符一定要转义

因为split之后是一人Array,一定要调用toList才能进入map.

split可以对次数进行限制。如果次数不限制，那requestTime 中间的分，秒也会被split.

如果List(Tuple2(),Tuple2()) 这种结果，直接调用toMap，会被转换成Map.


 
[scala-string-split-does-not-work](http://stackoverflow.com/questions/11284771/scala-string-split-does-not-work 'http://stackoverflow.com/questions/11284771/scala-string-split-does-not-work')


