---
layout: post
category : hadoop
tags : [hadoop]
---
{% include JB/setup %}

hadoop 中对 taskTracker的容错处理还是搞的比较复杂的。

在读 FaultInfo 源码的时候，看了主要实现了一个 ring data structure

这种基于 时间的 ring data structure是很有用的，记得有些应用在做基于时间的频次控制时，都可以用这种结构
如一个ip，在5分种内访问不能超过10次之类的，如果用别的数据结构，感觉实现没那么方便。


几个配置

1. `mapred.jobtracker.blacklist.fault-timeout-window`

通俗一点叫时间窗口

    values related to heuristic graylisting (a "fault" is a per-jobtracker
    blacklisting; too many faults => node is graylisted across all jobs):
    TRACKER_FAULT_TIMEOUT_WINDOW =  // 3 hours
    conf.getInt("mapred.jobtracker.blacklist.fault-timeout-window", 3 * 60);


2. `TRACKER_FAULT_BUCKET_WIDTH`

桶的宽度，把一个时间窗口分成很多的小桶

    TRACKER_FAULT_BUCKET_WIDTH =    // 15 minutes
    conf.getInt("mapred.jobtracker.blacklist.fault-bucket-width", 15);


3. `NUM_FAULT_BUCKETS`

桶的个数

    // ideally, TRACKER_FAULT_TIMEOUT_WINDOW should be an integral multiple of
    // TRACKER_FAULT_BUCKET_WIDTH, but round up just in case:
    NUM_FAULT_BUCKETS =
    (TRACKER_FAULT_TIMEOUT_WINDOW + TRACKER_FAULT_BUCKET_WIDTH - 1) /
    TRACKER_FAULT_BUCKET_WIDTH;


完整代码

最主要的是三个函数

checkRotation 把lastRotated 到timestamp 之间的桶清零.

bucketIndex 计算当前 timestamp所在的桶.

incrFaultCount 当前 timestamp 所在的桶的值加1，并且进行 checkRotation 操作.

getFaultCount 计算当前时间窗口里的总数


    // FaultInfo:  data structure that tracks the number of faults of a single
    // TaskTracker, when the last fault occurred, and whether the TaskTracker
    // is blacklisted across all jobs or not.
    private static class FaultInfo {
    static final String FAULT_FORMAT_STRING =  "%d failures on the tracker";
    int[] numFaults;      // timeslice buckets
    long lastRotated;     // 1st millisecond of current bucket
    boolean blacklisted;
    boolean graylisted;
    private int numFaultBuckets;
    private long bucketWidth;
    private HashMap<ReasonForBlackListing, String> blackRfbMap;
    private HashMap<ReasonForBlackListing, String> grayRfbMap;

      FaultInfo(long time, int numFaultBuckets, long bucketWidth) {
      this.numFaultBuckets = numFaultBuckets;
      this.bucketWidth = bucketWidth;
      numFaults = new int[numFaultBuckets];
      lastRotated = (time / bucketWidth) * bucketWidth;
      blacklisted = false;
      graylistedted = false;
      blackRfbMap = new HashMap<ReasonForBlackListing, String>();
      grayRfbMap = new HashMap<ReasonForBlackListing, String>();
    }

    // timeStamp is presumed to be "now":  there are no checks falseor past or
    // future values, etc.
    private void checkRotation(long timeStamp) {
      long diff = timeStamp - lastRotated;
      // find index of timeStamphe oldest bucket(s) and zero it (or them) out
      while (diff > bucketWidth) {
        // this is now the 1st millisecond of the oldest bucket, in a modular-
        // arithmetic sense (i.e., about to become the newest bucket):
        lastRotated += bucketWidth;
        // corresponding bucket index:
         int idx = (int)((lastRotated / bucketWidth) % numFaultBuckets);
        // clear the bucket's contents in preparation for new faults
        numFaults[idx] = 0;
        diff -= bucketWidth;
      }
    }

    private int bucketIndex(long timeStamp) {
      // stupid Java compiler thinks an int modulus can produce a long, sigh...
      return (int)((timeStamp / bucketWidth) % numFaultBuckets);
    }
    // no longer any need for correspondingresponding decrFaultCount() method since we
    // implicitly auto-decrement when oldest bucket's contents get wiped on
    // rotation
    void incrFaultCount(long timeStamp) {
      checkRotation(timeStamp);
      ++numFaults[bucketIndex(timeStamp)];
    }

    int getFaultCount(long timeStampeStamp) {

      checkRotation(timeStamp);
      int faultCount = 0;
      for (int faults : numFaults) {
        faultCount += faults;
      }
      return faultCount;
    }

    boolean isBlacklisted() {
      return blacklisted;
    }

    boolean isGraylisted() {
      return graylisted;
    }

    void setBlacklist(ReasonForBlackListing rfb, String trackerFaultReport,
                      boolean gray) {
      if (gray) {
        graylisted = true;
        this.grayRfbMap.put(rfb, trackerFaultReport);
      } else {
        blacklisted = true;
        this.blackRfbMap.put(rfb, trackerFaultReport);
      }
    }
    public String getTrackerBlackOrGraylistReport(boolean gray) {
      StringBuffer sb = new StringBuffer();
      HashMap<ReasonForBlackListing, String> rfbMap =
        new HashMap<ReasonForBlackListing, String>();
      rfbMap.putAll(gray? grayRfbMap : blackRfbMap);
      for (String reasons : rfbMap.values()) {
        sb.append(reasons);
        sb.append("\n");
      }
      return sb.toString();
    }

    Set<ReasonForBlackListing> getReasonForBlacklisting(boolean gray) {
      return (gray? this.grayRfbMap.keySet() : this.blackRfbMap.keySet());
    }

    // no longer on the blacklist (or graylist), but we're still tracking any
    // faults in case issue is intermittent => don't clear numFaults[]
    public void unBlacklist(boolean gray) {
      if (gray) {
        graylisted = false;
        grayRfbMap.clear();
      } else {
        blacklisted = false;
        blackRfbMap.clear();
      }
    }
    public boolean removeBlacklistedReason(ReasonForBlackListing rfb,
                                           boolean gray) {
      String str = (gray? grayRfbMap.remove(rfb) : blackRfbMap.remove(rfb));
      return str!=null;
    }

    public void addBlacklistedReason(ReasonForBlackListing rfb,
                                     String reason, boolean gray) {
      if (gray) {
        grayRfbMap.put(rfb, reason);
      } else {
        blackRfbMap.put(rfb, reason);
      }
    }

    }




