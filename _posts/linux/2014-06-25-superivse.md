---
layout: post
category : linux
tags : [shell]
---
{% include JB/setup %}

supervise 非常不错，用来做进程的监管，但是有一个问题，因为一个目录只能有一个run文件。

如果像flume-ng那样，要起多个conf的进程，这就有点问题了，不过 Daemontools 提供一个 service目录，可以在这个目录下建立多个子目录。

里面放不同的run文件就可以了。


然后用如下命令，起动守护进程就可以了

    svscan /service  

[supervise 详解](http://blog.163.com/linzhigui1988@126/blog/static/101886581201254033885/ 'Supervice 详解')
