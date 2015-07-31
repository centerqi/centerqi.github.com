---
layout: post
category : hadoop
tags : [hbase]
---
{% include JB/setup %}


本来想用Pig写Hbase的，但是因为很多Hive表，再单独搞一套也没必要。

`hbase-0.94.20  hadoop 1.1.2   hive 0.13.1 zookeeper-3.4.5`


1. 配置hive的CLASS_PATH

更改 hive-config.sh 文件

    export HBASE_CONF_DIR=/usr/local/webserver/hbase/conf
    export HBASE_HOME=/usr/local/webserver/hbase
    export CLASSPATH=${CLASSPATH}:${HADOOP_CONF_DIR}:${HBASE_CONF_DIR}


2. 配置hive启动参数

    /usr/local/webserver/hive_udc/bin/hiveudc  --auxpath   /usr/local/webserver/hive_udc/lib/hive-hbase-handler-0.13.1.jar,/usr/local/webserver/hive_udc/lib/zookeeper-3.4.5.jar,/usr/local/webserver/hive_udc/lib/guava-11.0.2.jar,/usr/local/webserver/hbase/hbase-0.94.20.jar  --hiveconf hbase.zookeeper.quorum=idc02-hd-ds-b01,idc02-hd-ds-b02,idc02-hd-ds-b03 



操作hbase可以参考官方文档 [https://cwiki.apache.org/confluence/display/Hive/HBaseIntegration](https://cwiki.apache.org/confluence/display/Hive/HBaseIntegration 'https://cwiki.apache.org/confluence/display/Hive/HBaseIntegration')
