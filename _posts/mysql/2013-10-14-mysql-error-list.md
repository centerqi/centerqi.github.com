---
layout: post
category : hive 
tags : [hive]
---
{% include JB/setup %}

1. 设置 mysql 的最大连接错误数

变量名称

    max_connect_errors

查看变量值

    select @@global.max_connect_errors;

设置变量

    set-variable=max_connections=250
    SET GLOBAL max_connections = 200;

2. 查看连接数

    show  processlist

3. 设置连接数

    max_connections

4. mysql 查看变量值 

    SHOW VARIABLES LIKE 'max_error_count';
    SELECT @@warning_count;

[mysql warning](http://dev.mysql.com/doc/refman/5.0/en/show-warnings.html 'mysql warning')
