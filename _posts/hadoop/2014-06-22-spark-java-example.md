---
layout: post
category : hadoop
tags : [spark]
---
{% include JB/setup %}

写spark的代码，最好是用scala，但是scala用的很方便，但是我自己写的代码，过几天就读不懂了，因为到处都是括号。

还是比较喜欢用java去写spark的代码。

我主要是自己用mvn从头到尾构建一个spark的job.

第一步，要建立好基本的目录，和生成基本的pom.xml文件

运行如下语句 就会生成基本的项目框架了。

    mvn archetype:generate -DarchetypeGroupId=org.apache.maven.archetypes -DgroupId=spark.examples -DartifactId=JavaWordCount -Dfilter=org.apache.maven.archetypes:maven-archetype-quickstart


第二步，写一个java的wordcount，不过也可以从spark的example中copy过来

代码如下，就是读取一个hdfs上面的文件，然后用空格去分词，分别统计每个词的次数。
然后保存到hdfs上面。



    package org.apache.spark.examples;

    import scala.Tuple2;
    import org.apache.spark.SparkConf;
    import org.apache.spark.api.java.JavaPairRDD;
    import org.apache.spark.api.java.JavaRDD;
    import org.apache.spark.api.java.JavaSparkContext;
    import org.apache.spark.api.java.function.FlatMapFunction;
    import org.apache.spark.api.java.function.Function2;
    import org.apache.spark.api.java.function.PairFunction;

    import java.util.Arrays;
    import java.util.List;
    import java.util.regex.Pattern;


    public final class JavaWordCount {
      private static final Pattern SPACE = Pattern.compile(" ");

      public static void main(String[] args) throws Exception {

        if (args.length < 1) {
          System.err.println("Usage: JavaWordCount <file>");
          System.exit(1);
        }   

        SparkConf sparkConf = new SparkConf().setAppName("JavaWordCount");
        JavaSparkContext ctx = new JavaSparkContext(sparkConf);
        JavaRDD<String> lines = ctx.textFile(args[0], 1);

        JavaRDD<String> words = lines.flatMap(new FlatMapFunction<String, String>() {
          @Override
          public Iterable<String> call(String s) {
            return Arrays.asList(SPACE.split(s));
          }
        });

        JavaPairRDD<String, Integer> ones = words.mapToPair(new PairFunction<String, String, Integer>() {
          @Override
          public Tuple2<String, Integer> call(String s) {
            System.out.println(s);
            return new Tuple2<String, Integer>(s, 1);
          }
        });

        JavaPairRDD<String, Integer> counts = ones.reduceByKey(new Function2<Integer, Integer, Integer>() {
          @Override
          public Integer call(Integer i1, Integer i2) {
            return i1 + i2;
          }
        });
        counts.saveAsTextFile("/user/www/tmp/wordcount/test1/");
    }

    }

第三步，修改 pom.xml文件（刚才运行的命令，应该已经生成了pom.xml）
完整的pom.xml文件如下，主要是添加spark和hadoop的依赖

    <project xmlns="http://maven.apache.org/POM/4.0.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
      xsi:schemaLocation="http://maven.apache.org/POM/4.0.0 http://maven.apache.org/maven-v4_0_0.xsd">
      <modelVersion>4.0.0</modelVersion>
      <groupId>spark.examples</groupId>
      <artifactId>JavaWordCount</artifactId>
      <packaging>jar</packaging>
      <version>1.0-SNAPSHOT</version>
      <name>JavaWordCount</name>
      <url>http://maven.apache.org</url>
      <dependencies>
        <dependency>
          <groupId>junit</groupId>
          <artifactId>junit</artifactId>
          <version>3.8.1</version>
          <scope>test</scope>
        </dependency>
        <dependency>
          <groupId>org.apache.spark</groupId>
          <artifactId>spark-core_2.10</artifactId>
          <version>1.0.0</version>
        </dependency>
        <dependency>
        <groupId>org.apache.hadoop</groupId>
            <artifactId>hadoop-client</artifactId>
            <version>2.4.0</version>
        </dependency>
      </dependencies>
    </project>

第四步，用mvn打包 

    mvn package

第五步，用spark提交job

    /usr/local/webserver/spark-1.0.0-bin-hadoop2/bin/spark-submit --class org.apache.spark.examples.JavaWordCount \
    --master yarn-cluster \
    --num-executors 3 \
    --driver-memory 4g \
    --executor-memory 2g \
    --executor-cores 1 \
    ./target/JavaWordCount-1.0-SNAPSHOT.jar \
    /user/www/javaword.java

[参考](http://stackoverflow.com/questions/22298192/how-to-run-a-spark-java-program 'java program参考')



