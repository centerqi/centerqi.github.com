---
layout: post
category : hadoop
tags : [spark]
---
{% include JB/setup %}


最近一直在调研流式计算的一些工具，在用spark＋flume的时候，碰到各种问题，特意了解了一下 zeromq+spark.

##spark zeromq streaming用的是 发布-订阅型连接

为了证明这一点，我特意翻了一下spark zeromq streaming的源代码。


    package org.apache.spark.streaming.zeromq

    import scala.reflect.ClassTag

    import akka.actor.Actor
    import akka.util.ByteString
    import akka.zeromq._

    import org.apache.spark.Logging
    import org.apache.spark.streaming.receiver.ActorHelper

    /**
     * A receiver to subscribe to ZeroMQ stream.
     */
    private[streaming] class ZeroMQReceiver[T: ClassTag](publisherUrl: String,
      subscribe: Subscribe,
      bytesToObjects: Seq[ByteString] => Iterator[T])
      extends Actor with ActorHelper with Logging {

      override def preStart() = ZeroMQExtension(context.system)
        .newSocket(SocketType.Sub, Listener(self), Connect(publisherUrl), subscribe)

      def receive: Receive = {

        case Connecting => logInfo("connecting ...")

        case m: ZMQMessage =>
          logDebug("Received message for:" + m.frame(0))

          // We ignore first frame for processing as it is the topic
          val bytes = m.frames.tail
          store(bytesToObjects(bytes))

        case Closed => logInfo("received closed ")
      }
    }

最重要的实现是在如下:

    ZeroMQExtension(context.system).newSocket(SocketType.Sub, Listener(self), Connect(publisherUrl), subscribe)

其实akka中有对zeromq的具体封装
[akka zeromq](http://www.gtan.com/akka_doc/scala/zeromq.html 'http://www.gtan.com/akka_doc/scala/zeromq.html')

[akka zeromq](http://doc.akka.io/docs/akka/snapshot/scala/zeromq.html 'http://doc.akka.io/docs/akka/snapshot/scala/zeromq.html')