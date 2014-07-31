---
layout: post
category : scala 
tags : [scala]
---
{% include JB/setup %}


基本类型
编译成字节码的时候，会替换成原生的java类型

    Value type                              Range

    Byte                                    8-bit signed 

    Short                                   16-bit signed

    Int                                     32-bit signed

    Long                                    64-bit signed

    Char                                    16-bit unsigned Unicode character 

    String                                  a sequence of Chars

    Float                                   32-bit IEEE 754 single-precision float

    Double                                  64-bit IEEE 754 double-precision float

    Boolean                                 true or false


char 类型

    val d = '\u77426'


###Operators are methods

Scala比较有意思的是，操作符就是方法调用

>these operators are actually just a nice syntax for ordinary method calls.

    val sum = 1 + 2    // Scala invokes (1).+(2)

    val s = "Hello, world!"

    s indexOf 'o'     // Scala invokes s.indexOf(’o’)

如果方法有参数，得用圆括号

    s indexOf ('o', 5) // Scala invokes s.indexOf(’o’, 5)

任何方法都可以当操作符用

    s.indexOf('o') //这是方法调用
    s indexOf 'o' //这是操作符

###infix operator notation

>nfix operator notation, which means the method to invoke sits between the object and the parameter or parameters you wish to pass to the method, as in “7 + 2”. 

 val v1= 7 + 8


###prefix operator notation

>In prefix notation, you put the method name before the object on which you are invoking the method, for example, the ‘-’ in -7

scala 可以用 unary_ 方法处理 prefix notation

  val v2 =  (2.0).unary_-

###postfix operator notation

>In postfix notation, you put the method after the object, for example, the “toLong” in “7 toLong”

    val s = "Hello, world!"

    s.toLowerCase // s toLowerCase

###操作符优先级别

    (all other special characters) 
    */%
    +-
    :
    =! <> &
    ˆ
    |
    (all letters)
    (all assignment operators)


###Decimal

这种情况最好用java.math.BigDecimal

    import java.math.BigDecimal
    val mData = new BigDecimal("9.655").setScale(2, BigDecimal.ROUND_HALF_UP);

但是 scala也对 BigDecimal进行了包装，放在 scala.math.BigDecimal

方便了不少，但是感觉还是直接用 BigDecimal就可以了。



