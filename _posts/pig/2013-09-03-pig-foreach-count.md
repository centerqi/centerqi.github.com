---
layout: post
category : pig 
tags : [pig]
---
{% include JB/setup %}


在pig里面计算有多少个，经常用COUNT(xx) 之类的，但是不能像SQL那样直接COUNT(*)，得先GROUP,然后再FOREACH,COUNT();

FOREACH里面能写很多语句，如FILTER之类的，但是发现一个问题，就是FOREACH里面包有FILTER,DISTICT的时候，就是有一个REDUCE完成的特别慢。

如果数据较大，简单就慢的出奇。

####原程序
代码很简单，就是求各个平台的某些action 的UV和PV

    SITEG = GROUP ASET by (platform);
    RES = FOREACH SITEG{
            UV = DISTINCT ASET.ukey;  

        LISTTMP = FILTER ASET BY requesturl == 'list';
            LISTUV = DISTINCT LISTTMP.ukey;

        ITEMTMP = FILTER ASET BY requesturl == 'item';
            ITEMUV = DISTINCT ITEMTMP.ukey;

        TAOKETMP = FILTER ASET BY requesturl == 'jump';
            TAOKEUV = DISTINCT TAOKETMP.ukey;

        COLTMP = FILTER ASET BY requesturl == 'favorite';
            COLUV = DISTINCT COLTMP.ukey;

        GENERATE FLATTEN(group),COUNT(UV),COUNT(LISTTMP),COUNT(LISTUV), COUNT(ITEMTMP),COUNT(ITEMUV), COUNT(TAOKETMP), COUNT(TAOKEUV),COUNT(COLTMP),COUNT(COLUV); 

    };

一个FOREACH 里面包含了较多的FILTER和DISTINCT。这就造成了一个严重的问题，数据会倾斜，也有人在 stackoverflow上面问了同样的问题，就是会有一个reduce特别慢。

[stackover flow 关于 NESTED FOREACH 的讨论](http://stackoverflow.com/questions/10732456/how-to-optimize-a-group-by-statement-in-pig-latin 'NESTED FOREACH')

1、优化的原则，尽量减少 NESTED FOREACH里面的操作数，把 FILTER 移除。
2、在业务逻辑上并行，如list,item,jump,favorite可以用platform,requesturl来做分组.
3、站点 UV 可以先做DISTINCT，这样减少数据量。


    CSET = FILTER BSET BY requesturl matches '.*(?i)^(jump|list|item|favorite)$';
    SITEG = GROUP CSET by (platform,requesturl)  PARALLEL 16;
    RES = FOREACH SITEG{
            UV = DISTINCT CSET.ukey;  
            GENERATE FLATTEN(group),COUNT(UV),COUNT(CSET); 
    };

    --求总的uv--
    ALLUVSET= DISTINCT BSET;

    ALLGROUP = GROUP ALLUVSET BY (platform);
    RESUV = FOREACH ALLGROUP{
            UV = DISTINCT ALLUVSET.ukey;
            GENERATE FLATTEN(group),'uv', COUNT(UV),COUNT(ALLUVSET);
    };
    SITERES = UNION RES, RESUV; 

效果还是很明显的
调整之前的时间

    real    10m51.994s
    user    0m13.544s
    sys     0m2.775s

调整之后的时间

    real    4m9.441s
    user    0m47.293s
    sys     0m26.683s



找了很多资料参考关于 hadoop pig skewed group

[Apache Pig’s Optimizer](ftp://ftp.research.microsoft.com/pub/debull/A13mar/gates.pdf 'apache pig 优化')

[apache-pig-performance-optimizations-talk-at-apachecon-2010](http://www.slideshare.net/thejasmn/apache-pig-performance-optimizations-talk-at-apachecon-2010 '')

[apache pig](http://pig.apache.org/docs/r0.9.1/perf.html '')
