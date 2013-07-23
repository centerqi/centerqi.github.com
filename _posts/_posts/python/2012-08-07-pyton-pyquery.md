---
layout: post
category : python
tags : [python]
---
{% include JB/setup %}

pyquery 很像 jquery 的语法，对于精确提取内容是非常方便的, 今天试用了一下，发现还是非常不错，性能上面，发现比 feedparser 好那么一点  
  

        from pyquery import PyQuery as pq
        from lxml import etree
        import urllib
        html = open('../data/test.html','rb').read()

        d = pq(html.decode('UTF-8'))
        tx= d('#text_body')
        print tx.text().encode('UTF-8').encode('hex')



[http://packages.python.org/pyquery/api.html](http://packages.python.org/pyquery/api.html)  
[http://abloz.com/2012/11/13/analysis-of-the-beijing-real-estate-transaction-data-pyquery-crawl.html](http://abloz.com/2012/11/13/analysis-of-the-beijing-real-estate-transaction-data-pyquery-crawl.html)  


