---
layout: post
category : python
tags : [python]
---
{% include JB/setup %}

昨天在做一统计,碰到两人问题, 感觉很有共性

有两个 list 文件 
其中 list A文件包含了 商品和价格的对应关系,如 productID, price  

其中 list B文件包含了 一系列 productID  

需求是要把  list B 文件中的 productID从list A文件中找出相应的价格 

一惯的作法是把 list A加载到内存,建立 productID对 price的对应关系，在 python 中  

dict是一个很理想的数据结构,  然后在 foreach list B 文件，从 dict读取其对应的价格  

如果 list A 文件与 list B 文件都不算大，机器性能或者内存好 应该也不会出问题  

list A文件大概有 1.5亿条, list B文件大概有 1亿条左右  

当用 dict 加载 list A文件的时候，内存达到 6G左右,并且有时出现 memoryError  

不知道 dict 的查找算法是怎么样的，最后 foreach的时候，速度很慢  


解决方式 
1、把 list A文件 根据 productID 进行行存储, 如 productID为 1，存储到第一行,把 rpoductID与行号对应起来  基本上所有的文件操作都是支持  

seek()方法的  现在没必要反 list A文件加载到内存了，并且文件寻址是很快的.

2、用 hadoop 去解决，用 hadoop 去做 join操作是相当的快.





 

