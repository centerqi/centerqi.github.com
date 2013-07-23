---
layout: post
category : php
tags : [php]
---
{% include JB/setup %}

乱码在程序处理中是经常碰到的问题，特别是越靠近前端的技术，就对编码的理解和运用要求就越高，最近用php做一个数据接口，提供给前端调用，碰到了好几个问题，所以感觉自己得好好总结一下。

 

（1）浏览器默认的url编码是不一样的，所以再前端提交接口数据时，一定要对url进行编码，并且双方都得知道是什么编码。

前端一般是用javascript提交数据，在javascript有好几个对url或者查询字符串进行编码的函数

 

escape是转出%u这样的Unicode字符，

encodeURI、encodeURIComponent则转出%EA这样的UTF-8格式字符序列，

 

encodeURI、encodeURIComponent 的区别

我们应该用encodeURI或者encodeURIComponent，而这两个很相像的兄弟的区别则在于他们使用的场合，

名字上已经给的很清楚了，encodeURI用于完整的URL的转码，

而encodeURIComponent用于参数部分，

所以对于?/&/#这样的在URL中表达特殊含义的字符，encodeURI是保留不做转码的，所以用哪个取决于需要。

更多的详细信息，仔细看这里：http://xkr.us/articles/javascript/encode-compare/

 

（2）ansi编码与ascii编码是沙子与沙漠的关系。

以下解释来自于百度百科

 

unicode和ansi都是字符代码的一种表示形式。

为使计算机支持更多语言，通常使用 0×80~0xFF 范围的 2 个字节来表示 1 个字符。

比如：汉字 ‘中’ 在中文操作系统中，使用 [0xD6,0xD0] 这两个字节存储。

不同的国家和地区制定了不同的标准，由此产生了 GB2312, BIG5, JIS 等各自的编码标准。

这些使用 2 个字节来代表一个字符的各种汉字延伸编码方式，称为 ANSI 编码。

在简体中文系统下，ANSI 编码代表 GB2312 编码，在日文操作系统下，ANSI 编码代表 JIS 编码。

不同 ANSI 编码之间互不兼容，当信息在国际间交流时，无法将属于两种语言的文字，存储在同一段 ANSI 编码的文本中。

 

（3）php 在中文ansi文件下，当接口传入参数为utf-8编码时，可能会出现一些一些有意思的事情。

如果已经确认前端传入参数为utf-8编码，而php文件为ansi文件时，有些事情是要特别注意的。

因中文ansi默认编码为gbk，所在如果在此文件中定义了一个php字符串，如

    $str = “中国人”;

但是url的编码为utf-8，所以你用$_GET['XXX']得到的字符串为utf-8，

不管是你用 $str == $_GET['XXX'],或者strpost($_GET['XXX'],$str);这样的操作，都将是无效且是危险的。

 

（4）php的md5在处理gbk时候，和处理utf8编码是完全不同的，md5与编码是相关的，因为不同编码的字节是可能不一样的。
