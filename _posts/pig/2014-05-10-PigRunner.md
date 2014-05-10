---
layout: post
category : pig 
tags : [pig]
---
{% include JB/setup %}


Pig 好像没有thrift接口，分析部的几个同事都会hql,pig脚本，hql是支持给他们提供了一个web界面,他们可以在上面自由的写hql。

hql当时是这样处理的，用php通过thrift来调用hive，但是真正执行hql，是通过php调用系统命令，然后把输出写到文件中，方法虽然土，但是很有效果。

想慢慢改进，让Pig 或者Hive支持http请求，首先从Pig更改开始，看了一下Pig的源代码，发现很方便支持http协议。

主要是调用 PigRunner.run 这一个方法，基本上可以把很多事情搞定，然后让php这边提供一个回调的接口，那就能很好的解决这个问题了。


用到jetty来做为web server;

jetty接受来http请求，直接调用PigRunner 这一类就搞定了。


    import java.io.File;
    import java.io.FileWriter;
    import java.io.IOException;
    import java.io.PrintWriter;
    import java.util.HashMap;
    import java.util.Iterator;
    import java.util.List;
    import java.util.Map;
    import java.util.Properties;

    import org.apache.hadoop.conf.Configuration;
    import org.apache.hadoop.fs.Path;
    import org.apache.hadoop.mapred.Counters;
    import org.apache.pig.ExecType;
    import org.apache.pig.PigRunner;
    import org.apache.pig.PigRunner.ReturnCode;
    import org.apache.pig.backend.hadoop.datastorage.ConfigurationUtil;
    import org.apache.pig.backend.hadoop.executionengine.mapReduceLayer.plans.MROperPlan;
    import org.apache.pig.impl.PigContext;
    import org.apache.pig.impl.io.FileLocalizer;
    import org.apache.pig.newplan.Operator;
    import org.apache.pig.tools.pigstats.InputStats;
    import org.apache.pig.tools.pigstats.JobStats;
    import org.apache.pig.tools.pigstats.OutputStats;
    import org.apache.pig.tools.pigstats.PigProgressNotificationListener;
    import org.apache.pig.tools.pigstats.PigStats;
    import org.apache.pig.tools.pigstats.PigStatsUtil;

    import org.apache.pig.PigServer;
    import org.apache.log4j.PropertyConfigurator;
    import org.apache.log4j.Logger;
    import org.apache.log4j.Level;

    import idata.utils.Utils;


    public class PigCommand {
            static Logger logger = Logger.getLogger(PigCommand.class.getName());
            
            public static boolean  checkSyntax(String pigContent){
                    boolean isSucc = false;
                    String tmpPath=null;
                    String tmpFile = null;
                    
                    if(pigContent == null){
                            return isSucc;
                    }
            tmpFile = Utils.createTempFile();
            if(tmpFile == null){
                logger.error("create tmp file failed");
                return isSucc;
            }
            
            Utils.filePutContents(tmpFile,pigContent,false);

                    try {
                String[] param = {"-x","local","-c",tmpFile}; 
                PigStats stats = PigRunner.run(param, null); 
               if(stats.isSuccessful()){
                    logger.info("check success"); 
                   isSucc = true;
               }else{
                    logger.error("Check fail"); 
                    isSucc = false;
               }
            }catch(Exception e){
                logger.error("Exception:" + e);
            } 
                    
                    return isSucc;
                    
                    
            }

    }

