---
layout: post
category : hadoop
tags : [hadoop]
---
{% include JB/setup %}

一直想知道 hadoop 的map数是怎么样计算出来的，找了不少资料，看来还是自己去读源代码来的实在.

决定用多少个 map的代码在如下。

在 FileInputFormat这个文件中实现了要多少个map

    hadoop-1.1.2/src/mapred/org/apache/hadoop/mapreduce/lib/input/FileInputFormat.java

最基本的实现如下



    /** 
     * Generate the list of files and make them into FileSplits.
     */ 
    public List<InputSplit> getSplits(JobContext job
            ) throws IOException {
        // minSize和maxSize都是bytes
        long minSize = Math.max(getFormatMinSplitSize(), getMinSplitSize(job));
        long maxSize = getMaxSplitSize(job);

        // generate splits
        List<InputSplit> splits = new ArrayList<InputSplit>();

        //获取所有的输入文件
        List<FileStatus>files = listStatus(job);
        
        //对每一个输入文件按照splitSize进行逻辑上的拆分，拆分成InputSplit

        for (FileStatus file: files) {
            Path path = file.getPath();
            FileSystem fs = path.getFileSystem(job.getConfiguration());
            long length = file.getLen();
            BlockLocation[] blkLocations = fs.getFileBlockLocations(file, 0, length);
            if ((length != 0) && isSplitable(job, path)) { 
                long blockSize = file.getBlockSize();
                long splitSize = computeSplitSize(blockSize, minSize, maxSize);

                long bytesRemaining = length;
                while (((double) bytesRemaining)/splitSize > SPLIT_SLOP) {
                    int blkIndex = getBlockIndex(blkLocations, length-bytesRemaining);
                    splits.add(new FileSplit(path, length-bytesRemaining, splitSize, 
                                blkLocations[blkIndex].getHosts()));
                    bytesRemaining -= splitSize;
                }

                if (bytesRemaining != 0) {
                    splits.add(new FileSplit(path, length-bytesRemaining, bytesRemaining, 
                                blkLocations[blkLocations.length-1].getHosts()));
                }
            } else if (length != 0) {
                splits.add(new FileSplit(path, 0, length, blkLocations[0].getHosts()));
            } else { 
                //Create empty hosts array for zero length files
                splits.add(new FileSplit(path, 0, length, new String[0]));
            }
        }

        // Save the number of input files in the job-conf
        job.getConfiguration().setLong(NUM_INPUT_FILES, files.size());

        LOG.debug("Total # of splits: " + splits.size());
        return splits;
    }

getFormatMinSplitSize 这个函数返回1，为什么返回1呢？又不把它做成可配置的呢？

     /**
       * Get the lower bound on split size imposed by the format.
       * @return the number of bytes of the minimal split for this format
       */
      protected long getFormatMinSplitSize() {
        return 1;
      }

getMinSplitSize 返回在jobConf中配置的项 mapred.min.split.size

    /**
       * Get the minimum split size
       * @param job the job
       * @return the minimum number of bytes that can be in a split
       */
      public static long getMinSplitSize(JobContext job) {
        return job.getConfiguration().getLong("mapred.min.split.size", 1L);
      }


getMaxSplitSize 返回在 jobConf中配置项 mapred.max.split.size

      /**
       * Get the maximum split size.
       * @param context the job to look at.
       * @return the maximum number of bytes a split can include
       */
      public static long getMaxSplitSize(JobContext context) {
        return context.getConfiguration().getLong("mapred.max.split.size", 
                                                  Long.MAX_VALUE);
      }

computeSplitSize 有点意思，先从 maxSize和blockSize中返回最小的，然后与 minSize比较，返回最大的

      protected long computeSplitSize(long blockSize, long minSize,
                                      long maxSize) {
        return Math.max(minSize, Math.min(maxSize, blockSize));
      }

###总结
1. 不是对所有的输入文件进行加起来进行InputSplit拆分，而是对每一个文件进行InputSplit进行拆分。
    如果你有110个文件，那理论上来说，最少会有 110个InputSplit，也就是最少会有 110个map

2. **要控制整个job的map数，可以把小的文件合并成一个大的，然后加大 minSize数，注意单位是 bytes.**


