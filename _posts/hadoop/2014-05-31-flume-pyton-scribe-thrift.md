---
layout: post
category : hadoop
tags : [flume]
---
{% include JB/setup %}

Flume的Source完美兼容scribe，也就是用发往scribe 的数据，完全可以发往 flume.

因为发往Scribe 的数据，一般都是通过thrift协议发送的。

配置 flume 的source如下

    scribe_agent.sources = scribesource
    scribe_agent.channels = fileChannel
    scribe_agent.sinks = avro-forward-res

    scribe_agent.sources.scribesource.type=org.apache.flume.source.scribe.ScribeSource
    scribe_agent.sources.scribesource.channels=fileChannel
    scribe_agent.sources.scribesource.workerThreads=5
    scribe_agent.sources.scribesource.port=14644


    scribe_agent.sinks.avro-forward-res.type=avro
    scribe_agent.sinks.avro-forward-res.hostname=10.1.15.199
    scribe_agent.sinks.avro-forward-res.port=60001
    scribe_agent.sinks.avro-forward-res.channel = fileChannel 

    scribe_agent.channels.fileChannel.type=file
    scribe_agent.channels.fileChannel.checkpointDir=/home/huqizhong/apache-flume-1.5.0-bin/data/c
    scribe_agent.channels.fileChannel.dataDirs=/home/huqizhong/apache-flume-1.5.0-bin/data/d

配置了一个source,监听 14644端口，用scribesource,并且把此数据转发到 10.1.15.199机器上，channel 为file.


用python写了一个简单的脚本，监控文件。

    import sys
    sys.path.append('./gen-py')

    from scribe import scribe
    from thrift.transport import TTransport, TSocket
    from thrift.protocol import TBinaryProtocol

    socket = TSocket.TSocket(host="10.1.15.84", port=14644)
    transport = TTransport.TFramedTransport(socket)
    protocol = TBinaryProtocol.TBinaryProtocol(trans=transport, strictRead=False, strictWrite=False)
    client = scribe.Client(protocol)
    transport.open()


[python scribe](http://blog.csdn.net/jiedushi/article/details/7968152 'python scribe')


