---
layout: post
category : java 
tags : [java]
---
{% include JB/setup %}

#hive data types and file formats

一看上次写的hive 笔记，时间真是把刀，距离hive的安装已经快四个月了，这其间事情太多，怎么没有好好把hive 总结一下
 
##hive原始类型 支持所有java支持的原始类型

    TINYINT
    SMALLINT
    INT
    BIGINT
    BOOLEAN
    FLOAT
    DOUBLE
    STRING
    TIMESTAMP
    BINARY

##hive 复合类型

    STRUCT #STRUCT {first STRING; last STRING}

    MAP

    ARRAY


###demo

    CREATE TABLE employees (
    name  STRING,
    salary  FLOAT,
    subordinates ARRAY<STRING>,
    deductions  MAP<STRING, FLOAT>,
    address  STRUCT<street:STRING, city:STRING, state:STRING, zip:INT>);

##hive 文本文件分隔付

    \n 默认分隔行
    ^A(ctrl+a) 分隔每一个字段(\003)
    ^B(ctrl+b) 分隔ARRAY和STRUCT中的每一个字段(\002)
    ^C(ctrl+c) 分隔MAP的每一个字段

##自定义格式

    CREATE TABLE employees (
    name  STRING,
    salary  FLOAT,
    subordinates ARRAY<STRING>,
    deductions  MAP<STRING, FLOAT>,
    address  STRUCT<street:STRING, city:STRING, state:STRING, zip:INT>
    )
    ROW FORMAT DELIMITED
    FIELDS TERMINATED BY '\001'
    COLLECTION ITEMS TERMINATED BY '\002'
    MAP KEYS TERMINATED BY '\003'
    LINES TERMINATED BY '\n'
    STORED AS TEXTFILE;

    CREATE TABLE some_data (
    first  FLOAT,
    second  FLOAT,
    third  FLOAT
    )
    ROW FORMAT DELIMITED
    FIELDS TERMINATED BY ',';



