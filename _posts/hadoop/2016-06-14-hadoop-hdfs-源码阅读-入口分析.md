---
layout: post
category : hadoop
tags : [hdfs]
---
{% include JB/setup %}



一年前把mr的源码读了一次，16年已经过去一半，年前计划把hdfs和spark的代码好好阅读一次，一直没有落实。

今天重新计划了一下，每天读一点，如hdfs的读写实现，namenode,datanode的实现方式，每周读几个小时，积累下来应该可以把hdfs的实现读完。

读任何代码都要先了解期大体架构，然后找入口，一点一点切入。

hdfs的大体架构网上已经有大把的文章，并且读过 google那几篇论文，基本上知道hdfs的实现原理了。

常用的hdfs操作有启动namenode,datanode,常规的文件操作，如读写hdfs，查看文件内容之类的。


hadoop有一个sbin目录，还有一个bin目录。


执行查看sbin目录命令

	ls -al sbin

输出

	distribute-exclude.sh
	hadoop-daemon.sh
	hadoop-daemons.sh
	hdfs-config.cmd
	hdfs-config.sh
	httpfs.sh
	mr-jobhistory-daemon.sh
	refresh-namenodes.sh
	slaves.sh
	start-all.cmd
	start-all.sh
	start-balancer.sh
	start-dfs.cmd
	start-dfs.sh
	start-secure-dns.sh
	start-yarn.cmd
	start-yarn.sh
	stop-all.cmd
	stop-all.sh
	stop-balancer.sh
	stop-dfs.cmd
	stop-dfs.sh
	stop-secure-dns.sh
	stop-yarn.cmd
	stop-yarn.sh
	yarn-daemon.sh
	yarn-daemons.sh

执行查看bin目录

	ls bin -al


输出

	container-executor
	hadoop
	hadoop.cmd
	hdfs
	hdfs.cmd
	mapred
	mapred.cmd
	rcc
	test-container-executor
	yarn
	yarn.cmd



如要启动hdfs，一般会执行。

	start-dfs.sh

1. start-dfs.sh 会调用 hadoop-daemon.sh。

2. hadoop-daemon.sh 会根据不同的命令调用, 如果是hdfs的相关操作，会调用bin/hdfs。

3. 在bin/hdfs 脚本中，会根据不同的命令，调用java的class。



if [ "$COMMAND" = "namenode" ] ; then
  CLASS='org.apache.hadoop.hdfs.server.namenode.NameNode'
  HADOOP_OPTS="$HADOOP_OPTS $HADOOP_NAMENODE_OPTS"
elif [ "$COMMAND" = "zkfc" ] ; then
  CLASS='org.apache.hadoop.hdfs.tools.DFSZKFailoverController'
  HADOOP_OPTS="$HADOOP_OPTS $HADOOP_ZKFC_OPTS"
elif [ "$COMMAND" = "secondarynamenode" ] ; then
  CLASS='org.apache.hadoop.hdfs.server.namenode.SecondaryNameNode'
  HADOOP_OPTS="$HADOOP_OPTS $HADOOP_SECONDARYNAMENODE_OPTS"
elif [ "$COMMAND" = "datanode" ] ; then
  CLASS='org.apache.hadoop.hdfs.server.datanode.DataNode'
  if [ "$starting_secure_dn" = "true" ]; then
    HADOOP_OPTS="$HADOOP_OPTS -jvm server $HADOOP_DATANODE_OPTS"
  else
    HADOOP_OPTS="$HADOOP_OPTS -server $HADOOP_DATANODE_OPTS"
  fi
elif [ "$COMMAND" = "journalnode" ] ; then
  CLASS='org.apache.hadoop.hdfs.qjournal.server.JournalNode'
  HADOOP_OPTS="$HADOOP_OPTS $HADOOP_JOURNALNODE_OPTS"
elif [ "$COMMAND" = "dfs" ] ; then
  CLASS=org.apache.hadoop.fs.FsShell
  HADOOP_OPTS="$HADOOP_OPTS $HADOOP_CLIENT_OPTS"
elif [ "$COMMAND" = "dfsadmin" ] ; then
  CLASS=org.apache.hadoop.hdfs.tools.DFSAdmin
  HADOOP_OPTS="$HADOOP_OPTS $HADOOP_CLIENT_OPTS"
elif [ "$COMMAND" = "haadmin" ] ; then
  CLASS=org.apache.hadoop.hdfs.tools.DFSHAAdmin
  CLASSPATH=${CLASSPATH}:${TOOL_PATH}
  HADOOP_OPTS="$HADOOP_OPTS $HADOOP_CLIENT_OPTS"
elif [ "$COMMAND" = "fsck" ] ; then
  CLASS=org.apache.hadoop.hdfs.tools.DFSck


这样就可以找到每一个命令的入口，也就找到了main方法，一个一个去阅读其实现。


