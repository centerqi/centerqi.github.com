---
layout: post
category : hadoop 
tags : [hadoop]
---
{% include JB/setup %}

今天早上的报表没有出来,报表是从用 hadoop 进行跑的  
一看 hadoop 相关日志,报如下错误  

    Message: org.apache.hadoop.ipc.RemoteException: java.io.IOException: org.apache.hadoop.fs.FSError: java.io.IOException: No space left on device

咋一看, 以为是 hdfs没空间了,运行如下命令

        hadoop dfsadmin -report

        Configured Capacity: 44302785945600 (40.29 TB)
        Present Capacity: 42020351946752 (38.22 TB)
        DFS Remaining: 8124859072512 (7.39 TB)
        DFS Used: 33895492874240 (30.83 TB)
        DFS Used%: 80.66%
        Under replicated blocks: 1687
        Blocks with corrupt replicas: 0
        Missing blocks: 0

从输出的结果来看,dfs的空间还很大,所以"No space left on device"这样的错误应该不是hdfs的问题  
应该是本地磁盘的问题,发现hadoop运行时,要用到一个临时目录,可以在core-site.xml文件中配置  

    hadoop.tmp.dir

默认的路径是 /tmp/hadoop-${user.name}  
看了一下 /tmp/的空间,确实很小了，把hadoop.tmp.dir挂到一个大一点的盘上就可以了  
hadoop.tmp.dir是其它临时目录的父目录  
但是一个问题还没有解决, hadoop.tmp.dir到底需要多大空间呢？怎么样计算呢？  

[common-question-and-requests-from-our-users](http://blog.cloudera.com/blog/2009/05/common-questions-and-requests-from-our-users/ 'common-questions-and-requests-from-our-users')
