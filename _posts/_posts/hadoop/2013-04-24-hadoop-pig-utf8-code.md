---
layout: post
category : hadoop
tags : [hadoop]
---
{% include JB/setup %}

早上来看报表，发现一个很奇怪的问题，就是中文变成乱码了

原来 是 `主支搜索 as slcnt`这个没有加类型，

不知道pig默认用什么类型去处理了，可能是bytearray,其实是java依赖于一些环境变量，

化现 crontab 的环境变量与用户登入的环境变量不同  

把 LANG=en_US.UTF-8

把 ``'主动搜索' as slcnt` 更正为 ``'主动搜索' as slcnt:chararray` 就不会出乱码了


        SSLG = GROUP SS BY (platform,page);
        SSLCNTTMP = FOREACH SSLG { GENERATE '主动搜索' as slcnt:chararray, FLATTEN(group),  COUNT(SS) as loadcnt; };
        SSLCNT = FILTER SSLCNTTMP by page < 11;


这几个包得好好了解下


        import java.nio.ByteBuffer;
        import java.nio.CharBuffer;
        import java.nio.charset.Charset;
        import java.nio.charset.CharsetDecoder;
        import java.nio.charset.CoderResult;
        import java.nio.charset.CodingErrorAction;

[java编码关系](http://www.philvarner.com/blog/2009/10/24/unicode-in-java-default-charset-part-4/)
