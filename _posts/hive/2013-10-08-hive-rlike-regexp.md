---
layout: post
category : hive 
tags : [hive]
---
{% include JB/setup %}

今天碰到一个很奇怪的问题是关于 hive中 RLIKE 与 REGEXP 的关系的。

先记录一下官方文档中，关于 RLIKE与 REGEXP 的解释

###LIKE

    A [NOT] LIKE B strings NULL if A or B is NULL, TRUE if string       A matches the SQL simple regular expression B, otherwise FALSE. The comparison is done character by character. The _ character in B matches any character in A (similar to . in posix regular expressions) while the % character in B matches an arbitrary number of characters in A (similar to .* in posix regular expressions) e.g. 'foobar' like 'foo' evaluates to FALSE where as 'foobar' like 'foo_ _ _' evaluates to TRUE and so does 'foobar' like 'foo%'

###RLIKE

    A RLIKE B strings NULL if A or B is NULL, TRUE if any (possibly empty) substring of A matches the Java regular expression B, otherwise FALSE. E.g. 'foobar' RLIKE 'foo' evaluates to TRUE and so does 'foobar' RLIKE '^f.*r$'.

###REGEXP

    A REGEXP B strings Same as RLIKE

想不明白，翩然 REGEXP就和RLIKE功能是一样的，为什么还要搞一个REGEXP，也可能是别名

但是在测试的时候，发现 REGEXP 确实和RLIKE不一样


这个语句是去过滤 splashPage和getConfig之类的请求,但是不生效

    SELECT DISTINCT url FROM  tmpUid where day='2013-10-07' AND NOT( url  REGEXP '.*(?i)(splashPage|getConfig|update\\/).*')

如下语句是生效的

    SELECT DISTINCT url FROM  tmpUid where day='2013-10-07' AND NOT( url  RLIKE '.*(?i)(splashPage|getConfig|update\\/).*')

具体原因还没有找到，特查

