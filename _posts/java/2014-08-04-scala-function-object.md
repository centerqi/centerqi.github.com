---
layout: post
category : scala 
tags : [scala]
---
{% include JB/setup %}


在看scala的教程，关于类的基本定义

    class Rational(n: Int, d: Int) {
      require(d != 0)
      private val g = gcd(n.abs, d.abs)
      val numer = n / g
      val denom = d / g
      def this(n: Int) = this(n, 1)
      def + (that: Rational): Rational =
        new Rational(
          numer * that.denom + that.numer * denom,
          denom * that.denom
        )
      def + (i: Int): Rational =
        new Rational(numer + i * denom, denom)
      def - (that: Rational): Rational =
        new Rational(
          numer * that.denom - that.numer * denom,
          denom * that.denom
        )
      def - (i: Int): Rational =
        new Rational(numer - i * denom, denom)
      def * (that: Rational): Rational =
        new Rational(numer * that.numer, denom * that.denom)
      def * (i: Int): Rational =
        new Rational(numer * i, denom)
      def / (that: Rational): Rational =
        new Rational(numer * that.denom, denom * that.numer)
      def / (i: Int): Rational =
        new Rational(numer, denom * i)
      override def toString = numer +"/"+ denom
    private def gcd(a: Int, b: Int): Int = if (b == 0) a else gcd(b, a % b)
    }


##Constructing a Rational

    class Rational(n: Int, d: Int)

这样也可以简单的定义一个类，如果没有body，就没必要用大括号括起来


##Reimplementing the toString method

    override def toString = numer +"/"+ denom

加override 就可以了。


##Checking preconditions

检查必要的参数

    require(d != 0)

require 是 Predef 定义的方法，在scala中是全局可见的。


##Adding fields

    val numer: Int = n
    val denom: Int = d

##Self references

    def lessThan(that: Rational) =
              this.numer * that.denom < that.numer * this.denom

用this这个keywords就完事。


##Auxiliary constructors

辅助构造函数,像java那样可以有多个构造函数

    def this(n: Int) = this(n, 1) // auxiliary constructor

    val y = new Rational(3)

##Private fields and methods

    private def gcd(a: Int, b: Int): Int = if (b == 0) a else gcd(b, a % b)

##Defining operators

就像定义方法一样

    def + (that: Rational): Rational =
    new Rational(
      numer * that.denom + that.numer * denom,
      denom * that.denom
    )
    def * (that: Rational): Rational =
    new Rational(numer * that.numer, denom * that.denom)


##Method overloading

只要保证参数类型或者参数过数不一样就可以了

     def + (that: Rational): Rational =
        new Rational(
          numer * that.denom + that.numer * denom,
          denom * that.denom
        )
      def + (i: Int): Rational =
        new Rational(numer + i * denom, denom)
