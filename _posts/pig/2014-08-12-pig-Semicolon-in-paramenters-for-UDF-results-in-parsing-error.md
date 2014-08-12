---
layout: post
category : pig 
tags : [pig]
---
{% include JB/setup %}


在REGEX_EXTRACT_ALL 中竟然不能用分号，google了一下，在0.11.1以下的版本中，都会受此影响。

想了一下，应该是语法解析的问题，把 `;` 当做一个token去处理了。

java是支持unicode处理的，因此，换成unicode的转义字符是可以了。

    AD = FOREACH AC GENERATE FLATTEN(REGEX_EXTRACT_ALL(cookie, '.*(?i)wfr=([^\\u003b\\" ]+).*')) AS  wfr;

\u003b是 `;` 的unicode编码。


[https://issues.apache.org/jira/browse/PIG-2507](https://issues.apache.org/jira/browse/PIG-2507 'https://issues.apache.org/jira/browse/PIG-2507')
