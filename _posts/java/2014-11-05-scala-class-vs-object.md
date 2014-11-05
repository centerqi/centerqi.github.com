---
layout: post
category : scala 
tags : [scala]
---
{% include JB/setup %}

scala 用class 和object这两个来定义一个对象或者类(说类和对象感觉都不太准确)，

object相当于实现了java class的一种 Singleton实现，主要是因为scala class中没有像java 那样的static 属于.

>Scala is more object-oriented than Java because in Scala we cannot have static members. 
>Instead, Scala has singleton objects. 
>A singleton is a class that can have only one instance, i.e., object. 
>You create singleton using the keyword object instead of class keyword. 
>Since you can't instantiate a singleton object, you can't pass parameters to the primary constructor. 


	class Person{
	   private var name=""
	   private var age=0

	   def getName() : String = name
	   def setName(nm: String) { name = nm }

	   def getAge() : Int = age
	   def setAge(ag: Int) {age=ag}
	}

	object Person{
	   var staticInt=0
	   val person=new Person
	   person.name="Rasesh Mori"
	   person.age=24

	   def greetPerson(): String = "Hello "+person.name+" static int: "+staticInt
	}

	Person.staticInt=1
	println(Person.greetPerson())

	$ scala person.scala
	Hello Rasesh Mori static int: 1

	[http://raseshmori.wordpress.com/2013/06/20/scala-part-4-classes-objects/](http://raseshmori.wordpress.com/2013/06/20/scala-part-4-classes-objects/ 'scala-part-4-classes-objects/')
	[scala_classes_objects](http://www.tutorialspoint.com/scala/scala_classes_objects.htm 'http://www.tutorialspoint.com/scala/scala_classes_objects.htm')