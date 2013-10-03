---
layout: post
category : hadoop
tags : [hadoop]
---
{% include JB/setup %}

几个配置


1. mapred.tasktracker.expiry.interval

Default:600000(10 * 60 * 1000) 默认为10分钟

Expert: The time-interval, in miliseconds, after which a tasktracker is declared 'lost' if it doesn't send heartbeats.

2. mapred.max.tracker.failures

Default:4

Expert:The number of task-failures on a tasktracker of a given job after which new tasks of that job aren't assigned to it.


3. mapred.max.tracker.blacklists

Default:4

TRACKER_FAULT_THRESHOLD

Expert:The number of blacklists for a taskTracker by various jobs after which the task tracker could be blacklisted across all jobs. 

The tracker will be given a tasks later (after a day). The tracker will become a healthy tracker after a restart.

此tracker 被加入job blacklist的次数，也就是有多少个job把此tracker 加入了blacklist

4. mapred.cluster.average.blacklist.threshold

Default:0.5

AVERAGE_BLACKLIST_THRESHOLD

expert:number of faults (per-job blacklistings) for given node is more

than (1 + AVERAGE_BLACKLIST_THRESHOLD) times the average number

of faults across all nodes (configurable)


>JobTracker 将记录每个TaskTracker 被作业加入黑名单的次数 #blacklist。当某个TaskTracker 同时满足以下条件时，将被加入 JobTracker的灰名单中。

>1. #blacklist 大小超过了 mapred.max.tracker.blacklists

>2. 该 TaskTracker的 #blacklists大小超过了所有 TaskTracker的 #blacklist的平均值的 mapred.cluster.average.blacklist.threshold倍

>3. 当前灰名单中TaskTracker的数目小于所有TaskTracker数目的 50% 

以上引自<<深入解析Mapreduce架构设计与实现原理>>

不过第一条应该是有一点问题，不一定要超过，应该想等也是可以的。

我还是喜欢读源代码，如是看了一下代码，基本上是弄明白了。

    /**
     * Graylists the tracker across all jobs (similar to blacklisting except
     * not actually removed from service) if all of the following heuristics
     * hold:
     * <ol>
     * <li>number of faults within TRACKER_FAULT_TIMEOUT_WINDOW is greater
     *     than or equal to TRACKER_FAULT_THRESHOLD (per-job blacklistings)
     *     (both configurable)</li>li>
     * <li>number of faults (per-job blacklistings) for given node is more
     *     than (1 + AVERAGE_BLACKLIST_THRESHOLD) times the average number
     *     of faults across all nodes (configurable)</li>li>
     * <li>less than 50% of the cluster is blacklisted (NOT configurable)</li>li>
     * </ol>ol>
     * Note that the node health-check script is not explicitly limited by
     * the 50%-blacklist limit.
     */
    // this is the sole source of "heuristic blacklisting" == graylisting
    private boolean exceedsFaults(FaultInfo fi, long timeStamp) {
      int faultCount = fi.getFaultCount(timeStamp);
      if (faultCount >= TRACKER_FAULT_THRESHOLD) {
        // calculate average faults across all nodes
        long clusterSize = getClusterStatus().getTaskTrackers();
        long sum = 0; 
        for (FaultInfo f : potentiallyFaultyTrackers.values()) {
          sum += f.getFaultCount(timeStamp);
        }    
        double avg = (double) sum / clusterSize;   // avg num faults per node
        // graylisted trackers are already included in clusterSize:
        long totalCluster = clusterSize + numBlacklistedTrackers;
        if ((faultCount - avg) > (AVERAGE_BLACKLIST_THRESHOLD * avg) &&
            numGraylistedTrackers < (totalCluster * MAX_BLACKLIST_FRACTION)) {
          return true;
        }
      }
      return false;
    }


    /* The maximum fraction (range [0.0-1.0]) of nodes in cluster allowed to be
    // added to the all-jobs blacklist via heuristics.  By default, no more than
    // 50% of the cluster can be heuristically blacklisted, but the external
    // node-healthcheck script is not affected by this.
    */
    private static double MAX_BLACKLIST_FRACTION = 0.5;



[mapred-default](http://archive.cloudera.com/cdh/3/hadoop/mapred-default.html 'maprd-default')
