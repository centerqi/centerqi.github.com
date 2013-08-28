---
layout: post
category : hive 
tags : [hive]
---
{% include JB/setup %}

hive对数据的读写，都是基于 hadoop 的 InputFormat api 和 OutputFormat api来完成的。

如果hadoop 的job 是io密集型的，那么对数据进行压缩，可以提高网络的传输性能。

如果是cpu bound 类型的job,可能导致性能下降。

###查看hadoop 支持的压缩类型

    hive -e "set io.compression.codecs"
    io.compression.codecs=org.apache.hadoop.io.compress.GzipCodec,
    org.apache.hadoop.io.compress.DefaultCodec,
    org.apache.hadoop.io.compress.BZip2Codec,
    org.apache.hadoop.io.compress.SnappyCodec

###压缩类型选择

1. Bzip 和 Gzip有效好的压缩比，但是cpu消耗会过高，如果追求磁盘空间，可以用Bzip或者Gzip

2. Lzo和Snappy压缩比较差，但是速度效快。

3. 另外一个重要的考虑是压缩文件是否支持拆分。
    MapReduce 经常会去把一个大文件分拆成几个小文件(经常每一个小文件往往是 64MB的倍数),这样每一个map就可以单独去处理被分拆的文件。
    这要求压缩文件提供完整的边界，但是Gzip,Snappy是不支持的，而Bzip,Lzo是支持的，从而可以让hadoop知道这些文件的边界。

###中间结果进行压缩    

    <property>
    <name>hive.exec.compress.intermediate</name>
    <value>true</value>value>
    <description>This controls whether intermediate files produced by Hive between
    multiple map-reduce jobs are compressed. The compression codec and other options
    are determined from hadoop config variables mapred.output.compress* </description>
    </property>

    <property>
    <name>mapred.map.output.compression.codec</name>
    <value>org.apache.hadoop.io.compress.SnappyCodec</value>
    <description>This controls whether intermediate files produced by Hive
    Enabling Intermediate Compression | 147
    between multiple map-reduce jobs are compressed. The compression codec
    and other options are determined from hadoop config variables
    mapred.output.compress* </description>
    </property>

也可以在hive 的job里面进行设置

    SET hive.exec.compress.output=true;
    SET mapred.output.compression.codec=org.apache.hadoop.io.compress.SnappyCodec;
    SET mapred.output.compression.type=BLOCK;

经过测试，发现对某些job,对中间结果进行压缩，性能有 5%左右的提升

###对最终输出进行压缩
    <property>
    <name>hive.exec.compress.output</name>
    <value>false</value>value>
    <description>This controls whether the final outputs of a query
    (to a local/hdfs file or a Hive table) is compressed. The compression
    codec and other options are determined from hadoop config variables
    mapred.output.compress* </description>
    </property>
    
gzip压缩是一个不错的选择

    <property>
    <name>mapred.output.compression.codec</name>
    <value>org.apache.hadoop.io.compress.GzipCodec</value>
    <description>If the job outputs are compressed, how should they be compressed?
    </description>
    </property>

###sequence file

>The sequence file format supported by Hadoop breaks a file into blocks and then op-tionally compresses the blocks in a splittable way.

    CREATE TABLE a_sequence_file_table STORED AS SEQUENCEFILE;

sequence支持三种压缩选项
1. NONE
2. RECORD
3. BLOCK

BLOCK压缩是非常有效率并且支持拆分
    <property>
    <name>mapred.output.compression.type</name>
    <value>BLOCK</value>value>
    <description>If the job outputs are to compressed as SequenceFiles,
    how should they be compressed? Should be one of NONE, RECORD or BLOCK.
    </description>
    </property>


