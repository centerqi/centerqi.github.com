---
layout: post
category : hive 
tags : [hive]
---
{% include JB/setup %}

sqoop从hive导出mysql还是比较麻烦的，不能自己创建表，得必须把表先创建好。

导出：
/usr/local/webserver/sqoop-1.4.4.bin__hadoop-1.0.0/bin/sqoop-export \
-Dmapred.job.queue.name=online  \
--connect jdbc:mysql://10.1.1.1/logdata \
--username username  \
--password password \
--table tmp_yy_meihaodone   \
--export-dir /user/www/hive/warehouse/tmp_yy_meihaodone \
--input-fields-terminated-by '\001'

[apache-sqoop-part-3-data-transfer](http://hadooped.blogspot.com/2013/06/apache-sqoop-part-3-data-transfer.html 'http://hadooped.blogspot.com/2013/06/apache-sqoop-part-3-data-transfer.html')
