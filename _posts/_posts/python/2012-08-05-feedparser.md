---
layout: post
category : python
tags : [python]
---
{% include JB/setup %}

对 rss 的解析，python 好像没有 php 那么多库,  feedparser 感觉还不错，不过性能真的不太符合我的要求  


发现 feedparser 对编码的处理真是有意思，特别是在中文处理方面    

xml的指定编码为 UTF-8，不知道怎么一回事，经过 feedparser 取出来之后，他自动转成 unicode 了 

所以得用 encode('utf8') 处理之后，才能还原成 utf8 

        d = feedparser.parse('news.xml')
        lenItem = len(d.entries)
        for i in range(0,lenItem):
                title =  d.entries[i].title
                link=  d.entries[i].link
                description=  d.entries[i].description
                category=  d.entries[i].category
                author=  d.entries[i].author
                pubDate=  d.entries[i].published
                pubTime = time.strptime(pubDate, "%a, %d %b %y %H:%M:%S +0800") 
                timeStamp = int(mktime(pubTime))

                title=title.encode('utf8')
                description = description.encode('utf8')
                category = category.encode('utf8')
                author = author.encode('utf8')




[feedparser character encoding ](http://packages.python.org/feedparser/character-encoding.html)
