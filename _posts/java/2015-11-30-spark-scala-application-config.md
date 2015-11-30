---
layout: post
category : java 
tags : [scala]
---
{% include JB/setup %}


Spark job中要读取各种配置文件，发现用resources这种方式虽然不是最好的，也许可以直接在命令行指定资源文件，先用这种土的方式解决问题。

##建立资源目录

    mkdir src/main/resources

##maven引入资源管理插件


    <plugin>
        <groupId>org.apache.maven.plugins</groupId>
        <artifactId>maven-resources-plugin</artifactId>
        <version>2.3</version>
        <configuration>
                <encoding>UTF-8</encoding>
        </configuration>
    </plugin>



##在resources下面创建application.conf文件

    kafka {
      topics = "test"
      brokers = "localhost:9092,localhost:9093,localhost:9094"
    }


##添加config依赖

scala中用的较多的依赖是 typesafe config

    <dependency>
        <groupId>com.typesafe</groupId>
        <artifactId>config</artifactId>
        <version>1.2.1</version>
    </dependency>


##在代码中使用

    import com.typesafe.config.ConfigFactory
    val conf = ConfigFactory.load
    val configtopics = conf.getString("kafka.topics").split(",").toSet

