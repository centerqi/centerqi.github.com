---
layout: post
category : hadoop
tags : [scala]
---
{% include JB/setup %}


因为我用scala来写storm的job,log4j在storm中使用的时候，要注意一下序列化的问题，解决方式有两种。

方式一

    @transient lazy private val LOG: Logger = Logger.getLogger(classOf[LogTest])

以上是用标注来解决序列化的问题，告诉此变量一要序列化。

方式二


    var LOG:Logger = _
    
    //在prepare中实例化
    this.LOG =  Logger.getLogger(  getClass().getName())


个人喜欢还是方式一

[https://johlrogge.wordpress.com/tag/scala/](https://johlrogge.wordpress.com/tag/scala/ 'https://johlrogge.wordpress.com/tag/scala/')

[http://stackoverflow.com/questions/6466190/how-to-log-in-scala-without-a-reference-to-the-logger-in-every-instance](http://stackoverflow.com/questions/6466190/how-to-log-in-scala-without-a-reference-to-the-logger-in-every-instance 'http://stackoverflow.com/questions/6466190/how-to-log-in-scala-without-a-reference-to-the-logger-in-every-instance')

