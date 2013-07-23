---
layout: post
category : python
tags : [lxml]
---
{% include JB/setup %}

python 对xml的解析库真是非常多，并且各自有各自的特点  


很多第三方库都兼容 elementtree这种方式  如 lxml 说完全兼容 elementtree 这种方式，一直迷惑了很久  


原来lxml是兼容了官方的 elementtree


从业界的测试来看，lxml在性能上比官方的 xml.elementtree要好  


[ElementTree与Element的关系与具体介绍](http://docs.python.org/2/library/xml.etree.elementtree.html ElementTree与Element的关系与具体介绍)

[lxml 的链接](http://lxml.de/ lxml的链接)


有几个概念的搞清楚  


1、elementTree和element是两个不同的class, 通过 tree = etree.parse()返回的是 elementTree，要进行元素的访问操作，还得调用 root=tree.getroot()  

2、findall只是返回它的儿子节点, find只返回他的第一个匹配的儿子节点.  

3、item直接返回他的所有元素，没有儿子节点这一限制.  

        #!/bin/env python
        #-*- coding:utf8 -*-
        from lxml import etree

        def loadConfig(file):

            tree = etree.parse(file)
            root = tree.getroot() 
            fields = root.iter('field')
            for i in fields:
                pass
            xfields = root.xpath(".//item/field")
            for x in xfields:
                print x.get('name')
            print type(xfields)
            print len(xfields)

        loadConfig("../taobao.xml")



