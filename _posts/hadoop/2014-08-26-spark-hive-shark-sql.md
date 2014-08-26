---
layout: post
category : hadoop
tags : [spark]
---
{% include JB/setup %}


一直没搞明白 shark,hive on spark,sql on spark的关系。

大概是如下的一个关系


<img src="http://databricks.com/wp-content/uploads/2014/07/sql-directions-1024x691.png" alt="sql-directions" />

1. shark已经停止支持了，可以迁移去sql.

2. shark可以迁移到 sql.

3. 新建立了一个项目，叫做hive on spark.

如果要hive支持，得编译时添加参数，个人喜欢用 make-distribution.sh 这个脚本



    sh make-distribution.sh --hadoop 2.4.1 --with-yarn --skpi-java-test --tgz --with-hive


[shark-spark-sql-hive-on-spark-and-the-future-of-sql-on-spark](http://databricks.com/blog/2014/07/01/shark-spark-sql-hive-on-spark-and-the-future-of-sql-on-spark.html 'http://databricks.com/blog/2014/07/01/shark-spark-sql-hive-on-spark-and-the-future-of-sql-on-spark.html')

[Hive on Spark](https://cwiki.apache.org/confluence/display/Hive/Hive+on+Spark 'https://cwiki.apache.org/confluence/display/Hive/Hive+on+Spark')
