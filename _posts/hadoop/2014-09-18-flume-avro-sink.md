---
layout: post
category : hadoop
tags : [spark]
---
{% include JB/setup %}


Flume 在使用的过程中，遇到了不少问题，trklj是avro sink 握手失败后，此sink就不在工作了，这一点让人很难理解，特意看了一下源代码，证实了自己的想法。


查看flume日志，出现如下的报错信息:

    oyConnection(AbstractRpcSink.java:249)] Rpc sink k1 closing Rpc client: NettyAvroRpcClient { host: 10.1.1.1, port: 1463 }
    2014-08-28 22:55:36,264 (SinkRunner-PollingRunner-DefaultSinkProcessor) [ERROR - org.apache.flume.SinkRunner$PollingRunner.r
    un(SinkRunner.java:160)] Unable to deliver event. Exception follows.
    org.apache.flume.EventDeliveryException: Failed to send events
            at org.apache.flume.sink.AbstractRpcSink.process(AbstractRpcSink.java:392)
            at org.apache.flume.sink.DefaultSinkProcessor.process(DefaultSinkProcessor.java:68)
            at org.apache.flume.SinkRunner$PollingRunner.run(SinkRunner.java:147)
            at java.lang.Thread.run(Thread.java:722)
    Caused by: org.apache.flume.EventDeliveryException: NettyAvroRpcClient { host: 10.1.1.1, port: 146
    3 }: Failed to send batch
            at org.apache.flume.api.NettyAvroRpcClient.appendBatch(NettyAvroRpcClient.java:311)
            at org.apache.flume.sink.AbstractRpcSink.process(AbstractRpcSink.java:376)
            ... 3 more10.1.1.1, port: 146
    3 }: Handshake timed out after 30000ms
            at org.apache.flume.api.NettyAvroRpcClient.appendBatch(NettyAvroRpcClient.java:355)
            at org.apache.flume.api.NettyAvroRpcClient.appendBatch(NettyAvroRpcClient.java:299)
            ... 4 more
    Caused by: java.util.concurrent.TimeoutException
            at java.util.concurrent.FutureTask$Sync.innerGet(FutureTask.java:258)
            at java.util.concurrent.FutureTask.get(FutureTask.java:119)
            at org.apache.flume.api.NettyAvroRpcClient.appendBatch(NettyAvroRpcClient.java:353)
            ... 5 more

得到如下信息
基本信息是握手超时了，把超时更改大一点应该能解决问题，但是这不是最优的方案，比如进行多次retry，设置较小的 连接超时。

sink的逻辑基本如下：

###主要实现在 flume-ng-core目录下的org.apache.flume.sink包中


1. LifecycleAware->Sink->AbstractSink->AbstractRpcSink

Sink接口

    public void setChannel(Channel channel);
    public Channel getChannel();
    public Status process() throws EventDeliveryException;
    public static enum Status {
        READY, BACKOFF
    }


process上面有这样一段话:

    /**
     * <p>Requests the sink to attempt to consume data from attached channel</p>p>
     * <p><strong>Note</strong>strong>: This method should be consuming from the channel
     * within the bounds of a Transaction. On successful delivery, the transaction
     * should be committed, and on failure it should be rolled back.
     * @return READY if 1 or more Events were successfully delivered, BACKOFF if
     * no data could be retrieved from the channel feeding this sink
     * @throws EventDeliveryException In case of any kind of failure to
     * deliver data to the next hop destination.
     */

如果有失败，他会向下一个可传输的结点传输数据，而不会重试，如果是sinkgroups ，应该是没问题的，如果是AvroSink这样的，是会直接挂了。

AbstractRpcSink 主要调用 confgure、start、process、stop 四个方法。还有一个抽象方法  initializeRpcClient

start会调用initializeRpcClient 方法，具体实现的是哪一个Client.


如 AvroSink 的实现。 

    public class AvroSink extends AbstractRpcSink {

      private static final Logger logger = LoggerFactory.getLogger(AvroSink.class);

      @Override
      protected RpcClient initializeRpcClient(Properties props) {
        logger.info("Attempting to create Avro Rpc client.");
        return RpcClientFactory.getInstance(props);
      }
    }


有四个client:

FailoverRpcClient、LoadBalancingRpcClient、ThriftRpcClient、NettyAvroRpcClient.

默认为 NettyAvroRpcClient



###主要实现在 flume-ng-sdk目录下的org.apache.flume.api包中





[参考分析](http://blog.csdn.net/szwangdf/article/details/34098807 'http://blog.csdn.net/szwangdf/article/details/34098807')
