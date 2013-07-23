---
layout: post
category : java 
tags : [java]
---
{% include JB/setup %}

Hive 安装就是很方便
1、下载copy到指定的目录，指定 HIVE_HOME

2、在dfs 上面建立目录

    fs -mkdir /user/hadoop/hive/warehouse
    fs -mkdir /user/hadoop/hive/tmp    

3、更改配置conf/hive-default.xml

        <property>
          <name>hive.metastore.warehouse.dir</name>name>
          <value>/user/hadoop/hive/warehouse</value>value>
          <description>location of default database for the warehouse</description>description>
        </property>


        <property>
          <name>hive.exec.scratchdir</name>name>
          <value>/user/hadoop/hive/tmp</value>value>
          <description>Scratch space for Hive jobs</description>description>
        </property>

4、在bin/hive-config.sh 中添加变量

        export JAVA_HOME=/usr/local/webserver/jdk
        export HADOOP_HOME=/usr/local/webserver/hadoop
        export HADOOP_CONF_DIR=/usr/local/webserver/hadoop/conf
        export CLASSPATH=${CLASSPATH}:${HADOOP_CONF_DIR}

5、修改hadoop/conf/hadoop-env.sh

    export HADOOP_CLASSPATH=${HADOOP_CLASSPATH}:$HBASE_HOME/hbase-0.90.6.jar:$HBASE_HOME/lib/zookeeper-3.3.2.jar:$HBASE_HOME/conf
