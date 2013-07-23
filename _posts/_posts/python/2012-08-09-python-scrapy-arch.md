---
layout: post
category : python
tags : [scrapy]
---
{% include JB/setup %}

scrapy的架构还是很清晰的，因为可以用一个简单的图把整个架构画出来，如果一个架构图得画成密密麻麻，感觉那不是一个好的架构  

[scrapy 的架构图](http://doc.scrapy.org/en/latest/topics/architecture.html 'scrapy 的架构图') 

主要的组件
Scrapy engine
Scheduler
Downloader
Spiders
Item Pipeline
Downloader middlewares
Spider middlewares

[基本爬虫](https://github.com/youngsterxyf/Translation/blob/master/Crawl-a-website-with-scrapy.rst '基本爬虫')

[怎么样去重](http://snipplr.com/view/67018/middleware-to-avoid-revisiting-already-visited-items/ '怎么样去重')

