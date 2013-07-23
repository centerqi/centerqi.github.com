---
layout: post
category : python
tags : [python]
---
{% include JB/setup %}

python 编码是很有意思的一件事情，搞的不好，经常容易出乱码

这个世界就怕乱，所以经常有xx社会  


#python内置编码  
1. python的默认内置编码成 unicode(万国码)  
2. 在作编译转换时，要将 unicode作为中间码  
3. 想直接从utf8转换成gbk是会报错的  
4. 解码decode是从其它编码解码成unicode  
5. encode 是将 unicode转码成别的编码方式  
6. 可以在文件的开头指定默认编码  
        #-*- coding:xxx -*-



        str = '中国'
        str.decode('gbk')
        #这是把gbk 解码成unicode，这可能会报错是，如果你的编码是utf8，如果你的编码是 gbk的话，就不会报错  


        如下代码 
        #/usr/bin/env python
        #-*- coding:xx -*-
        #default utf8 code
        str =  "中国"

        #utf-8 to unicode 
        unicodeStr = str.decode('utf-8')

        #unicode to gbk
        gbkStr = unicodeStr.decode('gbk')





[python编码详解](http://hi.baidu.com/tornadory/item/4c6a2d37599d09352e20c4fb)
