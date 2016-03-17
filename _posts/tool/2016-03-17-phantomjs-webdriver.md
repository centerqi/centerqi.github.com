---
layout: post
category : data 
tags : [data]
---
{% include JB/setup %}

从新浪微博抓取一些数据做调研用，结果发现他把我的帐号给封禁了，无可接受的是直接不让登入成功，出这个策略的人也是醉了，

这也就是不让我正常的看微博了，申诉也没有回复，其实理想的封禁策略是能区分人和机器，而不是全部拍死。

现在很多网页都是各种ajax加载，你直接抓取网页很麻烦，要自己构造ajax请求。

但是现在有一些工具，可以构造一个没有图形界面的浏览器，除了图形界面外，他拥有浏览器应该有的功能，比如dom解析，javascript支持，支持ajax请求之类。

##Headless browser

>A headless browser is a web browser without a graphical user interface.
>Headless browsers provide automated control of a web page in an environment similar to popular web browsers, 
>but are executed via a command line interface or using network communication. 
>They are particularly useful for testing web pages as they are able to render and understand HTML the same way a browser would, 
>including styling elements such as page layout, colour, font selection and execution of JavaScript and AJAX which are usually not available when using other testing methods


##Phantomjs

1. phantomjs就是一个Headless browser.

2. phantomjs完整的实现了WebDriver Wire Protocol

基于上述特点，可以有两种方式去实现数据抓取。

1. 可以用js去实现数据抓取，因为phantomjs直接支持js运行，比如jquery.

2. 用别的高级语言去实现，通过 WebDriver Wire Protocol.


##基于Js的抓取

	var page = require('webpage').create();
	var fs = require('fs'),
	system = require('system');

	page.onConsoleMessage = function(msg) {
		var path="data.lst"
		fs.write(path, msg, 'wa'); //追加写的方式
	}

	function just_wait() {
		setTimeout(function() {
				phantom.exit();
				}, 4000);
	}


	page.open(url, function(status) {
			if(status === "success") {	
			var jqueryJs = 'http://cdn.staticfile.org/jquery/2.0.3/jquery.min.js'; 

			page.includeJs(jqueryJs, function(){ //includeJs会把js当做正常的js请求加载到页面

				page.evaluate(function(keywords) {
				var cardList =	$(".WB_cardwrap.S_bg2.clearfix");
				cardList.each(function(i,obj){
					var weibocontent = $(this).find(".comment_txt").text();
					console.log(weibocontent);
				});
				return;
					},keywords); //传递 keywords参数
				});
		}

	just_wait();	
	});

##注意点
1. page.evaluate 方法是在沙盒中执行的，因此不能访问 phantom 对像，所以console.log()输出是不会到控制台点，除非你设置page.onConsoleMessage 方法.

2. 在page.evaluate中执行的操作，是异步的，可能你的页面操作还没完成，phantom已经结束了，如在 evaluate发起http请求，可能是不成功的,因此最好设置一个just_wait，官方实现了一个Waitfor的方法.

3. includeJs和injectJs是完全不一样的， injectJs是不会把js加载到当面页面中去的。


