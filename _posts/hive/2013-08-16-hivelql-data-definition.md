---
layout: post
category : hive
tags : [hive]
---
{% include JB/setup %}


##数据库的定义

    CREATE DATABASE financials;

    CREATE DATABASE IF NOT EXISTS financials;

    CREATE DATABASE financials LOCATION '/my/preferred/directory'; #同时指定数据存放目录

    CREATE DATABASE financials COMMENT 'Holds all financial tables'; #对数据库添加描述

    #在数据库定义的时候加上一些元信息

    CREATE DATABASE financials
    WITH DBPROPERTIES ('creator' = 'Mark Moneybags', 'date' = '2012-01-02');

    #查看元信息

    DESCRIBE DATABASE EXTENDED financials;
    

    #更改数据库的属性

    ALTER DATABASE financials SET DBPROPERTIES ('edited-by' = 'Joe Dba');

##创建表

        CREATE TABLE IF NOT EXISTS mydb.employees (
        name  STRING COMMENT 'Employee name',
        salary  FLOAT  COMMENT 'Employee salary',
        subordinates ARRAY<STRING> COMMENT 'Names of subordinates',
        deductions  MAP<STRING, FLOAT>
        COMMENT 'Keys are deductions names, values are percentages',
        address  STRUCT<street:STRING, city:STRING, state:STRING, zip:INT>
        COMMENT 'Home address')
        COMMENT 'Description of the table'
        TBLPROPERTIES ('creator'='me', 'created_at'='2012-01-02 10:00:00', ...)
        LOCATION '/user/hive/warehouse/mydb.db/employees';
        
        #直接复制模式
        CREATE TABLE IF NOT EXISTS mydb.employees2
        LIKE mydb.employees;

        #创建外部表,数据已经存在/data/stocks
        CREATE EXTERNAL TABLE IF NOT EXISTS stocks (
        exchange  STRING,
        symbol  STRING,
        ymd  STRING,
        price_open  FLOAT,
        price_high  FLOAT,
        price_low  FLOAT,
        price_close  FLOAT,
        volume  INT,
        price_adj_close FLOAT)
        ROW FORMAT DELIMITED FIELDS TERMINATED BY ','
        LOCATION '/data/stocks';

        #表分区管理
        CREATE TABLE employees (
        name  STRING,
        salary  FLOAT,
        subordinates ARRAY<STRING>,
        deductions  MAP<STRING, FLOAT>,
        address  STRUCT<street:STRING, city:STRING, state:STRING, zip:INT>
        )
        PARTITIONED BY (country STRING, state STRING);

        #从本地加载数据到表中
        LOAD DATA LOCAL INPATH '${env:HOME}/california-employees'
        INTO TABLE employees
        PARTITION (country = 'US', state = 'CA');

        #创建外部分区表
        CREATE EXTERNAL TABLE IF NOT EXISTS log_messages (
        hms  INT,
        severity  STRING,
        server  STRING,
        process_id  INT,
        message  STRING)
        PARTITIONED BY (year INT, month INT, day INT)
        ROW FORMAT DELIMITED FIELDS TERMINATED BY '\t';

        #自定义表的存储格式
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
        #存储格式有TEXTFILE,SEQUENCEFILE, RCFILE

        #更改表
        ALTER TABLE log_messages RENAME TO logmsgs;
        ALTER TABLE log_messages ADD IF NOT EXISTS
        PARTITION (year = 2011, month = 1, day = 1) LOCATION '/logs/2011/01/01'
        PARTITION (year = 2011, month = 1, day = 2) LOCATION '/logs/2011/01/02'
        PARTITION (year = 2011, month = 1, day = 3) LOCATION '/logs/2011/01/03'
        ...;

        #改变列
        ALTER TABLE log_messages
        CHANGE COLUMN hms hours_minutes_seconds INT
        COMMENT 'The hours, minutes, and seconds part of the timestamp'
        AFTER severity;

        #增加列
        ALTER TABLE log_messages ADD COLUMNS (
        app_name  STRING COMMENT 'Application name',
        session_id LONG  COMMENT 'The current session id');
        
        #更改存储格式
        ALTER TABLE log_messages
        PARTITION(year = 2012, month = 1, day = 1)
        SET FILEFORMAT SEQUENCEFILE;

