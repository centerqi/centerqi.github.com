---
layout: post
category : hadoop
tags : [hadoop]
---
{% include JB/setup %}

###hadoop-env.sh
    本文件影响 hadoop jdk选项和demon jdk 选项  

###core-site.xml
    An XML file that specifies parameters relevant to all Hadoop daemons and clients.


###hdfs-site.xml
    An XML file that specifies parameters used by the HDFS daemons and clients.

###mapred-site.xml
    An XML file that specifies parameters used by the MapReduce daemons and cli-ents.

###log4j.properties
    A Java property file that contains all log configuration information.


###masters (optional)
    A newline separated list of machines that run the secondary namenode, used only
    by the start-*.shhelper scripts.

###slaves (optional)
    A newline separated list of machine names that run the datanode / tasktracker pair
    of daemons, used only by the start-*.shhelper scripts.

###fair-scheduler.xml (optional)
    The file used to specify the resource pools and settings for the Fair Scheduler task
    scheduler plugin for MapReduce.

###capacity-scheduler.xml (optional)
    The name of the file used to specify the queues and settings for the Capacity
    Scheduler task scheduler plugin for MapReduce.

###dfs.include (optional, conventional name)
    A newline separated list of machine names that are permitted to connect to the
    namenode.

###dfs.exclude (optional, conventional name)
    A newline separated list of machine names that are not permitted to connect to the
    namenode.

###hadoop-policy.xml
    An XML file that defines which users and / or groups are permitted to invoke
    specific RPC functions when communicating with Hadoop.

###mapred-queue-acls.xml
    An XML file that defines which users and / or groups are permitted to submit jobs
    to which MapReduce job queues.

###taskcontroller.cfg
    A Java property−style file that defines values used by the setuid  task-controller
    MapReduce helper program used when operating in secure mode.


