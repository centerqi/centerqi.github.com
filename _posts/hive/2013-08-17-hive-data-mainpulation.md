---
layout: post
category : hive 
tags : [hive]
---
{% include JB/setup %}

##Loading Data into Managed Tables

>This command will first create the directory for the partition, if it doesn’t already exist,
then copy the data to it.
If the target table is not partitioned, you omit the PARTITIONclause.

>`OVERWRITE` keyword, any data already present in the target directory
will be deleted first. Without the keyword, the new files are simply added to the target
directory. However, if files already exist in the target directory that match filenames
being loaded, the old files are overwritten.

>If the  `LOCAL` keyword is used, the path is assumed to be in the local filesystem. The data
is  copiedinto the final location. If  LOCALis omitted, the path is assumed to be in the
distributed filesystem.


>The `PARTITION` clause is required if the table is partitioned and you must specify a value
for each partition key.

    LOAD DATA LOCAL INPATH '${env:HOME}/california-employees'
    OVERWRITE INTO TABLE employees
    PARTITION (country = 'US', state = 'CA');


##Inserting Data into Tables from Queries

    INSERT OVERWRITE TABLE employees
    PARTITION (country = 'US', state = 'OR')
    SELECT * FROM staged_employees se
    WHERE se.cnty = 'US' AND se.st = 'OR';
    
>scan the input data once and split it multiple ways

    FROM staged_employees se
    INSERT OVERWRITE TABLE employees
    PARTITION (country = 'US', state = 'OR')
    SELECT * WHERE se.cnty = 'US' AND se.st = 'OR'
    INSERT OVERWRITE TABLE employees
    PARTITION (country = 'US', state = 'CA')
    SELECT * WHERE se.cnty = 'US' AND se.st = 'CA'
    INSERT OVERWRITE TABLE employees
    PARTITION (country = 'US', state = 'IL')
    SELECT * WHERE se.cnty = 'US' AND se.st = 'IL';

##Dynamic Partition Inserts

>Hive determines the values of the partition keys, countryand state, from the `last two columns` in the  SELECTclause
Dynamic partitioning is not enabled by default. When it is enabled, it works in “strict” mode by default, where it expects at least some columns to be static.

    set hive.exec.dynamic.partition=true;
    set hive.exec.dynamic.partition.mode=nonstrict;
    set hive.exec.max.dynamic.partitions.pernode=1000;

    INSERT OVERWRITE TABLE employees
    PARTITION (country, state)
    SELECT ..., se.cnty, se.st
    FROM staged_employees se;

##Creating Tables and Loading Them in One Query

>This feature can’t be used with external tables. 

    CREATE TABLE ca_employees
    AS SELECT name, salary, address
    FROM employees
    WHERE se.state = 'CA';

##Exporting Data

    INSERT OVERWRITE LOCAL DIRECTORY '/tmp/ca_employees'
    SELECT name, salary, address
    FROM employees
    WHERE se.state = 'CA';
