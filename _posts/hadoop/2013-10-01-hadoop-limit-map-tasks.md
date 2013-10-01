---
layout: post
category : hadoop
tags : [hadoop]
---
{% include JB/setup %}

一直想对 hadoop 自带的 CapacityScheduler 算法进行升级，因为那东东没法限制一个job占用的最大map数。
如这个 job 要 100 个 map tasks,如果你的队列里面有 100个 map slot，他会全占用，我想让他最多占用 4个map，那是没法实现的。

需求很简单，要更改的代码也是很简单，但是要找到在哪里更改，这就比较困难了。
花了两天时间，终于找到了在哪里更改。

主要是在 MapSchedulingMgr 中的 obtainNewTask 函数中进行更改


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
        //添加如下三行代码,如果大于 3个map 的slot，就不给他新的任务,不过最好放到配置文件中
        if (job.runningMaps()>3){
            return  TaskLookupResult.getNoTaskFoundResult();
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

