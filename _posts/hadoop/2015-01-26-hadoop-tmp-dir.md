---
layout: post
category : hadoop
tags : [spark]
---
{% include JB/setup %}


最近跑hadoop任务的时候，老是把hadoop机器给跑死，今天监控了一下，发现是系统盘有大量的io,造成 ssh都很难联接上去。


lsof -p pid 查看相关进程号，发现大量的io在写 data盘（系统盘）

    /data/hadoop/nodemanager/usercache/www/appcache/application_1419224078120_3433/spark-local-20150126090224-e636/31/temp_shuffle_a8f61352-9907-4052-b507-374990bf9698


###yarn.nodemanager.local-dirs

    List of directories to store localized files in. An application's localized file directory will be found in: ${yarn.nodemanager.local-dirs}/usercache/${user}/appcache/application_${appid}. Individual containers' work directories, called container_${contid}, will be subdirectories of this.

每一个container都会有这样一个目录，这个目录下面会写很多的临时文件。

这个目录是支持多个盘的，用逗号分隔，把他从系统盘迁移开后，整个文件都会被分到多个盘上，效果非常不错。



###PID

    HADOOP_MAPRED_PID_DIR

    HADOOP_PID_DIR

这两个默认都是/tmp/目录，如果tmp清理了，你重启的时候，会报 no namenode to stop
