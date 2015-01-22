---
layout: post
category : scala 
tags : [storm]
---
{% include JB/setup %}

一直没搞明白 BaseBasicBolt 与 BaseRichBolt 的区别。

##BaseRichBolt

    You must – and are able to – manually ack() an incoming tuple.
    
    Can be used to delay acking a tuple, 

    e.g. for algorithms that need to work across multiple incoming tuples.

    


##BaseBasicBolt 

    Auto-acks the incoming tuple at the end of its execute() method.
    
    These bolts are typically simple functions or filters.


[http://www.slideshare.net/miguno/apache-storm-09-basic-training-verisign](http://www.slideshare.net/miguno/apache-storm-09-basic-training-verisign 'http://www.slideshare.net/miguno/apache-storm-09-basic-training-verisign')

[twitter-storm如何保证消息不丢失](http://xumingming.sinaapp.com/127/twitter-storm%E5%A6%82%E4%BD%95%E4%BF%9D%E8%AF%81%E6%B6%88%E6%81%AF%E4%B8%8D%E4%B8%A2%E5%A4%B1/ 'http://xumingming.sinaapp.com/127/twitter-storm%E5%A6%82%E4%BD%95%E4%BF%9D%E8%AF%81%E6%B6%88%E6%81%AF%E4%B8%8D%E4%B8%A2%E5%A4%B1/')
