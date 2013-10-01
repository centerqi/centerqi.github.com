---
layout: post
category : hadoop
tags : [hadoop]
---
{% include JB/setup %}

上次找到了怎么样去限制一个job 占用的 max slot,但是那直接写死的代码里，基本上是不现实的，要做到可配置。

只要添加如下代码，就可以在每一个hadoop 的  job中进行限制了

    JobConf jobConf = job.getJobConf();
    int limitMapSlots = jobConf.getInt("mapred.limit.map.slots",0);
    LOG.info("limitMapSlots="+limitMapSlots);
    LOG.info("desiredMaps = "+job.desiredMaps()+" runningMaps = "+job.runningMaps());
      if (limitMapSlots >= 1){
                 if (job.runningMaps() > limitMapSlots ){
                    return  TaskLookupResult.getNoTaskFoundResult();
                 }
          }



首先得到 jobConf 

然后取得 mapred.limit.map.slots 的值,默认为0 

最后确认，是否用户设置了 mapred.limit.map.slots ，如果设置了，就去检查是否现在运行的 map slots超过了 mapred.limit.map.slots


完整代码

    /**
    * The scheduling algorithms for map tasks. 
    */
    private static class MapSchedulingMgr extends TaskSchedulingMgr {

    MapSchedulingMgr(CapacityTaskScheduler schedulr) {
      super(schedulr);
      type = TaskType.MAP;
      queueComparator = mapComparator;
    }

    @Override
    TaskLookupResult obtainNewTask(TaskTrackerStatus taskTracker,
                                   JobInProgress job, boolean assignOffSwitch)
    throws IOException {

    JobConf jobConf = job.getJobConf();
    int limitMapSlots = jobConf.getInt("mapred.limit.map.slots",0);
    LOG.info("limitMapSlots="+limitMapSlots);
    LOG.info("desiredMaps = "+job.desiredMaps()+" runningMaps = "+job.runningMaps());
    if (limitMapSlots >= 1){
                 if (job.runningMaps() > limitMapSlots ){
                    return  TaskLookupResult.getNoTaskFoundResult();
                 }
          }

  
      ClusterStatus clusterStatus =
        scheduler.taskTrackerManager.getClusterStatus();
      int numTaskTrackers = clusterStatus.getTaskTrackers();
      int numUniqueHosts = scheduler.taskTrackerManager.getNumberOfUniqueHosts();

      // Inform the job it is about to get a scheduling opportunity
      job.schedulingOpportunity();

      // First, try to get a 'local' task
      Task t = job.obtainNewNodeOrRackLocalMapTask(taskTracker,
                                                   numTaskTrackers,
                                                   numUniqueHosts);

      if (t != null) {
        return TaskLookupResult.getTaskFoundResult(t, job);
      }

        // Next, try to get an 'off-switch' task if appropriate
      // Do not bother as much about locality for High-RAM jobs
      if (job.getNumSlotsPerMap() > 1 ||
          (assignOffSwitch &&
              job.scheduleOffSwitch(numTaskTrackers))) {
        t =
          job.obtainNewNonLocalMapTask(taskTracker, numTaskTrackers, numUniqueHosts);
      }

      return (t != null) ?
          TaskLookupResult.getOffSwitchTaskFoundResult(t, job) :
          TaskLookupResult.getNoTaskFoundResult();
    }

    @Override
    int getClusterCapacity() {
      return scheduler.taskTrackerManager.getClusterStatus().getMaxMapTasks();
    }

    @Override
    int getRunningTasks(JobInProgress job) {
      return job.runningMaps();
    }

    @Override
    int getPendingTasks(JobInProgress job) {
      return job.pendingMaps();
    }

    @Override
    int getSlotsPerTask(JobInProgress job) {
      return job.getNumSlotsPerTask(TaskType.MAP);
    }

    int getNumReservedTaskTrackers(JobInProgress job) {
      return job.getNumReservedTaskTrackersForMaps();
    }

    @Override
    boolean hasSpeculativeTask(JobInProgress job, TaskTrackerStatus tts) {
      //Check if job supports speculative map execution first then 
      //check if job has speculative maps.
      return (job.getMapSpeculativeExecution())&& (
          hasSpeculativeTask(job.getTasks(TaskType.MAP),
              job.getStatus().mapProgress(), tts));
    }
    }

