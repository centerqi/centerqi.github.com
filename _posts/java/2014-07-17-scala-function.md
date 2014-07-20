---
layout: post
category : scala 
tags : [scala]
---
{% include JB/setup %}


Scala 函数定义 

    def functionName ([list of parameters]) : [return type] = {
       function body
       return [expr]
    }

Demo

    object add{
       def addInt( a:Int, b:Int ) : Int = {
          var sum:Int = 0
          sum = a + b

          return sum
       }
    }


Call by name

这个比较有意思，t: => Long 传递的是一个返回Long类型的一个函数


    object Test {
       def main(args: Array[String]) {
            delayed(time());
       }

       def time() = {
          println("Getting time in nano seconds")
          System.nanoTime
       }
       def delayed( t: => Long ) = {
          println("In delayed method")
          println("Param: " + t)
          t
       }
    }


可变参数

    object Test {
       def main(args: Array[String]) {
            printStrings("Hello", "Scala", "Python");
       }
       def printStrings( args:String* ) = {
          var i : Int = 0;
          for( arg <- args ){
             println("Arg value[" + i + "] = " + arg );
             i = i + 1;
          }
       }
    }

默认参数

    object Test {
       def main(args: Array[String]) {
            println( "Returned Value : " + addInt() );
       }
       def addInt( a:Int=5, b:Int=7 ) : Int = {
          var sum:Int = 0
          sum = a + b

          return sum
       }
    }

Nested Functions

    object Test {
       def main(args: Array[String]) {
          println( factorial(0) )
          println( factorial(1) )
          println( factorial(2) )
          println( factorial(3) )
       }

       def factorial(i: Int): Int = {
          def fact(i: Int, accumulator: Int): Int = {
             if (i <= 1)
                accumulator
             else
                fact(i - 1, i * accumulator)
          }
          fact(i, 1)
       }
    }

Partially Applied Functions


这个更有意思

    import java.util.Date

    object Test {
       def main(args: Array[String]) {
          val date = new Date
          log(date, "message1" )
          Thread.sleep(1000)
          log(date, "message2" )
          Thread.sleep(1000)
          log(date, "message3" )
       }

       def log(date: Date, message: String)  = {
         println(date + "----" + message)
       }
    }
这样写很累，有一种方法可以省一个参数

可以把一个函数给一个变量

    import java.util.Date

    object Test {
       def main(args: Array[String]) {
          val date = new Date
          val logWithDateBound = log(date, _ : String)

          logWithDateBound("message1" )
          Thread.sleep(1000)
          logWithDateBound("message2" )
          Thread.sleep(1000)
          logWithDateBound("message3" )
       }

       def log(date: Date, message: String)  = {
         println(date + "----" + message)
       }
    }

Named Arguments

>In a normal function call, the arguments in the call are matched one by one in the order of the parameters of the called function. Named arguments allow you to pass arguments to a function in a different order. The syntax is simply that each argument is preceded by a parameter name and an equals sign. Following is a simple example to show the concept:

    object Test {
       def main(args: Array[String]) {
            printInt(b=5, a=7);
       }
       def printInt( a:Int, b:Int ) = {
          println("Value of a : " + a );
          println("Value of b : " + b );
       }
    }


Higher-Order Functions

这个很独特点，应该算是一种设计模式

感觉有点像抽象工厂

具体列子


    val  filter = ( predicate :Int => Boolean, xs :List[Int] )  =>  {
     
        for(  x <- xs;  if predicate( x )  )  yield x
    }

    val isFactorOf  =  ( num :Int ) => {
     
        ( factor :Int ) => num % factor == 0
    }


    val factorsOfHundred =  filter( isFactorOf( 100 ), candidates )

[http://gleichmann.wordpress.com/2010/11/28/high-higher-higher-order-functions/](http://gleichmann.wordpress.com/2010/11/28/high-higher-higher-order-functions/ 'http://gleichmann.wordpress.com/2010/11/28/high-higher-higher-order-functions/')


Anonymous Functions

>Scala provides a relatively lightweight syntax for defining anonymous functions. Anonymous functions in source code are called function literals and at run time, function literals are instantiated into objects called function values.

>Scala supports first-class functions, which means you can express functions in function literal syntax, i.e., (x: Int) => x + 1, and that functions can be represented by objects, which are called function values. The following expression creates a successor function for integers:


    var mul = (x: Int, y: Int) => x*y


Currying Functions

我真不知道这种为什么有存在的必要

>Currying transforms a function that takes multiple parameters into a chain of functions, each taking a single parameter. Curried functions are defined with multiple parameter lists, as follows:

    def strcat(s1: String)(s2: String) = s1 + s2
