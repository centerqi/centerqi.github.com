---
layout: post
category : scala 
tags : [scala]
---
{% include JB/setup %}


在stack overflow 上面看到一个有趣的问题，也是我一直没搞明白了，粗略的看了一下。

应该是这样一个过程

    parse program
    types checked
    do erasure
    implicit conversion
    byte-code generated
    optimize

byte-code generated 应该是 用asm之类的工具生成的。


先记起来，下次好好的去理解一下他的整个编译过程


[How is scala generating byte code? Using some libraries like ASM, or write binary directly?](http://stackoverflow.com/questions/13380807/how-is-scala-generating-byte-code-using-some-libraries-like-asm-or-write-binar 'How is scala generating byte code? Using some libraries like ASM, or write binary directly?')

[what-is-the-order-of-the-scala-compiler-phases](http://stackoverflow.com/questions/4527902/what-is-the-order-of-the-scala-compiler-phases 'http://stackoverflow.com/questions/4527902/what-is-the-order-of-the-scala-compiler-phases')

[GenBCode](https://github.com/magarciaEPFL/scala/blob/GenBCodeOpt/src/compiler/scala/tools/nsc/backend/jvm/GenBCode.scala 'https://github.com/magarciaEPFL/scala/blob/GenBCodeOpt/src/compiler/scala/tools/nsc/backend/jvm/GenBCode.scala')
