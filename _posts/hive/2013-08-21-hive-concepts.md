---
layout: post
category : hive 
tags : [hive]
---
{% include JB/setup %}

hive 有很多概念关系数据库还是不同的，这些概念没具体搞清楚，影响对hive的使用和理解。

####Buckets
buckets是为了增加并行，把文件分散开，以下引用阿里数据平台

>Buckets 对指定列计算 hash，根据 hash 值切分数据，目的是为了并行，每一个 Bucket 对应一个文件。
>将 user 列分散至 32 个 bucket，
>首先对 user 列的值计算 hash，对应 hash 值为 0 的 HDFS 目录为：/wh/pvs/ds=20090801/ctry=US/part-00000；
>hash 值为 20 的 HDFS 目录为：/wh/pvs/ds=20090801/ctry=US/part-00020

####Partition
>Partition 对应于数据库中的 Partition 列的密集索引，但是 Hive 中 Partition 的组织方式和数据库中的很不相同。
>在 Hive 中，表中的一个 Partition 对应于表下的一个目录，所有的 Partition 的数据都存储在对应的目录中。
>例如：pvs 表中包含 ds 和 city 两个 Partition，则对应于 ds = 20090801, ctry = US 的 HDFS 子目录为：/wh/pvs/ds=20090801/ctry=US；
>对应于 ds = 20090801, ctry = CA 的 HDFS 子目录为；/wh/pvs/ds=20090801/ctry=CA

####全局排序

    ORDER BY 

把所有的结果放到一个redcue中去排序，如果数据量大，理论上来说不现实

    SORT BY 
    
SORT BY 对每一个reduce中的数据进行排序(不是全局排序，是对各个reduce中的数据进行排序，不会整体有序)

>Hive supports SORT BY which sorts the data per reducer. 
>The difference between "order by" and "sort by" is that the former guarantees total order in the output 
>while the latter only guarantees ordering of the rows within a reducer. 
>If there are more than one reducer, "sort by" may give partially ordered final results.


    Distribute by

>All rows with the same Distribute By columns will go to the same reducer(保证相同的key去同一个reduce)

    Cluster by

>除了具有Distribute by的功能外，还会对该字段进行排序。因此，常常认为cluster by = distribute by + sort by

