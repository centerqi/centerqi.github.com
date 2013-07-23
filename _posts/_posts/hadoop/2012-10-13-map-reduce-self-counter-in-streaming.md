---
layout: post
category : hadoop
tags : [hadoop]
---
{% include JB/setup %}

计数器真是一个好东西，可以让你实时看到一些信息，用来优化你的代码是非常有用的。


如你在shell下执行 hadoop job -status jobid


其中jobid为你要查看哪一个具体的job的id。


可以看到如下的一些信息。


        Job: job_201207181140_6578
        file: hdfs://idc01-thd-ns-01:9000/user/hadoop/.staging/job_201207181140_6578/job.xml
        tracking URL: http://idc01-thd-ns-01:50030/jobdetails.jsp?jobid=job_201207181140_6578
        map() completion: 1.0
        reduce() completion: 1.0

        Counters: 31
                Job Counters
                        Launched reduce tasks=5
                        SLOTS_MILLIS_MAPS=14139776
                        Total time spent by all reduces waiting after reserving slots (ms)=0
                        Total time spent by all maps waiting after reserving slots (ms)=0
                        Rack-local map tasks=124
                        Launched map tasks=130
                        Data-local map tasks=6
                        SLOTS_MILLIS_REDUCES=1377402
                File Input Format Counters
                        Bytes Read=32778889030
                File Output Format Counters
                        Bytes Written=197
                FileSystemCounters
                        FILE_BYTES_READ=112554354
                        HDFS_BYTES_READ=32778907919
                        FILE_BYTES_WRITTEN=183986649
                        HDFS_BYTES_WRITTEN=197
                Map-Reduce Framework
                        Map output materialized bytes=70236744
                        Map input records=90014403
                        Reduce shuffle bytes=69660305
                        Spilled Records=180436467
                        Map output bytes=2653271412
                        Total committed heap usage (bytes)=114300551168
                        CPU time spent (ms)=18796870
                        Map input bytes=129602444434
                        SPLIT_RAW_BYTES=18881
                        Combine input records=0
                        Reduce input records=60301623
                        Reduce input groups=418480
                        Combine output records=0
                        Physical memory (bytes) snapshot=66001813504
                        Reduce output records=28
                        Virtual memory (bytes) snapshot=482626166784
                        Map output records=60301623

很明显的看到，这些是分组的来显示的。

在streaming中要添加计数器非常简单，感觉比用java根据他的类关系去写是很麻烦的。


但是他对格式是有基本要求的。  

        reporter:counter:group,counter,amount

如在php中  
        echo "reporter:counter:Temperature,Missing,1\n".PHP_EOL;

reporter:counter是固定的。 


Temperature是group名，如Map-Reduce Framework。


Missing是计数器名


1是计算的值




