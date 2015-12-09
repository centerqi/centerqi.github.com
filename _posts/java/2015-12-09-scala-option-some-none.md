---
layout: post
category : java 
tags : [scala]
---
{% include JB/setup %}


引起我对option注意的是在其官网有如下一段牛B的话

>Represents optional values. Instances of Option are either an instance of scala.Some or the object None.

>The most idiomatic way to use an scala.Option instance is to treat it as a collection or monad and use map,flatMap, filter, or foreach:

与我一惯的用法不一样，我以前是一般是在模式匹配时用的最多。

仔细的去看一下官网的例子，发现scala在设计这一特征时，也包含了他的设计哲学，简化代码量，如果没有这一特征，那像java那样，会出现很多的 null确认。

最常见的场景，如想根据某id获取某用户的信息，如果id存在，当然一切操作正常，如果id不存在，就会出现NullPointer异常之类的信息

    val customer = Customers.findById(1234) 
    customer.getAccount(FUNSTUFF).getLastInterest.getAmount

java中经典的解决方法如下


    if(customer != null){
        if(customer.getAccount(FUNSTUFF) != null){
            if(customer.getAccount(FUNSTUFF).getLastInterest != null){

            }
        }
    }

以上代码不但量大，而且很不优雅，scala是讲究优雅的。

当然，在scala中也可以依照上面这种语法去写，但那为何要用scala而不用java呢？


如果利用scala中的option特征去做，就非常优雅了。

option的继承关系。

    sealed trait Option[A] 
    case class Some[A](a: A) extends Option[A] 
    case class None[A] extends Option[A]

优点

>Values that may or may not exist now stated in type system 

>Clearly shows possible non-existence 

>Compiler forces you to deal with it 

>You won’t accidentally rely on value

这是我看有人这样来评价他的优点，我个人的理解是这样的。

1. 值存在与不存在进行了明确的定义。

2. 编译器会强行你去处理，减少运行时的风险。


创建option类型

    val o = Some(3)
    val n = None


    val capitals = Map("France" -> "Paris", "Japan" -> "Tokyo")
    println("capitals.get( \"France\" ) : " +  capitals.get( "France" ))
    println("capitals.get( \"India\" ) : " +  capitals.get( "India" ))

输出

    capitals.get( "France" ) : Some(Paris)
    capitals.get( "India" ) : None


我习惯的方式是用他去做模式匹配

    println("show(capitals.get( \"Japan\")) : " +  show(capitals.get( "Japan")) )
    println("show(capitals.get( \"India\")) : " +  show(capitals.get( "India")) )

     def show(x: Option[String]) = x match {
          case Some(s) => s
          case None => "?"
    }

    show(capitals.get( "Japan")) : Tokyo
    show(capitals.get( "India")) : ?



用getOrElse

    val a:Option[Int] = Some(5)

    val b:Option[Int] = None 
          
    println("a.getOrElse(0): " + a.getOrElse(0) )
    println("b.getOrElse(10): " + b.getOrElse(10) )

输出

    a.getOrElse(0): 5
    b.getOrElse(10): 10


用isEmpty

    val a:Option[Int] = Some(5)
    val b:Option[Int] = None 
          
    println("a.isEmpty: " + a.isEmpty )
    println("b.isEmpty: " + b.isEmpty )

    a.isEmpty: false
    b.isEmpty: true

[http://www.tutorialspoint.com/scala/scala_options.htm](http://www.tutorialspoint.com/scala/scala_options.htm 'http://www.tutorialspoint.com/scala/scala_options.htm')


如果仅仅是上面这些用法，他开头那一段牛B的话，就完全没有理解到位。

用在集合上,下面这代码能省很多事

    t = Some("SoyaWannaBurger")
    println(t map(_.toString) filter(_.length > 0) map(_.toUpperCase))


[http://www.cis.upenn.edu/~matuszek/cis554-2011/Pages/scalas-option-type.html](http://www.cis.upenn.edu/~matuszek/cis554-2011/Pages/scalas-option-type.html 'http://www.cis.upenn.edu/~matuszek/cis554-2011/Pages/scalas-option-type.html')


其实最经典的是官网的例子

    val name: Option[String] = request getParameter "name"
    val upper = name map { _.trim } filter { _.length != 0 } map { _.toUpperCase }
    println(upper getOrElse "")

其实他相当于

    val upper = for {
      name <- request getParameter "name"
      trimmed <- Some(name.trim)
      upper <- Some(trimmed.toUpperCase) if trimmed.length != 0
    } yield upper
    println(upper getOrElse "")

发现map函数的说明很到位

    final def
    map[B](f: (A) ⇒ B): Option[B]
    Returns a scala.Some containing the result of applying f to this scala.Option's value if this scala.Option is nonempty.

[http://www.scala-lang.org/api/current/index.html#scala.Option](http://www.scala-lang.org/api/current/index.html#scala.Option 'http://www.scala-lang.org/api/current/index.html#scala.Option')



具体参考
[http://blog.originate.com/blog/2014/06/15/idiomatic-scala-your-options-do-not-match/](http://blog.originate.com/blog/2014/06/15/idiomatic-scala-your-options-do-not-match/ 'http://blog.originate.com/blog/2014/06/15/idiomatic-scala-your-options-do-not-match/')

[http://danielwestheide.com/blog/2012/12/19/the-neophytes-guide-to-scala-part-5-the-option-type.html](http://danielwestheide.com/blog/2012/12/19/the-neophytes-guide-to-scala-part-5-the-option-type.html 'http://danielwestheide.com/blog/2012/12/19/the-neophytes-guide-to-scala-part-5-the-option-type.html')

[http://adit.io/posts/2013-04-17-functors,_applicatives,_and_monads_in_pictures.html](http://adit.io/posts/2013-04-17-functors,_applicatives,_and_monads_in_pictures.html 'http://adit.io/posts/2013-04-17-functors,_applicatives,_and_monads_in_pictures.html')

[http://www.slideshare.net/jankrag/introduction-to-option-monad-in-scala](http://www.slideshare.net/jankrag/introduction-to-option-monad-in-scala 'http://www.slideshare.net/jankrag/introduction-to-option-monad-in-scala')

