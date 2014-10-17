---
layout: post
category : hadoop
tags : [spark]
---
{% include JB/setup %}


最近准备把flume的代码好好读一次，从头到尾。入口在 flume-ng-node下面的Application

    Application application;
    if(reload) {
        EventBus eventBus = new EventBus(agentName + "-event-bus");
        PollingPropertiesFileConfigurationProvider configurationProvider =
            new PollingPropertiesFileConfigurationProvider(agentName,
                    configurationFile, eventBus, 30);
        components.add(configurationProvider);
        application = new Application(components);
        eventBus.register(application);
    } else {
        PropertiesFileConfigurationProvider configurationProvider =
            new PropertiesFileConfigurationProvider(agentName,
                    configurationFile);
        application = new Application();
        application.handleConfigurationEvent(configurationProvider.getConfiguration());
    }
    application.start();

默认是 30秒重新加载一下配置文件，才用 EventBus来处理。

再来看一下EventBus的接收者:


    @Subscribe
    public synchronized void handleConfigurationEvent(MaterializedConfiguration conf) {
        stopAllComponents();
        startAllComponents(conf);
    }





