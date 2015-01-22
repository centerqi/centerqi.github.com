---
layout: post
category : scala 
tags : [storm]
---
{% include JB/setup %}

最近写bolt的时候，经常遇到serialize异常，究其原因，发现是在 bolt中有些不能序列化的字段，如数据库联系，redis联接之类的。

scala 没有自己的序列化框架，完全用的是java那一套，java.io.Serializable 默认只能对一些基本类型的数据结构进行序列化。

scala有如下解决方式


    // Must be transient because Logger is not serializable
    @transient lazy private val log: Logger = LoggerFactory.getLogger(classOf[AvroKafkaSinkBolt[T]])


    // Must be transient because KafkaProducerApp is not serializable.  The factory approach to instantiate a Kafka producer
    // unfortunately means we must use a var combined with `prepare()` -- a val would cause a NullPointerException at
    // runtime for the producer.
    @transient private var producer: KafkaProducerApp = _

###@transient

这个好像就是序列化的时候，直接把它的值变成null，也就是不去序列化此字段的引用。

###lazy

引用别人的一个很好的解释

    val strVal = scala.io.Source.fromFile("test.txt").mkString //在strVal被定义的时候获取值，如果test.txt不存在，直接报异常
     
    lazy val strLazy = scala.io.Source.fromFile("test.txt").mkString //在strLazy第一次被使用的时候取值，如果test.txt不存在，不使用strLazy是不会报异常的，第一次访问strLazy的时候报异常 
     
    def strDef = scala.io.Source.fromFile("test.txt").mkString //每次使用的时候都重新取值<span></span>span>


[scala 标注讲解](http://www.artima.com/pins1ed/annotations.html 'http://www.artima.com/pins1ed/annotations.html')


[scala 标注一个不错的ppt](http://www.slideshare.net/knoldus/annotations-14963496 'http://www.slideshare.net/knoldus/annotations-14963496')

[kafka-storm-starter](https://github.com/miguno/kafka-storm-starter/tree/develop/src/main/scala/com/miguno/kafkastorm 'https://github.com/miguno/kafka-storm-starter/tree/develop/src/main/scala/com/miguno/kafkastorm')
