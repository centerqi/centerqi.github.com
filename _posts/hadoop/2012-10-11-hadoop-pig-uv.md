---
layout: post
category : hadoop 
tags : [pig]
---
{% include JB/setup %}

UV计算的方式,有好几种处理方式，粗略计算的方式有 ip, 或者服务器下发一个 id，如百度用的好像是 BDUSS或者BAIDUID之类的   
但是要计算手机的用户的UV好像比PC上面复杂一点，这得感谢很多山寨厂商，并且各种平台，还有用户权限很多因素。  
如 Android有的版本如果要获取用户的 mac或者imei之类的，必须用户授权，ios平台的mac和openudid之类的，也会有这种情况   
计算UV的标准，分平台，Android是用mac+imei，而ios是用 mac+openudid。  

以android平台分例，会存在如下四种情况

<table class="table table-striped table-bordered">
    <tr><td>MAC</td><td>IMEI</td></tr>
    <tr><td>A</td><td>1</td></tr>
    <tr><td></td><td>1</td></tr>
    <tr><td>A</td><td></td></tr>
    <tr><td>B</td><td>2</td></tr>
    <tr><td>C</td><td></td></tr>
    <tr><td></td><td>3</td></tr>
    <tr><td>D</td><td>4</td></tr>
    <tr><td></td><td></td></tr>
</table>

从上面的例子可以看出存在如下几种情况  
1. 有mac 并且有imei  
2. 有mac, 无imei  
3. 无mac, 有imei  
4. 无mac,无imei  

计算uv的方法很简单，基本思路如下  
1. 先找出有mac并且有imei的做为集合A  
2. 找出MAC非空的集合标记为B  
3. 找出MAC为空的集合标记为C
4. 用B LEFT JOIN A BY  MAC得到集合D  
5. FILTER D by imei IS NULL得到集合E
6. 用C LEFT JOIN A BY imei 得到集合F  
7. FILTER F by mac IS NULL 得到集合G
8. UNIQUESET = UNION G, E, A  

按照上面的计算步骤，用pig实现如下  


        A = FILTER UVSET BY (mac is not null) AND (imei is not null);
        B = FILTER UVSET BY (mac is not null);
        C = FILTER UVSET BY (mac is null);

        D = JOIN B BY mac LEFT OUTER, A BY mac;
        E = FILTER D by (A::mac is null);
        E1 = FOREACH E GENERATE B::mac as mac, B::imei as imei;


        F = JOIN C BY imei LEFT OUTER, A BY imei;
        G = FILTER F BY (A::imei is null);
        G1 = FOREACH G GENERATE G::mac as mac, G::imei as imei;

        UNIQUESET = UNION G1, E1, A;

        TMPSET = GROUP UNIQUESET ALL;
        OUTRES = FOREACH TMPSET GENERATE COUNT(UNIQUESET);
        DUMP OUTRES;




