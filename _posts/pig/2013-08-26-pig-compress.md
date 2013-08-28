---
layout: post
category : java 
tags : [java]
---
{% include JB/setup %}

##hive调优

###hive 压缩

    set hive.exec.compress.output true;
    set mapred.output.compression.codec org.apache.hadoop.io.compress.SnappyCodec;
    set mapred.output.compression.type block;

[snappy for pig](http://hadoopified.wordpress.com/2012/01/24/snappy-compression-with-pig/ 'snappy for pig')

