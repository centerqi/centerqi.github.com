---
layout: post
category : hadoop 
tags : [hadoop]
---
{% include JB/setup %}

##hadoop pig log

###在调试udf的时候，log是很重要的一个东西，在hadoop pig 的 udf 中，可以访问到 log 的

    import org.apache.commons.logging.Log;
    import org.apache.commons.logging.LogFactory; 

###获取当前的 log 对像

    private static final Log log = LogFactory.getLog(xxx.class);


###使用

    log.warn(String message);
    log.debug(String message);



