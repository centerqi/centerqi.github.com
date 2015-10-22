---
layout: post
category : java 
tags : [scala]
---
{% include JB/setup %}


spark 1.5.1改进不少，今天重新编译了一下，他那个编译脚本还是不少问题(make-distribution.sh)，我自己重新写了一下，并且用mvn编译，自己写了一个发布脚本。

mvn 命令 

    /data/server/hadoop/workspace/spark-1.5.1/build/apache-maven-3.3.3/bin/mvn clean package -DskipTests -Phadoop-2.6 -Pyarn -Phive -Phive-thriftserver 

打包脚本

    sh package.sh



##主依赖

1. hive 1.2

2. scala 2.10

3. hadoop 2.6

###与hive集成

spark与hive集成方式有两.

    方案一  在hive中把执行引擎换成 spark.

    方案二 spark只读hive的元数据，用spark-sql或者spark-shell去执行hql语句(比较推荐这种方式，优点是解藕，不影响hive原有的执行方式，并且spark升级不强依赖于hive版本). 
    


##hive配置

1. copy hive的配置文件（hive-site.xml） 到 spark的conf目录下

2. 修改配置文件添加依赖，主要是把spark的conf加入到SPARK_CLASSPATH

    export SPARK_CLASSPATH=$SPARK_CLASSPATH:/usr/local/webserver/spark-1.5.1-bin-2.6.1/conf/*:/usr/local/webserver/spark-1.5.1-bin-2.6.1/lib/*
    export SPARK_CLASSPATH=$SPARK_CLASSPATH:/usr/local/webserver/hadoop-spark/share/hadoop/hdfs/*:/usr/local/webserver/hadoop-spark/share/hadoop/common/*:/usr/local/webserver/hadoop-spark/share/hadoop/common/lib/*:/usr/local/webserver/hadoop-spark/share/hadoop/hdfs/lib/*:/usr/local/webserver/hadoop-spark/share/hadoop/mapreduce/*:/usr/local/webserver/hadoop-spark/share/hadoop/mapreduce/lib/*:/usr/local/webserver/hadoop-spark/share/hadoop/yarn/*:/usr/local/webserver/hadoop-spark/share/hadoop/yarn/lib/*

3. 添加datanucleus类库

把liblib_managed/jars/全部copy到spark/lib下面

    datanucleus-api-jdo-3.2.6.jar
    datanucleus-core-3.2.10.jar
    datanucleus-rdbms-3.2.9.jar


4. 添加jdbc类库

    mysql-connector-java-5.1.21.jar
    

##验证
以yarn-client模式验证spark-sql


    ./bin/spark-sql --master yarn-client --num-executors 4 --driver-memory 20g --executor-memory 25g 



