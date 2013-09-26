---
layout: post
category : hadoop
tags : [hadoop]
---
{% include JB/setup %}

Hadoop Jobtracer 与 Tasktracer的心跳间隔一直没怎么搞明白。

特意看了一下源代码，并且自己设置了各个变量，用 EXCEL画了图

具体代码


    public int getNextHeartbeatInterval() {
    // get the no of task trackers
    int clusterSize = getClusterStatus().getTaskTrackers();
    int heartbeatInterval =  Math.max(
                                (int)(1000 * HEARTBEATS_SCALING_FACTOR *
                                      ((double)clusterSize /
                                                NUM_HEARTBEATS_IN_SECOND)),
                                HEARTBEAT_INTERVAL_MIN) ;
    return heartbeatInterval;
    }


各个变量

    // Scaling factor for heartbeats, used for testing only
    static final String JT_HEARTBEATS_SCALING_FACTOR =
    "mapreduce.jobtracker.heartbeats.scaling.factor";
    private float HEARTBEATS_SCALING_FACTOR;
    private final float MIN_HEARTBEATS_SCALING_FACTOR = 0.01f;
    private final float DEFAULT_HEARTBEATS_SCALING_FACTOR = 1.0f;

      
      
    HEARTBEATS_SCALING_FACTOR =
      conf.getFloat(JT_HEARTBEATS_SCALING_FACTOR,
                    DEFAULT_HEARTBEATS_SCALING_FACTOR);
    if (HEARTBEATS_SCALING_FACTOR < MIN_HEARTBEATS_SCALING_FACTOR) {
      HEARTBEATS_SCALING_FACTOR = DEFAULT_HEARTBEATS_SCALING_FACTOR;
    }

    
    
    
    // Approximate number of heartbeats that could arrive JobTracker
      // in a second
    static final String JT_HEARTBEATS_IN_SECOND = "mapred.heartbeats.in.second";
    private int NUM_HEARTBEATS_IN_SECOND;
    private static final int DEFAULT_NUM_HEARTBEATS_IN_SECOND = 100;
    private static final int MIN_NUM_HEARTBEATS_IN_SECOND = 1;


    NUM_HEARTBEATS_IN_SECOND =
      conf.getInt(JT_HEARTBEATS_IN_SECOND, DEFAULT_NUM_HEARTBEATS_IN_SECOND);
    if (NUM_HEARTBEATS_IN_SECOND < MIN_NUM_HEARTBEATS_IN_SECOND) {
      NUM_HEARTBEATS_IN_SECOND = DEFAULT_NUM_HEARTBEATS_IN_SECOND;
    }


    int HEARTBEAT_INTERVAL_MIN = 300;
