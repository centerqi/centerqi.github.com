---
layout: post
category : hadoop
tags : [spark]
---
{% include JB/setup %}


编译hadoop 2.4.1

##Requirements:

    Unix System

    ProtocolBuffer 2.5.0

    CMake 2.6 or newer (if compiling native code)

    Internet connection for first build (to fetch all Maven and Hadoop dependencies)

    Apache Maven 3.2.1 (ea8b2b07643dbb1b84b6d16e1f08391b666bc1e9; 2014-02-15T01:37:52+08:00)

    Java version: 1.7.0_67, vendor: Oracle Corporation

    findbugs

##编译

    mvn7 package -Pdist,native,docs,src -DskipTests -Dtar

##注意事项
1. 如果是要native，findbugs是不可以的。

2. 如果protobuf 不在默认的环境变量里面，一定要调转环境变量

    HADOOP_PROTOC_PATH

如
    export HADOOP_PROTOC_PATH=/usr/local/webserver/protobuf-2.5.0/bin/protoc