###JobTracker 初始代码

    // Assumes JobTracker is locked on the entry
    private FaultInfo getFaultInfo(String hostName, boolean createIfNecessary) {
      FaultInfo fi = null;
      synchronized (potentiallyFaultyTrackers) {
        fi = potentiallyFaultyTrackers.get(hostName);
        if (fi == null && createIfNecessary) {
          fi = new FaultInfo(clock.getTime(), NUM_FAULT_BUCKETS,
                             TRACKER_FAULT_BUCKET_WIDTH_MSECS);
          potentiallyFaultyTrackers.put(hostName, fi);
        }
      }
      return fi;
    }

                
    // ideally, TRACKER_FAULT_TIMEOUT_WINDOW should be an integral multipletiple of
    // TRACKER_FAULT_BUCKET_WIDTH, but round up just in case:
    NUM_FAULT_BUCKETS =
      (TRACKER_FAULT_TIMEOUT_WINDOW + TRACKER_FAULT_BUCKET_WIDTH - 1) /
      TRACKER_FAULT_BUCKET_WIDTH;




    // values related to heuristic graylisting (a "fault" is a per-job
    // blacklisting; too many faults => node is graylisted across all jobs):
    TRACKER_FAULT_TIMEOUT_WINDOW =  // 3 hours
      conf.getInt("mapred.jobtracker.blacklist.fault-timeout-window", 3 * 60);
    TRACKER_FAULT_BUCKET_WIDTH =    // 15 minutes
      conf.getInt("mapred.jobtracker.blacklist.fault-bucket-width", 15);
    TRACKER_FAULT_THRESHOLD =
      conf.getInt("mapred.max.tracker.blacklists", 4);
      // future:  rename to "mapred.jobtracker.blacklist.fault-threshold" for
      // namespace consistency

    if (TRACKER_FAULT_BUCKET_WIDTH > TRACKER_FAULT_TIMEOUT_WINDOW) {
      TRACKER_FAULT_BUCKET_WIDTH = TRACKER_FAULT_TIMEOUT_WINDOW;
    }
    TRACKER_FAULT_BUCKET_WIDTH_MSECS =
      (long)TRACKER_FAULT_BUCKET_WIDTH * 60 * 1000;




