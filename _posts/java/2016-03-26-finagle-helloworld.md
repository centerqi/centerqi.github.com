---
layout: post
category : scala 
tags : [scala]
---
{% include JB/setup %}


尝试用Finagle写了一个HelloWorld，最近在调研各种Rpc框架，虽然还没有领略到他的设计精华。

	import com.twitter.finagle.{Http, Service}
	import com.twitter.finagle.http
	import com.twitter.util.{Await, Future}


	object Main {

	  def main(args: Array[String]): Unit = {

		val service = new Service[http.Request, http.Response] {
		  def apply(req: http.Request): Future[http.Response] ={
			val content= req.getParam("content", "")
			var res = new StringBuilder()
			res.append("input:")
			res.append(content)
			val r = req.response
			r.setContentString(res.toString())
			Future.value(
			  r
			)
		  }
		}

		val server = Http.serve(":8080", service)
		Await.ready(server)

	  }
	}

