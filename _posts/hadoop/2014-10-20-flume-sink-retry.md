---
layout: post
category : hadoop
tags : [flume]
---
{% include JB/setup %}


在搞明白了Sink是怎么样load 具体的sink实现之后，然后发现 调用sink 的process的方法有点小意思。

看sink的代码如下：

    /*
     * Licensed to the Apache Software Foundation (ASF) under one
     * or more contributor license agreements.  See the NOTICE file
     * distributed with this work for additional information
     * regarding copyright ownership.  The ASF licenses this file
     * to you under the Apache License, Version 2.0 (the
     * "License"); you may not use this file except in compliance
     * with the License.  You may obtain a copy of the License at
     *
     * http://www.apache.org/licenses/LICENSE-2.0
     *
     * Unless required by applicable law or agreed to in writing,
     * software distributed under the License is distributed on an
     * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
     * KIND, either express or implied.  See the License for the
     * specific language governing permissions and limitations
     * under the License.
     */

    package org.apache.flume;

    import java.util.concurrent.atomic.AtomicBoolean;

    import org.apache.flume.lifecycle.LifecycleAware;
    import org.apache.flume.lifecycle.LifecycleState;
    import org.slf4j.Logger;
    import org.slf4j.LoggerFactory;

    /**
     * <p>
     * A driver for {@linkplain Sink sinks} that polls them, attempting to
     * {@linkplain Sink#process() process} events if any are available in the
     * {@link Channel}.
     * </p>
     *
     * <p>
     * Note that, unlike {@linkplain Source sources}, all sinks are polled.
     * </p>
     *
     * @see org.apache.flume.Sink
     * @see org.apache.flume.SourceRunner
     */
    public class SinkRunner implements LifecycleAware {

      private static final Logger logger = LoggerFactory
          .getLogger(SinkRunner.class);
      private static final long backoffSleepIncrement = 1000;
      private static final long maxBackoffSleep = 5000;

      private CounterGroup counterGroup;
      private PollingRunner runner;
      private Thread runnerThread;
      private LifecycleState lifecycleState;

      private SinkProcessor policy;

      public SinkRunner() {
        counterGroup = new CounterGroup();
        lifecycleState = LifecycleState.IDLE;
      }

      public SinkRunner(SinkProcessor policy) {
        this();
        setSink(policy);
      }

      public SinkProcessor getPolicy() {
        return policy;
      }

      public void setSink(SinkProcessor policy) {
        this.policy = policy;
      }

      @Override
      public void start() {
        SinkProcessor policy = getPolicy();

        policy.start();

        runner = new PollingRunner();

        runner.policy = policy;
        runner.counterGroup = counterGroup;
        runner.shouldStop = new AtomicBoolean();

        runnerThread = new Thread(runner);
        runnerThread.setName("SinkRunner-PollingRunner-" +
            policy.getClass().getSimpleName());
        runnerThread.start();

        lifecycleState = LifecycleState.START;
      }

      @Override
      public void stop() {

        if (runnerThread != null) {
          runner.shouldStop.set(true);
          runnerThread.interrupt();

          while (runnerThread.isAlive()) {
            try {
              logger.debug("Waiting for runner thread to exit");
              runnerThread.join(500);
            } catch (InterruptedException e) {
              logger
              .debug(
                  "Interrupted while waiting for runner thread to exit. Exception follows.",
                  e);
            }
          }
        }

        getPolicy().stop();
        lifecycleState = LifecycleState.STOP;
      }

      @Override
      public String toString() {
        return "SinkRunner: { policy:" + getPolicy() + " counterGroup:"
            + counterGroup + " }";
      }

      @Override
      public LifecycleState getLifecycleState() {
        return lifecycleState;
      }

      /**
       * {@link Runnable} that {@linkplain SinkProcessor#process() polls} a
       * {@link SinkProcessor} and manages event delivery notification,
       * {@link Sink.Status BACKOFF} delay handling, etc.
       */
      public static class PollingRunner implements Runnable {

        private SinkProcessor policy;
        private AtomicBoolean shouldStop;
        private CounterGroup counterGroup;

        @Override
        public void run() {
          logger.debug("Polling sink runner starting");

          while (!shouldStop.get()) {
            try {
              if (policy.process().equals(Sink.Status.BACKOFF)) {
                counterGroup.incrementAndGet("runner.backoffs");

                Thread.sleep(Math.min(
                    counterGroup.incrementAndGet("runner.backoffs.consecutive")
                    * backoffSleepIncrement, maxBackoffSleep));
              } else {
                counterGroup.set("runner.backoffs.consecutive", 0L);
              }
            } catch (InterruptedException e) {
              logger.debug("Interrupted while processing an event. Exiting.");
              counterGroup.incrementAndGet("runner.interruptions");
            } catch (Exception e) {
              logger.error("Unable to deliver event. Exception follows.", e);
              if (e instanceof EventDeliveryException) {
                counterGroup.incrementAndGet("runner.deliveryErrors");
              } else {
                counterGroup.incrementAndGet("runner.errors");
              }
              try {
                Thread.sleep(maxBackoffSleep);
              } catch (InterruptedException ex) {
                Thread.currentThread().interrupt();
              }
            }
          }
          logger.debug("Polling runner exiting. Metrics:{}", counterGroup);
        }

      }
    }

    组件会调用 SinkRunner的start 方法，而start 方法会调用 DefalutSinkProcess的start 方法。
    这样会初始整个具体的sink，比如AvroSink.

    然后SinkRunner会单独启动一个线程来处理（process）调用，这会有一个问题，如果channel特别小，或者网络延时大的话，单个线程的sink能处理的过来吗？
    这也是性能所在。

    看一下process的具体实现。

    **
   * {@link Runnable} that {@linkplain SinkProcessor#process() polls} a
   * {@link SinkProcessor} and manages event delivery notification,
   * {@link Sink.Status BACKOFF} delay handling, etc.
   */
  public static class PollingRunner implements Runnable {

    private SinkProcessor policy;
    private AtomicBoolean shouldStop;
    private CounterGroup counterGroup;

    @Override
    public void run() {
      logger.debug("Polling sink runner starting");

      while (!shouldStop.get()) {
        try {
          if (policy.process().equals(Sink.Status.BACKOFF)) {
            counterGroup.incrementAndGet("runner.backoffs");

            Thread.sleep(Math.min(
                counterGroup.incrementAndGet("runner.backoffs.consecutive")
                * backoffSleepIncrement, maxBackoffSleep));
          } else {
            counterGroup.set("runner.backoffs.consecutive", 0L);
          }
        } catch (InterruptedException e) {
          logger.debug("Interrupted while processing an event. Exiting.");
          counterGroup.incrementAndGet("runner.interruptions");
        } catch (Exception e) {
          logger.error("Unable to deliver event. Exception follows.", e);
          if (e instanceof EventDeliveryException) {
            counterGroup.incrementAndGet("runner.deliveryErrors");
          } else {
            counterGroup.incrementAndGet("runner.errors");
          }
          try {
            Thread.sleep(maxBackoffSleep);
          } catch (InterruptedException ex) {
            Thread.currentThread().interrupt();
          }
        }
      }
      logger.debug("Polling runner exiting. Metrics:{}", counterGroup);
    }

  }



    
