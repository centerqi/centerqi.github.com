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


###yarn内存设置
yarn可以设置内存的地方比较多，从hdp找到了几个参数

几个重要的公式

    # of containers = min (2*CORES, 1.8*DISKS, (Total available RAM) / MIN_CONTAINER_SIZE)

    RAM-per-container = max(MIN_CONTAINER_SIZE, (Total Available RAM) / containers))


MIN_CONTAINER_SIZE 参考

<table border="1" class="MsoNormalTable" style="margin-left:4.5pt;border-collapse:collapse;mso-table-layout-alt:fixed;  border:none;mso-border-alt:solid black 1.0pt;mso-yfti-tbllook:1184;mso-padding-alt:  0in .5pt 0in .5pt;mso-border-insideh:1.0pt solid black;mso-border-insidev:  1.0pt solid black" width="432" id="d6e1249"><colgroup><col width="209pt"><col width="217pt"></colgroup><tbody><tr>
                    <td valign="top">
                        <span class="bold"><strong>Total RAM per Node</strong></span>
                    </td>
                    <td valign="top">
                        <span class="bold"><strong>Recommended Minimum Container Size</strong></span>
                    </td>
                </tr><tr>
                    <td valign="top"> Less than 4 GB </td>
                    <td valign="top"> 256 MB </td>
                </tr><tr>
                    <td valign="top"> Between 4 GB and 8 GB </td>
                    <td valign="top"> 512 MB </td>
                </tr><tr>
                    <td valign="top"> Between 8 GB and 24 GB </td>
                    <td valign="top"> 1024 MB </td>
                </tr><tr>
                    <td valign="top"> Above 24 GB </td>
                    <td valign="top"> 2048 MB </td>
                </tr></tbody></table>


<table border="1" class="MsoNormalTable" style="margin-left:4.5pt;border-collapse:collapse;mso-table-layout-alt:fixed;  border:none;mso-border-alt:solid black 1.0pt;mso-yfti-tbllook:1184;mso-padding-alt:  0in .5pt 0in .5pt;mso-border-insideh:1.0pt solid black;mso-border-insidev:  1.0pt solid black" width="778" id="d6e1274"><colgroup><col width="180pt"><col width="300pt"><col width="300pt"></colgroup><tbody><tr>
                <td valign="top">
                    <span class="bold"><strong>Configuration File</strong></span>
                </td>
                <td valign="top">
                    <span class="bold"><strong>Configuration Setting</strong></span>
                </td>
                <td valign="top">
                    <span class="bold"><strong>Value Calculation</strong></span>
                </td>
            </tr><tr>
                <td valign="top">yarn-site.xml</td>
                <td> yarn.nodemanager.resource.memory-mb</td>
                <td valign="top"> = containers * RAM-per-container </td>
            </tr><tr>
                <td valign="top">yarn-site.xml</td>
                <td> yarn.scheduler.minimum-allocation-mb </td>
                <td valign="top"> = RAM-per-container </td>
            </tr><tr>
                <td valign="top">yarn-site.xml</td>
                <td> yarn.scheduler.maximum-allocation-mb</td>
                <td valign="top"> = containers * RAM-per-container </td>
            </tr><tr>
                <td valign="top">mapred-site.xml</td>
                <td> mapreduce.map.memory.mb</td>
                <td valign="top"> = RAM-per-container </td>
            </tr><tr>
                <td valign="top">mapred-site.xml&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
                <td>mapreduce.reduce.memory.mb</td>
                <td valign="top"> = 2 * RAM-per-container </td>
            </tr><tr>
                <td valign="top">mapred-site.xml</td>
                <td>mapreduce.map.java.opts</td>
                <td valign="top"> = 0.8 * RAM-per-container </td>
            </tr><tr>
                <td valign="top">mapred-site.xml</td>
                <td>mapreduce.reduce.java.opts</td>
                <td valign="top"> = 0.8 * 2 * RAM-per-container </td>
            </tr><tr>
                <td valign="top">yarn-site.xml (check)</td>
                <td>yarn.app.mapreduce.am.resource.mb</td>
                <td valign="top"> = 2 * RAM-per-container </td>
            </tr><tr>
                <td valign="top">yarn-site.xml (check)</td>
                <td>yarn.app.mapreduce.am.command-opts</td>
                <td valign="top"> = 0.8 * 2 * RAM-per-container </td>
            </tr></tbody></table>

[Determine YARN and MapReduce Memory Configuration Settings](http://docs.hortonworks.com/HDPDocuments/HDP2/HDP-2.0.6.0/bk_installing_manually_book/content/rpm-chap1-11.html 'Determine YARN and MapReduce Memory Configuration Settings')
