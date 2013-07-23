---
layout: post
category : data 
tags : [data]
---
{% include JB/setup %}


移动上面的 UV 统计确实很麻烦，因为没有所谓的 cookie 这一搞法，并且平台较多，很不规范，在pc上面能做的如基于 cookie 和 ip 之类的都用不上  

在 pc 上面主要是根据  User Agent, Cookie and/or Registration ID 去计算 UV  

在 app 中，没有 User Agent, Cookie这一类的搞法，如果强制用户去注册，那成本也就太大了  

最好是 服务器给每一个设备都下发一个id，就像 cookie那样，存储在客户端  

但是怎么样去保证不重复生成，这个不太好处理  

1. 设备类型较多，如 现在的智能电视，手机，平板，机顶盒之类  
2. 平台较多，主流的有 windows, ios, android  

因为用户的主要组成是 ios,和 android,有一些基本的方法去识别用户 

#ios 
udid是apple 提供的，保证设备唯一，可惜这东西不能用了  

openudid 同一个设备可能存在多个值，刷系统可能变更  

#android
imei由厂商保证唯一，但是山寨机特别多，不可能唯一  

imsi由运营商唯唯一  

Seria Number 有版本限制, 恢复出厂设置时，可能会变  

android ID   有版本限制, 恢复出厂设置时，可能会变  

还有很神奇的山寨机，双卡双待之类的，怎么样去区分?

#没有银弹
只有尽可能多的去组合，降低串号的风险  

ios = `mac+openudid`  

andro = `imei+mac`

这样组合，基本上重复的机率较小，但是也会存在 imei和mac都为空的情况  




[UV（唯一访问者）和参照系（Context）的重要性](http://www.chinawebanalytics.cn/uv%EF%BC%88%E5%94%AF%E4%B8%80%E8%AE%BF%E9%97%AE%E8%80%85%EF%BC%89%E5%92%8C%E5%8F%82%E7%85%A7%E7%B3%BB%EF%BC%88context%EF%BC%89%E7%9A%84%E9%87%8D%E8%A6%81%E6%80%A7/)

[UV 的基本概念](http://www.chinawebanalytics.cn/%E7%BD%91%E7%AB%99%E5%88%86%E6%9E%90%E7%9A%84%E6%9C%80%E5%9F%BA%E6%9C%AC%E6%A6%82%E5%BF%B5%EF%BC%882%EF%BC%89%E2%80%94%E2%80%94uv/)

[UV与 VISITS 区别](http://www.szwebanalytics.com/unique-visitor-%E7%BB%9D%E5%AF%B9%E5%94%AF%E4%B8%80%E8%AE%BF%E9%97%AE%E8%80%85-vs-visits-%E8%AE%BF%E9%97%AE%E6%AC%A1%E6%95%B0.html)

[在 PC 上面怎么样计算UV](http://en.wikipedia.org/wiki/Unique_user)

[imei](http://baike.baidu.com/view/90099.htm)

[imsi](http://baike.baidu.com/view/715091.htm)

[udid](http://baike.baidu.com/view/8764986.htm)
