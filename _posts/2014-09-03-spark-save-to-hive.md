---
layout: post
category : hadoop
tags : [spark]
---
{% include JB/setup %}


Spark hive 的环境已经搭建完毕，开始试着把现有的hql脚本迁移到spark上。

因为spark对hive的支持还不完善，很多语法不支持，我还提交了一个bug

以下代码都在 spark-shell中执行过。

##hive读写

    import org.apache.spark.{SparkConf, SparkContext}
    import org.apache.spark.sql._
    import hiveContext._
    import scala.collection.mutable.ArrayBuffer

    val hiveContext = new org.apache.spark.sql.hive.HiveContext(sc)


    //从meta表中读取userid,idlist数据
    val usermeta =  hql("  SELECT userid,idlist from meta WHERE day='2014-08-01'  and idlist !='' 1000")

    //Scheme
    case class SomeClass(name:String,productid:String,type:String)


    //把idlist拆分成多条纪录
    val scmm = usermeta.flatMap(t=>{
    val idlist = t(1).toString.split(";")
    val b = ArrayBuffer[SomeClass]()
    for(id <- idlist){
      b+=SomeClass(t(0).toString,id,"0")
    }
    b
    })
    val spark_save_rdd  = createSchemaRDD(scmm)
    spark_save_rdd.saveAsTable("spark_save_rdd") 

##RDD Save to hive
在邮件组里讨论人，有人提到另外一种方式，如下。

    import org.apache.spark.{SparkConf, SparkContext}
    import org.apache.spark.sql._
    val hiveContext = new org.apache.spark.sql.hive.HiveContext(sc)
    import hiveContext._

    val usermeta =  hql("  SELECT userid,idlist from meta WHERE day='2014-08-01'  AND url = 'getItems.do' AND idlist != '' AND isback != 1  limit 1000")
    case class SomeClass(name:String,productid:String,rtype:String)


    import scala.collection.mutable.ArrayBuffer

    val hqlscm = usermeta.flatMap(t=>{
    val idlist = t(1).toString.split(";")
    val b = ArrayBuffer[SomeClass]()
    for(id <- idlist){
      b+=SomeClass(t(0).toString,id,"0")
    }
    b
    })

    hqlscm.registerAsTable("rdd_save_table")
    hql("create table hive_rdd_save_table as select  * from rdd_save_table ")

参考
[save-schemardd-to-hive](http://apache-spark-user-list.1001560.n3.nabble.com/save-schemardd-to-hive-td13260.html 'http://apache-spark-user-list.1001560.n3.nabble.com/save-schemardd-to-hive-td13260.html')

[Support for CREATE TABLE AS SELECT that specifies the format](https://issues.apache.org/jira/browse/SPARK-3343 'https://issues.apache.org/jira/browse/SPARK-3343')
