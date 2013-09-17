---
layout: post
category : hive 
tags : [hive]
---
{% include JB/setup %}

今天运营的同学来找我看 hive的错误，他们写了一条很 不错的SQL

    select b.platform,b.url,count(0) from proxymeta b join (select platform,machineid,max(requesttime) as maxrequesttime from proxymeta where day='2013-09-16' group by platform,machineid) a on b.platform=a.platform and b.machineid=a.machineid and b.requesttime=a.maxrequesttime where day='2013-09-16' group by b.platform,b.url order by b.platform asc,count(0) desc;"

其实这条SQL的目的很简单，就是看用户从哪一条请求(也就是哪一个操作)退出,并且退出的用户数有多少，并且排序。

HIVE的排序 ORDER BY 是很有个性的，他会放到一台机器去做排序，这样才能保证全局。

但是这条SQL会出现如下错误.

    FAILED: Error in semantic analysis: Line 10:9 Invalid table alias or column reference 'b': (possible column names are: _col0, _col1, _col2)

原因分析

1、ORDER BY 是不能加别名的，因为他是对 select 的结果进行排序，而 SELECT的结果已(可以看成一张新表),新表中再用b.platform这样的ALIAS，就会报错。

重新更正后的SQL

    hive -e "select b.platform,b.url,count(*) as oc from proxymeta b join (select platform,machineid,max(requesttime) as maxrequesttime from proxymeta where day='2013-09-16' group by platform,machineid) a on b.platform=a.platform and b.machineid=a.machineid and b.requesttime=a.maxrequesttime where day='2013-09-16' group by b.platform,b.url order by platform asc,oc desc limit 10;")"

[Table aliases in order by clause lead to semantic analysis failure](https://issues.apache.org/jira/browse/HIVE-1449 'Table aliases in order by clause lead to semantic analysis failure')


