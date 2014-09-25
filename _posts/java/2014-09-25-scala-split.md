---
layout: post
category : scala 
tags : [scala]
---
{% include JB/setup %}

今天被Scala的split 坑了两个小时，找了很久，才怀疑split的问题。

1. 如果不能切分，他会返回原始字符串，如

    "".split("a").length //长度为 0
    "aaaaa".split("b").length //长度为1

2.如果全部是切分符号，会返回 0

    "aaaaa".split("a").length

