---
layout: post
category : scala 
tags : [scala]
---
{% include JB/setup %}

Scala 打包工具还是很不错的，也有maven那样的功能，把整个项目的依赖都打包成一个可执行的jar(不是真正的执行，必须要用jvm).

项目的布局还是有要求的。

用g8命令生成基本的框架，目录如下

    /workspace/scala/scalaproxy/
                                target/
                                src/
                                project/assembly.sbt
                                build.sbt
                                assembly.sbt



/workspace/scala/scalaproxy/ 为项目的ROOT目录,项目名称为scalaproxy


1. 在project中创建 assembly.sbt文件，内容如下

    addSbtPlugin("com.eed3si9n" % "sbt-assembly" % "0.11.2")

2. 在要目录创建 assembly.sbt文件,内容如下

    `import AssemblyKeys._`

    assemblySettings

3. 在build.sbt内容的第一行添加
    
    `import AssemblyKeys._`

4. 在build.sbt内容的最后一行添加

    mainClass in assembly := Some("com.koudai.scalaproxy.FileTest")

完整的build.sbt文件如下

    `import AssemblyKeys._` 

    name := "scalaproxy"

    organization := "com.koudai"

    version := "0.0.1"

    scalaVersion := "2.10.3"

    libraryDependencies ++= Seq(
            "org.scalatest" % "scalatest_2.10" % "2.0" % "test" withSources() withJavadoc(),
            "org.scalacheck" %% "scalacheck" % "1.10.0" % "test" withSources() withJavadoc(),
            "org.slf4j" % "slf4j-api" % "1.6.4",
            "org.apache.commons" % "commons-lang3" % "3.3.2"
    )

    initialCommands := "import com.koudai.scalaproxy._"

    mainClass in assembly := Some("com.koudai.scalaproxy.FileTest")

[scala-single-file-executable-jar](http://blog.bstpierre.org/scala-single-file-executable-jar 'http://blog.bstpierre.org/scala-single-file-executable-jar')

[sbt-assembly](https://github.com/sbt/sbt-assembly 'https://github.com/sbt/sbt-assembly')


