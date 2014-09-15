---
layout: post
category : hadoop
tags : [spark]
---
{% include JB/setup %}


一直想把flume和spark整合，测试了一下，问题不大。

因为在yarn集群上面，所以得先启动spark application，这样才能知道他的flume reciver在哪一台机器上面。

如:

指定端口为 60000

    /usr/local/webserver/sparkhive/bin/spark-submit --class org.apache.spark.examples.streaming.FlumeEventCount --master yarn --deploy-mode cluster --queue  online --num-executors 5 --driver-memory 6g --executor-memory 20g --executor-cores 5 lib/spark-examples-1.0.2-hadoop2.4.1.jar 0.0.0.0 60000
