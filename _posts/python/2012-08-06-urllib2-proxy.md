---
layout: post
category : python
tags : [python]
---
{% include JB/setup %}

自己写了一个小工具，抓取一些站点的东西，然后经过自己写的和个简单的算法，  

给自己推荐感兴趣的文章，主要是在手机上看, 因为这个要抓取的 list 里面有很多国外的站点，  

原因就不用说了，不用代理是访问不了的, urllib2 是支持代理的  


        import urllib2
        enable_proxy = True
        proxy_handler = urllib2.ProxyHandler({"http" : 'http://127.0.0.1:8087'})
        null_proxy_handler = urllib2.ProxyHandler({})

        if enable_proxy:
            opener = urllib2.build_opener(proxy_handler)
        else:
            opener = urllib2.build_opener(null_proxy_handler)

        urllib2.install_opener(opener)
        f = open('00000002.html','wb')
        html = urllib2.urlopen('http://cn.nytimes.com').read()
        f.write(html)
        f.close()


[stack overflow urllib proxy](http://stackoverflow.com/questions/1450132/proxy-with-urllib2)
