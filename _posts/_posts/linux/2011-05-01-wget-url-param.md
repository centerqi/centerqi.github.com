---
layout: post
category : linux
tags : [c]
---
{% include JB/setup %}

#wget 参数
发现wget 后面的url代有多个参数的时候，后面的参数直接被 wget 给丢掉了，这真是一个糟糕的设计  
解决办法是加上引号  

#wget 代理
wget 也能支持代理,这样就省事多了

    wget -e http-proxy=127.0.0.1:8087 --proxy=on -c  http://cn.nytimes.com/rss/news.xml -O ./data/cn.nytimes.2013-01-000.xml

