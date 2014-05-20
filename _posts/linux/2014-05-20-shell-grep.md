---
layout: post
category : linux
tags : [shell]
---
{% include JB/setup %}

grep 真方便，遇到一种情况。

    grepStr="a=aaaa&b=bbbbbb&c=cccccc"

想要在grepStr 中匹配出 b的值，也就是最后的输出为 bbbbbb 

并且只能用一个grep。

找了好久，最后发现断言能解决这个文件。 


代码如下

    echo "a=aaaa&b=bbbbbb&c=cccccc" |  grep  -oP '(?<=b\=)[^&\s]*' 

输出

    bbbbbb


以后遇到各种解决不了的问题的时候，可以去翻翻 断言 这东东，下面这三文章总结的不错

[grep 断言](http://jjdoor.blog.163.com/blog/static/184780342012318917389/ 'grep 断言')

[grep 断言](http://www.regular-expressions.info/lookaround.html 'grep 断言')

[grep 断言](http://www.regular-expressions.info/lookaround.html  'grep 断言')


