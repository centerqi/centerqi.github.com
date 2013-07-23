---
layout: post
category :mysql 
tags : [mysql]
---
{% include JB/setup %}

[mysql中的varchar类型](http://www.cnblogs.com/doit8791/archive/2012/05/28/2522556.html 'mysql中的varchar类型')  
varchar基本信息  


1、5.0版本后,varchar(20),指的是 20 个字符，不是 20 字节，varchar(20)也能存储20字中文汉字(utf8)  


2、mysql中的varchar 最大字节(bytes)是 65533 bytes(2^16 - 1) 因为这是row maxsize限制的 [column-count-limit](http://dev.mysql.com/doc/refman/5.0/en/column-count-limit.html,'column-count-limit')    


3、varchar 字段是将实际内容单独存储在聚簇索引之外，内容开头用1到2个字节表示实际长度（长度超过255时需要2个字节），因此最大长度不能超过65535。  


4、字符类型若为gbk，每个字符最多占2个字节，最大长度不能超过32766,字符类型若为utf8，每个字符最多占3个字节，最大长度不能超过21845。  


varchar与char的关系  
1、char的长度是从[0,255]个字符。    


2、varchar的长度是[0, 65535]。    


3、char会padding，而varchar不会，varchar会用一到两个字节来表示长度。    


[varchar 与char的区别](http://dev.mysql.com/doc/refman/5.0/en/char.html 'varchar 与 char的区别')。  


        TINYTEXT,TEXT,MEDIUMTEXT,LONGTEXT  
        TINYTEXT    256 bytes 
        TEXT    65,535 bytes ~64kb
        MEDIUMTEXT  16,777,215 bytes ~16MB
        LONGTEXT    4,294,967,295 bytes ~4GB


