---
layout: post
category : elasticsearch 
tags : [elasticsearch]
---
{% include JB/setup %}




Elasticsearch有很多名词，发现有一个总结的非常不错，基本上没转过别人的文章，特意转一下，以下文章转自[http://prisoner.github.io/2014/04/04/ElasticSearch%E5%90%8D%E8%AF%8D%E8%A7%A3%E9%87%8A.html](http://prisoner.github.io/2014/04/04/ElasticSearch%E5%90%8D%E8%AF%8D%E8%A7%A3%E9%87%8A.html 'http://prisoner.github.io/2014/04/04/ElasticSearch%E5%90%8D%E8%AF%8D%E8%A7%A3%E9%87%8A.html')





##cluster

每个cluster由一个或多个节点组成,它们共享同一个集群名.每个cluster有一个被自动选出的master节点,当该master节点挂掉的时候会被自动替换.


##node

node是elasticsearch的运行实例.为了测试,多个node可以在同一台服务器上启动,但是通常一个服务器只放一个node. 系统启动时,node会使用广播(或指定的多播)来发现一个现有的cluster,并且试图加入该cluster.


##index

index有点像关系型数据库中的“database”,包含不同的type的schema映射. 一个index是一个逻辑上的命名空间,具有一个或多个primary shards,可以拥有零个或多个replia shards.


##shard

一个shard是一个单独的lucene实例,是被elasticsearch自动管理的底层工作单元.一个索引是包含primary或replia切片的逻辑命名空间. 除了需要定义primary shards和replia shards的数量以外,你不需要直接指定使用的shards,你的代码中只关心index就好. Elasticsearch在集群中分布所有的shards,并且在添加删除节点时,自动重新分配.


##primary shard

每个document都存储在一个单独的primary shard中.当为一个document建索引时,首先在primary shard上建立,然后在该primary shard的所有replica shards上面建. 默认的,每个索引有5个primary shards.你可以通过减少或增加primary shards的数量来伸缩你的索引能够接受的文档数量. 当索引创建以后,你不能够改变索引中primary shards的数量.


##replica shard

每个primary shard有零或多个repica shard,replica是primary的拷贝,有两个目的,

    1. 提高恢复能力：当primary挂掉时,replica可以变成primary
    2. 提高性能：get和search请求既可以由primary又可以由replica处理

默认的,每个primary有一个replica,但一个索引的replica的数量可以动态地调整.replica从不与它的primary在同一个node上启动.


##routing

当为某个document建立索引的时候,索引存储在某个primary shard上.该shard是通过哈希routing value选出来的.默认的,routing value通过document ID得到,或者当该文档有特定的父文档,从父文档的ID得到(这是为了保证子文档和父文档存储在相同的shard). 该value可以在建索引时指定,或者在mapping中通过routing field给定.


##recovery
代表数据恢复或叫数据重新分布, elasticsearch在有节点加入或退出时会根据机器的负载对索引分片进行重新分配, 挂掉的节点重新启动时也会进行数据恢复.

##river

代表elasticsearch的一个数据源, 也是其它存储方式(如：数据库)同步数据到elasticsearch的一个方法.它是以插件方式存在的一个elasticsearch服务, 通过读取river中的数据并把它索引到elasticsearch中, 官方的river有couchDB的, RabbitMQ的, Twitter的, Wikipedia的, river这个功能将会在后面的文件中重点说到.


##gateway

代表elasticsearch索引的持久化存储方式. elasticsearch默认是先把索引存放到内存中, 当内存满了时再持久化到硬盘. 当这个elasticsearch集群关闭再重新启动时就会从gateway中读取索引数据. elasticsearch支持多种类型的gateway, 有本地文件系统(默认), 分布式文件系统, Hadoop的HDFS和amazon的s3云存储服务.


##discovery.zen

代表elasticsearch的自动发现节点机制, elasticsearch是一个基于p2p的系统, 它先通过广播寻找存在的节点, 再通过多播协议来进行节点之间的通信, 同时也支持点对点的交互.
##Transport

代表elasticsearch内部节点或集群与客户端的交互方式, 默认内部是使用tcp协议进行交互, 同时它支持http协议(json格式)、thrift、servlet、memcached、zeroMQ等的传输协议(通过插件方式集成).


