---
layout: page
title: thinking
tagline: Supporting tagline
---
{% include JB/setup %}

##码农
没有程序员证的码农,和如下几位交情不错 `php, python, c`   
爱翻源码,给 `php, nginx`做过内科手术  
以前混饭于百度,现安居于口袋  
##爱折腾
linux是一个怎么折腾都可以的平台  
`智能家居`爱好者  
`Raspberry Pi`(树莓派)

##命令控
`vim`,`links`,`mutt`,`markdown`,`github`

##数据控
以前是数据搬运工,现在是数据抽取工  
常用农具为 `mysql,hadoop, pig, R, Hive, Mahout,hbase,spark,flume`  

##湘菜
`肥锅肉`, `剁椒鱼头`, `湘菜粉` 


<ul class="posts">
  {% for post in site.posts %}
    <li><span>{{ post.date | date:"%Y-%m-%d" }}</span> &raquo; <a href="{{ BASE_PATH }}{{ post.url }}">{{ post.title }}</a></li>
  {% endfor %}
</ul>


