---
layout: post
category : hadoop
tags : [spark]
---
{% include JB/setup %}


spark现在的配置还是有点乱的，很多参数可以配置。为了让spark hive的功能可以在 yarn cluster模式下运行。

花了点时间仔细看了一下他的配置.

1. 编译spark with hive.

    sh make-distribution.sh --hadoop 2.4.1 --with-yarn --skpi-java-test --tgz --with-hive


2. 把hive 的hive-site.xml放到spark的conf下面

3. 把mysql的驱动放入 spark/lib下面

4. 更改配置文件.

在spark-env.sh中加入spark/lib下面的几个jdo的jar




    export SPARK_YARN_USER_ENV="CLASSPATH=/usr/local/webserver/hadoop-2.4.1/etc/hadoop:/usr/local/webserver/sparkhive/conf/:/usr/local/webserver/hadoop-2.4.1/shar
e/hadoop/common/lib/hadoop-lzo-0.4.20-SNAPSHOT.jar:/usr/local/webserver/sparkhive/lib/datanucleus-api-jdo-3.2.1.jar:/usr/local/webserver/sparkhive/lib/datanucleus-core-3.2.2.jar:/usr/local/webserver/sparkhive/lib/datanucleus-rdbms-3.2.1.jar:/usr/local/webserver/sparkhive/conf/hive-site.xml:/usr/local/webserver/sparkhive/lib/mysql-connector-java-5.1.25-bin.jar"


`一定要把spark/conf的目录加入到classpath`,为什么要这样，暂时还没搞明白.有可能是为了找hive-site.xml。

不然会报如下错误:

    14/08/30 16:30:49 INFO Datastore: The class "org.apache.hadoop.hive.metastore.model.MFieldSchema" is tagged as "embedded-only" so does not have its own datastore table.
    14/08/30 16:30:49 INFO Datastore: The class "org.apache.hadoop.hive.metastore.model.MOrder" is tagged as "embedded-only" so does not have its own datastore table.
    14/08/30 16:30:54 ERROR Hive: NoSuchObjectException(message:default.tmp_adclick_udc table not found)
            at org.apache.hadoop.hive.metastore.HiveMetaStore$HMSHandler.get_table(HiveMetaStore.java:1373)
            at sun.reflect.NativeMethodAccessorImpl.invoke0(Native Method)
            at sun.reflect.NativeMethodAccessorImpl.invoke(NativeMethodAccessorImpl.java:57)
            at sun.reflect.DelegatingMethodAccessorImpl.invoke(DelegatingMethodAccessorImpl.java:43)
            at java.lang.reflect.Method.invoke(Method.java:606)
            at org.apache.hadoop.hive.metastore.RetryingHMSHandler.invoke(RetryingHMSHandler.java:103)
            at com.sun.proxy.$Proxy26.get_table(Unknown Source)
            at org.apache.hadoop.hive.metastore.HiveMetaStoreClient.getTable(HiveMetaStoreClient.java:854)
            at sun.reflect.NativeMethodAccessorImpl.invoke0(Native Method)
            at sun.reflect.NativeMethodAccessorImpl.invoke(NativeMethodAccessorImpl.java:57)
