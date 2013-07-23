---
layout: post
category : hadoop 
tags : [hadoop]
---
{% include JB/setup %}

##hadoop pig bincond matches 

bincond不知道怎么样译成中文，感觉和条件表示示是一样的，前面还可以加 not

###注意事项
1. bincond 后，是没有模式的，须自己指定模式  

2. matches 的正则与 java 的正则完全符合  

3. bincond前面可以加逻辑条件,如 not 


        %default inputstr '/data/proxy/project/udf/pig/raw/log*'
        --加载原始日志--
        AA = LOAD '$inputstr' USING kload.KoudaiLoader('platform,requesturl,imei,openudid,mac') AS(platform, requesturl, imei, openudid, mac);

        --过滤后台进程请求--
        AB = FILTER AA BY not backFilter();

        --格式化用户ukey--
        AC = FOREACH AB GENERATE  flatten(kload.KoudFormateUkey(platform,mac,imei,openudid)) AS(platform,ukey),requesturl;

        ACITEM = FOREACH AC GENERATE platform,ukey,(requesturl matches '.*(?i)getItemInfo.*'? 'itempg':requesturl) as requesturl;

        ACLIST = FOREACH ACITEM GENERATE platform,ukey,(requesturl matches '.*(?i)(getMyStreetProducts|queryRecommendItems|dailyTop|queryIShoppingSimple|categorySearch|list
        ThemeItem|checkProduct|listAllGroupCombines|getAppsByGroup_v2).*'? 'listpg':requesturl) as requesturl;

        ACLAST = FOREACH ACLIST GENERATE platform,ukey,(requesturl matches '.*(?i)taoke.*'? 'taokepg':requesturl) as requesturl;

        --这里取了反作操，如果不匹配
        ACOTHER = FOREACH ACLAST GENERATE platform,ukey,(not requesturl matches '.*(?i)(itempg|taokepg|listpg).*'? 'otherpg':requesturl) as requesturl;
        dump ACOTHER;

##输出
        (android,90:C1:15:6C:B3:C1_358943040907312,listpg)
        (android,B4:98:42:68:8C:DF_867083011351846,otherpg)
        (iphone,38:48:4C:1C:CD:70_4BB2D0811DCFF387291405433667E27BCAAB290D,otherpg)
        (android,B0:AA:36:C3:B2:07_864048013754035,otherpg)
        (iphone,C8:6F:1D:27:0C:71_DE6D3F93F6F1AF6E657E216CBD0CC7E590EC1BE4,taokepg)
        (android,98:0C:82:AF:72:3A_357474047931510,otherpg)
