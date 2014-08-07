---
layout: post
category : scala 
tags : [scala]
---
{% include JB/setup %}

scala 的条件表达示会返回一个值

###Scala’s if is an expression that results in a value. 


    val filename =
    if (!args.isEmpty) args(0)
    else "default.txt"


###While loops
while 和 do while 是一个loop表达示，不是条件表达示，所以不会有返回值

>The while and do-while constructs are called “loops,” not expressions, because they don’t result in an interesting value.


循环读取只能这样写


    var line = ""
    do {
      line = readLine()
      println("Read: "+ line)
    } while (line != "")

写成这样是不行的

    var line = ""
    while ((line = readLine()) != "") // This doesn’t work!
    println("Read: "+ line)


编译如上代码，会产生一个  

    warning: comparing values of types Unit and String using `!=' will always yield true'`

因为 () 返回的是一个 Unit，而Unit是不等于 ""的。

()是函数调用

    (line = readLine()) 


###For expressions

####Iteration through collections

    val filesHere = (new java.io.File(".")).listFiles
    for (file <- filesHere)
    println(file)

    #产生一个序列
    for (i <- 1 to 4)yield i

    for (i <- 1 until 4)print i

####Filtering
    
    val filesHere = (new java.io.File(".")).listFiles
    for (file <- filesHere if file.getName.endsWith(".scala"))
    println(file)


    #多个filter
    for (
              file <- filesHere
              if file.isFile
              if file.getName.endsWith(".scala")
    ) println(file)

####Nested iteration

    def fileLines(file: java.io.File) =
    scala.io.Source.fromFile(file).getLines().toList
    def grep(pattern: String) =
    for (
            file <- filesHere
            if file.getName.endsWith(".scala");
            line <- fileLines(file)
            if line.trim.matches(pattern)
        ) println(file +": "+ line.trim)
    grep(".*gcd.*")


####Producing a new collection

    def scalaFiles =
    for {
        file <- filesHere
            if file.getName.endsWith(".scala")
    } yield file


####Catching exceptions

    import java.io.FileReader
    import java.io.FileNotFoundException
    import java.io.IOException
    try {
        val f = new FileReader("input.txt")
            // Use and close file
    } catch {
        case ex: FileNotFoundException => // Handle missing file
        case ex: IOException => // Handle other I/O error
    }


    import java.io.FileReader
    val file = new FileReader("input.txt")
    try {
        // Use the file
    } finally {
        file.close()  // Be sure to close the file
    }

    #返回一个值
    import java.net.URL
    import java.net.MalformedURLException
    def urlFor(path: String) =
    try {
        new URL(path)
    } catch {
        case e: MalformedURLException =>
                new URL("http://www.scala-lang.org")


####Match expressions

    val firstArg = if (!args.isEmpty) args(0) else ""
    val friend =
    firstArg match {
    case "salt" => "pepper"
    case "chips" => "salsa"
    case "eggs" => "bacon"
    case _ => "huh?"
    }
    println(friend)}

####break

    import scala.util.control.Breaks._
    import java.io._
    val in = new BufferedReader(new InputStreamReader(System.in))
    breakable {
        while (true) {
            println("? ")
                if (in.readLine() == "") break
        }
    }
