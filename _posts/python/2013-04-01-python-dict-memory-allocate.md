---
layout: post
category : python
tags : [python]
---
{% include JB/setup %}

python的dict结构的内存分配真是有点变成，他是一个预分配的过程, 这样虽然减少了内存分配的次数  

但是会造成很大的浪费  

特别是在加载大字典时，如加载一个 1亿左右的kv字典时key为 string, v为浮点数 内存用到了 6G  

基本上是按倍数分配的，如在 8kw时，内存大概是 3G, 到1亿时，就分配 6G  

    sys.getsizeof(obj)
    可以得到对象的内存大小

[python 内存](http://stackoverflow.com/questions/1896674/python-how-to-read-huge-text-file-into-memory)
