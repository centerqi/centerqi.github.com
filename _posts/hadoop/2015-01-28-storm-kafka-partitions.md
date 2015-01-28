---
layout: post
category : hadoop
tags : [storm]
---
{% include JB/setup %}


在storm中，用kafka做为Spout，如果一个topic有多个分区，如果用静态broker连接方式，一定要把所有分区都加上。

    val hostsAndPartitions:GlobalPartitionInformation = new GlobalPartitionInformation();
    hostsAndPartitions.addPartition(0, new Broker("10.1.13.170", 9092));
    hostsAndPartitions.addPartition(1, new Broker("10.1.13.170", 9092));
    hostsAndPartitions.addPartition(2, new Broker("10.1.13.170", 9092));

分区的下标是从 0 开始的，如有三个分区，那就得增加 3个分区。


###Relationship between Spout parallelism and number of kafka partitions

这个问题也是我关注的地方就在这，

    val builder:TopologyBuilder  = new TopologyBuilder();
    builder.setSpout("spout", new KafkaSpout(kafkaConfig), 3);
    

如果是单台机器，3个分区，那最大并发数应该是 3

参考

[storm-kafka-multiple-spouts-how-to-share-the-load](http://stackoverflow.com/questions/18267834/storm-kafka-multiple-spouts-how-to-share-the-load 'http://stackoverflow.com/questions/18267834/storm-kafka-multiple-spouts-how-to-share-the-load') 

[Relationship between Spout parallelism and number of kafka partitions](https://groups.google.com/forum/#!topic/storm-user/mBA1e6Y1MYY 'https://groups.google.com/forum/#!topic/storm-user/mBA1e6Y1MYY')

