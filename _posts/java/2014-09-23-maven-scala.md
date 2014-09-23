---
layout: post
category : scala 
tags : [scala]
---
{% include JB/setup %}


在写spark job的时候，受不了 sbt的语法，还是看 xml 比较方便。

一般的项目，用maven 的archtype就能搞定。

    mvn7 archetype:generate \
    -DarchetypeGroupId=org.scala-tools.archetypes \
    -DarchetypeArtifactId=scala-archetype-simple  \
    -DremoteRepositories=http://scala-tools.org/repo-releases \
    -DgroupId=com.koudai.spark.job \
    -DartifactId=sparkjob \
    -Dversion=1.1.0

这语句会自动生成项目的框架，并且自动生成pom.xml

    <project xmlns="http://maven.apache.org/POM/4.0.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://maven.apache.org/POM/4.0.0 http://maven.apache.org/maven-v4_0_0.xsd">
      <modelVersion>4.0.0</modelVersion>
      <groupId>com.koudai.spark.job</groupId>
      <artifactId>sparkjob</artifactId>
      <version>1.1.0</version>
      <name>${project.artifactId}</name>
      <description>My wonderfull scala app</description>
      <inceptionYear>2010</inceptionYear>
      <licenses>
        <license>
          <name>My License</name>
          <url>http://....</url>
          <distribution>repo</distribution>
        </license>
      </licenses>

      <properties>
        <maven.compiler.source>1.5</maven.compiler.source>
        <maven.compiler.target>1.5</maven.compiler.target>
        <encoding>UTF-8</encoding>
        <scala.version>2.10.4</scala.version>
        <scala.binary.version>2.10</scala.binary.version>
        <project.version>1.1.0</project.version>

      </properties>

    <!--
      <repositories>
        <repository>
          <id>scala-tools.org</id>
          <name>Scala-Tools Maven2 Repository</name>
          <url>http://scala-tools.org/repo-releases</url>
        </repository>
      </repositories>

      <pluginRepositories>
        <pluginRepository>
          <id>scala-tools.org</id>
          <name>Scala-Tools Maven2 Repository</name>
          <url>http://scala-tools.org/repo-releases</url>
        </pluginRepository>
      </pluginRepositories>
    -->
      <dependencies>
        <dependency>
          <groupId>org.scala-lang</groupId>
          <artifactId>scala-library</artifactId>
          <version>${scala.version}</version>
        </dependency>
        <dependency>
          <groupId>org.apache.spark</groupId>
          <artifactId>spark-core_${scala.binary.version}</artifactId>
          <version>${project.version}</version>
          <scope>provided</scope>
        </dependency>
        <dependency>
          <groupId>org.apache.spark</groupId>
          <artifactId>spark-streaming_${scala.binary.version}</artifactId>
          <version>${project.version}</version>
          <scope>provided</scope>
        </dependency>

        <dependency>
          <groupId>org.apache.spark</groupId>
          <artifactId>spark-streaming-flume_${scala.binary.version}</artifactId>
          <version>${project.version}</version>
        </dependency>
         <dependency>
          <groupId>com.twitter</groupId>
          <artifactId>algebird-core_${scala.binary.version}</artifactId>
          <version>0.1.11</version>
        </dependency>
        <dependency>
          <groupId>com.github.scopt</groupId>
          <artifactId>scopt_${scala.binary.version}</artifactId>
          <version>3.2.0</version>
        </dependency>
     

        <!-- Test  -->
        <dependency>
          <groupId>junit</groupId>
          <artifactId>junit</artifactId>
          <version>4.8.1</version>
          <scope>test</scope>
        </dependency>
        <dependency>
          <groupId>org.scala-tools.testing</groupId>
          <artifactId>specs_2.10</artifactId>
          <version>1.6.9</version>
          <scope>test</scope>
        </dependency>
        <dependency>
          <groupId>org.scalatest</groupId>
          <artifactId>scalatest</artifactId>
          <version>1.2</version>
          <scope>test</scope>
        </dependency>
      </dependencies>

      <build>
        <sourceDirectory>src/main/scala</sourceDirectory>
        <testSourceDirectory>src/test/scala</testSourceDirectory>
        <plugins>
          <plugin>
            <groupId>org.scala-tools</groupId>
            <artifactId>maven-scala-plugin</artifactId>
            <version>2.15.0</version>
            <executions>
              <execution>
                <goals>
                  <goal>compile</goal>
                  <!-- <goal>testCompile</goal> -->
                </goals>
                <configuration>
                  <args>
                    <arg>-make:transitive</arg>
                    <arg>-dependencyfile</arg>
                    <arg>${project.build.directory}/.scala_dependencies</arg>
                  </args>
                </configuration>
              </execution>
            </executions>
          </plugin>
          <plugin>
            <groupId>org.apache.maven.plugins</groupId>
            <artifactId>maven-surefire-plugin</artifactId>
            <version>2.6</version>
            <configuration>
              <useFile>false</useFile>
              <disableXmlReport>true</disableXmlReport>
              <!-- If you have classpath issue like NoDefClassError,... -->
              <!-- useManifestOnlyJar>false</useManifestOnlyJar -->
              <includes>
                <include>**/*Test.*</include>
                <include>**/*Suite.*</include>
              </includes>
            </configuration>
          </plugin>
        </plugins>
      </build>
    </project>


[scala_and_maven_getting_started](https://blogs.oracle.com/arungupta/entry/scala_and_maven_getting_started 'scala_and_maven_getting_started')


[http://www.scala-blogs.org/2008/01/maven-for-scala.html](http://www.scala-blogs.org/2008/01/maven-for-scala.html 'http://www.scala-blogs.org/2008/01/maven-for-scala.html')


如果要把依赖的jar打成一个包，必须加一个插件
在build结点加一个插件

    <plugin>
        <groupId>org.apache.maven.plugins</groupId>
        <artifactId>maven-shade-plugin</artifactId>
        <configuration>
          <shadedArtifactAttached>false</shadedArtifactAttached>
          <outputFile>spark-simple.jar</outputFile>
          <artifactSet>
            <includes>
              <include>*:*</include>
            </includes>
          </artifactSet>
          <filters>
            <filter>
              <artifact>*:*</artifact>
              <excludes>
                <exclude>META-INF/*.SF</exclude>
                <exclude>META-INF/*.DSA</exclude>
                <exclude>META-INF/*.RSA</exclude>
              </excludes>
            </filter>
          </filters>
        </configuration>
        <executions>
          <execution>
            <phase>package</phase>
            <goals>
              <goal>shade</goal>
            </goals>
            <configuration>
              <transformers>
                <transformer implementation="org.apache.maven.plugins.shade.resource.ServicesResourceTransformer" />
                <transformer implementation="org.apache.maven.plugins.shade.resource.AppendingTransformer">
                  <resource>reference.conf</resource>
                </transformer>
                <transformer implementation="org.apache.maven.plugins.shade.resource.DontIncludeResourceTransformer">
                  <resource>log4j.properties</resource>
                </transformer>
              </transformers>
            </configuration>
          </execution>
        </executions>
      </plugin>




