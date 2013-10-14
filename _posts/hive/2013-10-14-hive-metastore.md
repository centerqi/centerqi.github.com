---
layout: post
category : hive 
tags : [hive]
---
{% include JB/setup %}

hive 今天碰到一个问题，读不到metastore数据。 

仔细一看，这个问题还是很有意思的。

错误如下

    FAILED: Error in metadata: Unable to fetch table proxytmp
    FAILED: Execution Error, return code 1 from org.apache.hadoop.hive.ql.exec.DDLTask

一看这个错误，应该是读取 metadata有问题了，打开debug

    hive -hiveconf hive.root.logger=DEBUG,console  

再执行如下语句

    DESC  proxytm;

打出了错误了stack信息

    Nested Throwables StackTrace:
    java.sql.SQLException: null,  message from server: "Host '192.168.1.1' is blocked because of many connection errors; unblock with 'mysqladmin flush-hosts'"

一看就是连接错误过多，这台 host被 block了，可以用 mysqladmin flush-hosts解决

但这个应该是治标不治本的问题、几个方案选择

1、运行 mysqldmin flush-hosts
2、增加 max_connect_errors数 
    show variables like '%max_connection_errors%';
    set global max_connect_errors = 1000;

查了一下具体原因，是我的mysql 连接超时，把连接超时更改大一点就好了。

hive 的metastore有三种模式，搞了好久才明白这三种的区别

1、Embedded Mode 

    This is the default metastore deployment mode for CDH. In this mode the metastore uses a Derby database, and both the database and the metastore service run embedded in the main HiveServer process. Both are started for you when you start the HiveServer process. This mode requires the least amount of effort to configure, but it can support only one active user at a time and is not certified for production use.


2、Local Mode
    
    In this mode the Hive metastore service runs in the same process as the main HiveServer process, but the metastore database runs in a separate process, and can be on a separate host. The embedded metastore service communicates with the metastore database over JDBC.

3、Remote Mode

    In this mode the Hive metastore service runs in its own JVM process; HiveServer2, HCatalog, Cloudera Impala™, and other processes communicate with it via the Thrift network API (configured via the hive.metastore.uris property). The metastore service communicates with the metastore database over JDBC (configured via the javax.jdo.option.ConnectionURL property). The database, the HiveServer process, and the metastore service can all be on the same host, but running the HiveServer process on a separate host provides better availability and scalability.

The main advantage of Remote mode over Local mode is that Remote mode does not require the administrator to share JDBC login information for the metastore database with each Hive user. HCatalog requires this mode.


基本的配置

    <property>
      <name>hive.metastore.local</name>
      <value>true</value>
      <description>controls whether to connect to remove metastore server or open a new metastore server in Hive Client JVM</description>
    </property>

    <property>
      <name>javax.jdo.option.ConnectionURL</name>
      <value>jdbc:mysql://101.1.15.115:3309/hive2?createDatabaseIfNotExist=true</value>
      <description>JDBC connect string for a JDBC metastore</description>
    </property>

    <property>
      <name>javax.jdo.option.ConnectionDriverName</name>
      <value>com.mysql.jdbc.Driver</value>
      <description>Driver class name for a JDBC metastore</description>
    </property>
