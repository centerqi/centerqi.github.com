---
layout: post
category : hadoop
tags : [hadoop]
---
{% include JB/setup %}

hadoop 有几个参数我一直容易误解

mapred.tasktracker.map.tasks.maximum

>The maximum number of map tasks that will be run simultaneously by a task tracker.
应该是一个node能同时执行的最大 map 数，也就是slot数

