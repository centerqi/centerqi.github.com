---
layout: post
category : python
tags : [python]
---
{% include JB/setup %}

今天开始用python去写一些脚本，原因是很多机器没有现成的php环境，但是大部分系统都有python环境  
发现python的代码段很有意思，这种缩进使代码看上去很舒服。  
主要是把一个csv文件读入内存，整理一下格式，然后输出为csv文件  
虽然只有几行代码，但是用到了python的一些库  
因为以前没学过python，是现炒现卖  

字符串的连接
[字符串的连接](http://canlynet.iteye.com/blog/675250, '字符串的连接')  

有点像java或者c++之类的字符串连接  
    fileName = "abc"
    postfix = ".txt"
    fullName = fileName + poxtFix
第二种是用 %号  
    fullName = '%s %s' % (fileName, postfix)  

日期的操作
用的最多的是 timestamp to date  
    t=datetime.fromtimestamp(time.time())
    t.strftime('%Y-%m-%d')'2012-03-07'


但是不明白为什么要导入很多包,如果不导入如下包，会报错  


    from datetime  import *
    import time


[time](http://stackoverflow.com/questions/3682748/converting-unix-timestamp-string-to-readable-date-in-python,'time')
[timestamp](http://docs.python.org/2/library/datetime.html, 'timestamp')

python中的字典是一很有用的数据结构，感觉和java或者c++的vector差不多。
1、创建字典

    dict1 = {}
    dict2 = {'fileName':'log.txt'}

2、访问可以用 for key in dict1 之类的语句，比php或者别的语言的好处是在确认是否此key在字典中存在时，可以用
    if xxx in collectDict:

3、字典的取值可以如下
    for key in collectDict:
更多
[字典](http://developer.51cto.com/art/201003/188837.htm,'字典')


csv文件的读写
python操作csv还是很方便的，虽然比php多了几行代码。  
[csv](http://docs.python.org/2/library/csv.html#writer-objects,'csv')  
不明白的一点是，调用writerows的时候，是要传入列表还是什么，文档上面没有demo


列表、元组、和字典的关系
字典感觉和php 中的数组没什么区别
http://yangsq.iteye.com/blog/128508





