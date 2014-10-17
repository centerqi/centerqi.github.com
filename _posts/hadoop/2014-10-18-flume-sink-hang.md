---
layout: post
category : hadoop
tags : [flume]
---
{% include JB/setup %}


Flume 使用过程中碰到一个很诡异的问题，就是当server端挂了后，agent不再给server端传送数据。

在跟进的过程中，发现server端启动后，agent和server端建立了一个 rpc的连接，连接建立后，就不再往后走了。




先来看正常启动的过程。

  2014-10-11 22:48:56,818 (lifecycleSupervisor-1-1) [DEBUG - org.apache.flume.api.NettyAvroRpcClient.configure(NettyAvroRpcClient.java:505)] Batch size string = 500
  2014-10-11 22:48:56,827 (lifecycleSupervisor-1-1) [WARN - org.apache.flume.api.NettyAvroRpcClient.configure(NettyAvroRpcClient.java:630)] Using default maxIOWorkers
    2014-10-11 22:48:57,518 (lifecycleSupervisor-1-1) [DEBUG - org.apache.flume.sink.AbstractRpcSink.createConnection(AbstractRpcSink.java:231)] Rpc sink k2: Created RpcClient: NettyAvroRpcClient { host: 10.2.8.11, port: 1463 }
    2014-10-11 22:48:57,518 (lifecycleSupervisor-1-1) [INFO - org.apache.flume.sink.AbstractRpcSink.start(AbstractRpcSink.java:303)] Rpc sink k2 started.
    2014-10-11 22:48:57,519 (SinkRunner-PollingRunner-DefaultSinkProcessor) [DEBUG - org.apache.flume.SinkRunner$PollingRunner.run(SinkRunner.java:143)] Polling sink runner starting


然后来看sink启动后，但是不传送数据的日志。

    2014-10-13 07:03:18,893 (SinkRunner-PollingRunner-DefaultSinkProcessor) [INFO - org.apache.flume.sink.AbstractRpcSink.createConnection(AbstractRpcSink.java:206)] Rpc sink k1: Building RpcClient with hostname: 10.2.8.11, port: 1463
    2014-10-13 07:03:18,893 (SinkRunner-PollingRunner-DefaultSinkProcessor) [INFO - org.apache.flume.sink.AvroSink.initializeRpcClient(AvroSink.java:126)] Attempting to create Avro Rpc client.
    2014-10-13 07:03:18,893 (SinkRunner-PollingRunner-DefaultSinkProcessor) [DEBUG - org.apache.flume.api.NettyAvroRpcClient.configure(NettyAvroRpcClient.java:505)] Batch size string = 1000
    2014-10-13 07:03:18,894 (SinkRunner-PollingRunner-DefaultSinkProcessor) [WARN - org.apache.flume.api.NettyAvroRpcClient.configure(NettyAvroRpcClient.java:630)] Using default maxIOWorkers
    2014-10-13 07:03:18,905 (SinkRunner-PollingRunner-DefaultSinkProcessor) [DEBUG - org.apache.flume.sink.AbstractRpcSink.createConnection(AbstractRpcSink.java:231)] Rpc sink k1: Created RpcClient: NettyAvroRpcClient { host: 10.2.8.11, port: 1463 }

从以上数据可以看出，`Rpc sink k2 started.` 创建完sink的连接后，理论上会打印此日志。


看一下AbstractRpcSink.java的  start 方法.


    /**
    * The start() of RpcSink is more of an optimization that allows connection
    * to be created before the process() loop is started. In case it so happens
    * that the start failed, the process() loop will itself attempt to reconnect
    * as necessary. This is the expected behavior since it is possible that the
    * downstream source becomes unavailable in the middle of the process loop
    * and the sink will have to retry the connection again.
    */
    @Override
    public void start() {
    logger.info("Starting {}...", this);
    sinkCounter.start();
    try {
      createConnection();
    } catch (FlumeException e) {
      logger.warn("Unable to create Rpc client using hostname: " + hostname
          + ", port: " + port, e);

      /* Try to prevent leaking resources. */
      destroyConnection();
    }

    super.start();

    logger.info("Rpc sink {} started.", getName());
    }

理论上是在调用super.start() 方法时出问题了。
AbstractRpcSink 的父类为 AbstractSink

看一下 AbstractSink的start 方法：

  @Override
  public synchronized void start() {
    Preconditions.checkState(channel != null, "No channel configured");

    lifecycleState = LifecycleState.START;
  }

  应该是channel出问题了，为什么在sink启动后，channel出问题了呢？ 
