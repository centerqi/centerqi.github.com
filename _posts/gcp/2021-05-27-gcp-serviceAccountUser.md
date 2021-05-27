---
layout: post
category : GCP
tags : [gcp]
---
{% include JB/setup %}

## 背景：

大量的测试机和开发机，晚上12点到早上8点是没有任何服务要运行的，所以为了节省一些费用，尝试实现一个定时开机，关机，其实原理非常简单，

就是在 gcp 上面跑了几个 functions 服务，然后再设置一个 Cloud Scheduler，最近通过 Pub/Sub 服务，触发这个 functions.


[使用 Cloud Scheduler 调度计算实例](https://cloud.google.com/scheduler/docs/start-and-stop-compute-engine-instances-on-a-schedule?hl=zh-cn)

## 遇到了权限的问题

我没有用系统默认的服务账户，并且全部是通过 api 来实现，没有在网页上面操作，因为通过 gcloud 命令行操作，有利于我对整个系统认识的更加清晰。

创建完服务账户，并且设置了 functions amdin 的角色，就是跑不成功，后来查了一下文档，就是自己设置的服务账户，并且设置 serviceAccountUser 角色。

	Service Account User(roles/iam.serviceAccountUser)


<img src="/assets/images/gcp_deploy_functions.png" />


一定要选择上面两种权限才能通过 api 去发布函数。


## 原因

1. 自己创建的服务账户并没有权限（Service Account User），创建了也要手动授权这个权限。


## 参考

[google serviceAccountUser](https://www.akiicat.com/2019/10/20/Google-Cloud-Platform/setup-google-cloud-for-third-application/)
