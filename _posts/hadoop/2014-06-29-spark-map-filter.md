---
layout: post
category : hadoop
tags : [spark]
---
{% include JB/setup %}

实测了一下，spark的性能还是很不错的，今天测试了一下spark的函数,map,filter

仅仅是做一下map和filter是测试不出太大性能的，重要的是测试join之类的聚合函数。


    package org.apache.spark.examples;

    import com.google.common.collect.Lists;
    import scala.Tuple2;
    import scala.Tuple3;
    import org.apache.spark.SparkConf;
    import org.apache.spark.api.java.JavaPairRDD;
    import org.apache.spark.api.java.JavaRDD;
    import org.apache.spark.api.java.JavaSparkContext;
    import org.apache.spark.api.java.function.Function2;
    import org.apache.spark.api.java.function.Function;
    import org.apache.spark.api.java.function.PairFunction;

    import java.io.Serializable;
    import java.util.Collections;
    import java.util.List;
    import java.util.regex.Matcher;
    import java.util.regex.Pattern;
    import java.util.*;

    public final class JavaLogQuery {

      public static Map<String,String> sourceToMap(String pline){
          String line = pline;
          int strLen = 0;
          String[] strArr;
          String[] strSubArr;
          Map<String,String> mapLog = new HashMap<String,String>();
          if(pline == null || pline.length() <= 2){
              return mapLog;
          }

          line = line.trim();
          strLen = line.length();
          line = line.substring(1,strLen -1);
          strArr = line.split("\"\\|\"");
          if(strArr.length == 0){
              return mapLog;
          }

          for(String s:strArr){
              if(s != null && s.length() != 0){
                  strSubArr = s.split(":",2);
                  if(strSubArr.length == 2){
                      mapLog.put(strSubArr[0],strSubArr[1]);
                  }
              }
          }
          return mapLog;
      }

     public static void main(String[] args) {

        SparkConf sparkConf = new SparkConf().setAppName("JavaLogQuery");
        JavaSparkContext jsc = new JavaSparkContext(sparkConf);

        JavaRDD<String> dataSet =  jsc.textFile(args[0],1) ;



        JavaRDD<String> lines = dataSet.map(new Function<String, String>() {
          @Override
          public String call(String s) {
            Map<String,String> mapLog = new HashMap<String,String>();
            mapLog = sourceToMap(s);
            String guid = "";

            if (mapLog.get("guid") == null){
                guid = "";
            }else{
                guid = mapLog.get("guid");
            }
            return guid;
          }
        });

        JavaRDD<String> guids = lines.filter(new Function<String,Boolean>(){
            public Boolean call(String s) throws Exception{
                if(s != null && !s.isEmpty()){
                    return true;
                }else{
                    return false;
                }
            }

        });


        guids.saveAsTextFile(args[1]);
        jsc.stop();
      }


