---
layout: post
category : hadoop 
tags : [hadoop]
---
{% include JB/setup %}

##app uv计算 

最可靠的uv计算方式是这样的 [hadoop pig uv计算](http://centerqi.github.io/hadoop/2012/10/11/hadoop-pig-uv/ 'hadoop pig uv')  

今天想用普通的方法，去计算uv，到底如上一种方式相差多少  

###android平台
ukey = mac+imei

###ios平台
ukey = mac+openudid


###代码

        ASET = LOAD 'xx.log' as (platform,ukey,requesturl)
        SITEG = GROUP ASET by (platform);
        RES = FOREACH SITEG{
            SITEUV = DISTINCT ASET.ukey;

            ITEMTMP = FILTER ASET BY requesturl == 'itempg';
            LISTTMP = FILTER ASET BY requesturl == 'listpg';
            TAOKETMP = FILTER ASET BY requesturl == 'taokepg';

            ITEMUV = DISTINCT ITEMTMP.ukey;
            LISTUV = DISTINCT LISTTMP.ukey;
            TAOKEUV = DISTINCT TAOKETMP.ukey;
           GENERATE FLATTEN(group), COUNT(SITEUV), COUNT(LISTUV), COUNT(ITEMUV), COUNT(TAOKEUV); 
        };


###对比结果
然后如请面比较靠普的方法进行对比

<table class="table table-striped table-bordered">
    <tr><td>platform</td><td>相差百分比</td></tr>
    <tr><td>android</td><td>4.111%</td></tr>
    <tr><td>ipad</td><td>0.6%</td></tr>
    <tr><td>iphone</td><td>0.1%</td></tr>
</table>


