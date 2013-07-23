---
layout: post
category : python
tags : [python]
---
{% include JB/setup %}

php与python的很多方式都想通, 如在eval  

eval可以把一个字符串变成变量

    //itemInfo为一个class
	objbr = itemInfo()
	objfav = itemInfo()
	objgmv = itemInfo()

    //用eval把字符串转换成对象
	for obj in ['objfav','objbr','objgmv']:
		obj = eval(obj)
		print ("%s,%s,%s,%s,%s,%s")%(maxDay,obj.type, obj.uv/obj.dayCnt, obj.productCnt/obj.dayCnt, obj.price/obj.dayCnt, obj.productNumberTimes/obj.dayCnt)



