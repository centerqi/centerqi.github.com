---
layout: post
category : hadoop
tags : [hadoop]
---
{% include JB/setup %}

看文档，一定要继承一个类 TaskScheduler
他是一个抽象类

    /**
     * Used by a {@link JobTracker} to schedule {@link Task}s on
     * {@link TaskTracker}s.
     * <p>
     * {@link TaskScheduler}s typically use one or more
     * {@link JobInProgressListener}s to receive notifications about jobs.
     * <p>
     * It is the responsibility of the {@link TaskScheduler}
     * to initialize tasks for a job, by calling {@link JobInProgress#initTasks()}
     * between the job being added (when
     * {@link JobInProgressListener#jobAdded(JobInProgress)} is called)
     * and tasks for that job being assigned (by
     * {@link #assignTasks(TaskTracker)}).
     * @see EagerTaskInitializationListener
     */
    abstract class TaskScheduler implements Configurable {

      protected Configuration conf;
      protected TaskTrackerManager taskTrackerManager;
      
      public Configuration getConf() {
        return conf;
      }

      public void setConf(Configuration conf) {
        this.conf = conf;
      }

      public synchronized void setTaskTrackerManager(
          TaskTrackerManager taskTrackerManager) {
        this.taskTrackerManager = taskTrackerManager;
      }
      
      /**
       * Lifecycle method to allow the scheduler to start any work in separate
       * threads.
       * @throws IOException
       */
      public void start() throws IOException {
        // do nothing
      }
      
      /**
       * Lifecycle method to allow the scheduler to stop any work it is doing.
       * @throws IOException
       */
      public void terminate() throws IOException {
        // do nothing
      }

      /**
       * Returns the tasks we'd like the TaskTracker to execute right now.
       * 
       * @param taskTracker The TaskTracker for which we're looking for tasks.
       * @return A list of tasks to run on that TaskTracker, possibly empty.
       */
      public abstract List<Task> assignTasks(TaskTracker taskTracker)
      throws IOException;

      /**
       * Returns a collection of jobs in an order which is specific to 
       * the particular scheduler.
       * @param queueName
       * @return
       */
      public abstract Collection<JobInProgress> getJobs(String queueName);

      /**
       * Refresh the configuration of the scheduler.
       */
      public void refresh() throws IOException {}


      /**
       * Subclasses can override to provide any scheduler-specific checking
       * mechanism for job submission.
       * @param job
       * @throws IOException
       */
      public void checkJobSubmission(JobInProgress job) throws IOException{
      }

    }
