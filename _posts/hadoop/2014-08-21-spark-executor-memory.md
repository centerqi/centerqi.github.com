---
layout: post
category : hadoop
tags : [spark]
---
{% include JB/setup %}


把Spark放到了yarn集群上面。


    spark-1.0.2-bin-2.4.1/bin/spark-submit --class SimpleApp \
    --master yarn \
    --deploy-mode cluster \
    --queue sls_queue_1 \
    --num-executors 3 \
    --driver-memory 6g \
    --executor-memory 10g \
    --executor-cores 5 \
    target/scala-2.10/simple-project_2.10-1.0.jar \
    /user/www/h5/output/2014-08-0*/* \
    /user/www/input/tmp/wordCounts_0


###num-executor

此次数指定了执行任务的结点个数

###driver-memory

driver的执行内存

###executor-memory

这个比较复杂，与spark的模型有关，网上找找了，spark用的是多线程模型,也就是多个task会共用这10g内存。

我特意去nodemanager上面看了一下task的启动参数,-Xms10240m.

    /usr/local/webserver/jdk1.7.0_67//bin/java \
    -server -XX:OnOutOfMemoryError=kill %p -Xms10240m -Xmx10240m \
    -Djava.io.tmpdir=/data/hadoop/nodemanager/usercache/www/appcache/application_1408182086233_0017/container_1408182086233_0017_01_000002/tmp \
    -Dlog4j.configuration=log4j-spark-container.properties \
    org.apache.spark.executor.CoarseGrainedExecutorBackend \
    akka.tcp://spark@idc02-sp.com:33289/user/CoarseGrainedScheduler 1 idc02-sp.com 5

启动的入口为

    org.apache.spark.executor.CoarseGrainedExecutorBackend 

看了一下这个class,具体的参数作用还没全搞明白，先记着，下次再深入读一下代码。

    def main(args: Array[String]) {
        args.length match {
          case x if x < 4 =>
            System.err.println(
              // Worker url is used in spark standalone mode to enforce fate-sharing with worker
              "Usage: CoarseGrainedExecutorBackend <driverUrl> <executorId> <hostname> " +
              "<cores> [<workerUrl>]")
            System.exit(1)
          case 4 =>
            run(args(0), args(1), args(2), args(3).toInt, None)
          case x if x > 4 =>
            run(args(0), args(1), args(2), args(3).toInt, Some(args(4)))
        }   
      }
    }


[apache-spark-multi-threads-model](http://dongxicheng.org/framework-on-yarn/apache-spark-multi-threads-model/ 'http://dongxicheng.org/framework-on-yarn/apache-spark-multi-threads-model/')
