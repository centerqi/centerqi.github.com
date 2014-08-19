---
layout: post
category : hadoop
tags : [spark]
---
{% include JB/setup %}


搭建了一个6台机器的 yarn集群用来测试，主集群还没有迁移。

感谢yarn最主要的地方是在对资源的管理上面，现在虽然主要是memory和cpu.

并且对cpu的隔离还不太好，但是比以前的slot的方式，确实是一大进步。


###slot vs containers

slot这样的粗粒度的资源管理已经被containers代替了

一个集群总共有多少个 containers ,这个是与cpu和内存有关的。

    yarn.nodemanager.resource.memory-mb //设置每一个nodemanager 的可用内存

    yarn.nodemanager.resource.cpu-vcores //设置每一个nodemanager 的可用cpu核算（虚拟）



而mapreduce跑job的时候，需要向yarn申请资源，他的每一个task要的资源是通过如下两个参数设置的。

    mapreduce.[map|reduce].memory.mb 

    mapreduce.[map|reduce].cpu.vcores

###并发
yarn的mapreduce的最大并发计算

>one can determine how many concurrent tasks are launched per node by dividing the resources allocated to YARN by the resources allocated to each MapReduce task,
>and taking the minimum of the two types of resources (memory and CPU). Specifically,
>you take the minimum of

公式

    yarn.nodemanager.resource.memory-mb divided by mapreduce.[map|reduce].memory.mb 
    yarn.nodemanager.resource.cpu-vcores divided by mapreduce.[map|reduce].cpu.vcores

<table class="table table-striped table-bordered">
    <tr><td>YARN</td><td>(in gb)</td></tr>

    <tr><td>yarn.nodemanager.resource.memory-mb</td><td>42</td></tr>
    <tr><td>yarn.nodemanager.resource.cpu-vcores</td><td>16</td></tr>

    <tr><td>MapReduce 2</td><td></td></tr>
    <tr><td>Map</td><td></td></tr>
    <tr><td>mapreduce.map.memory.mb</td><td>2</td></tr>
    <tr><td>Map Memory Slots</td><td>21</td></tr>
    <tr><td>mapreduce.map.cpu.vcores</td><td>1</td></tr>
    <tr><td>Map CPU Slots</td><td>16</td></tr>
    <tr><td>Map Task Count</td><td>16</td></tr>

    <tr><td>Reduce</td><td></td></tr>
    <tr><td>mapreduce.reduce.memory.mb</td><td>2</td></tr>
    <tr><td>Reduce Memory Slots</td><td>21</td></tr>
    <tr><td>mapreduce.reduce.cpu.vcores</td><td>1</td></tr>
    <tr><td>Reduce CPU slots</td><td>16</td></tr>
    <tr><td>Reduce Task Count</td><td>16</td></tr>
</table>

以上的并发task数并不是固定的，如以下参数也会影响并发.

    mapreduce.job.reduce.slowstart.completedmaps //默认 0.05

[apache-hadoop-yarn-avoiding-6-time-consuming-gotchas](http://blog.cloudera.com/blog/2014/04/apache-hadoop-yarn-avoiding-6-time-consuming-gotchas/ 'apache-hadoop-yarn-avoiding-6-time-consuming-gotchas')
