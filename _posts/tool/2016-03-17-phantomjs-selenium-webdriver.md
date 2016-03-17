---
layout: post
category : data 
tags : [data]
---
{% include JB/setup %}

可以用 高级语言与 phantomjs，因为phantomjs已经完整支持WebDriver Wire Protocol.

先启动 phantomjs的WebDriver 

	/usr/local/webserver/phantomjs-2.1.1-linux-x86_64/bin/phantomjs --proxy=10.1.5.108:9999 --webdriver=0.0.0.0:9099


然后在动态语言中去连接webdriver，因为我喜欢用Scala，如下是Scala的代码


添加Selenium依赖

	<dependency>
	<groupId>org.seleniumhq.selenium</groupId>
	<artifactId>selenium-java</artifactId>
	<version>2.52.0</version>
	</dependency>


Sacal代码


import java.net.URL;
import org.openqa.selenium.remote.DesiredCapabilities;
import org.openqa.selenium.remote.RemoteWebDriver;

object Main {

	def main(args: Array[String]): Unit = {

		val driver = new RemoteWebDriver(new URL("http://10.1.13.190:9099"),DesiredCapabilities.phantomjs())
		driver.get("http://ip.taobao.com/")
		println(driver.getPageSource)

	}

}
