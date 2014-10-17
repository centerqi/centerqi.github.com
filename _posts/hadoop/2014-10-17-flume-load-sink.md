---
layout: post
category : hadoop
tags : [spark]
---
{% include JB/setup %}


终于弄明白了Flume是怎么样加载Sink相关组件了。

以type=avro为例,

在flume-ng-node 中的 AbstractConfigurationProvider 加载的了各个组件。



    this.sinkFactory = new DefaultSinkFactory(); //初始化sinkFactory.

在loadSinks中，获取此type的sink class.

    Sink sink = sinkFactory.create(sinkName, context.getString(BasicConfigurationConstants.CONFIG_TYPE)); //sinkName是定义的sink名字，如果type为avro，context.getString(BasicConfigurationConstants.CONFIG_TYPE)为avro.



在看 DefaultSinkFactory 的create方法。

    public Sink create(String name, String type) throws FlumeException {
    Preconditions.checkNotNull(name, "name");
    Preconditions.checkNotNull(type, "type");
    logger.info("Creating instance of sink: {}, type: {}", name, type);
    Class<? extends Sink> sinkClass = getClass(type);
    try {
      Sink sink = sinkClass.newInstance();
      sink.setName(name);
      return sink;
    } catch (Exception ex) {
      throw new FlumeException("Unable to create sink: " + name
          + ", type: " + type + ", class: " + sinkClass.getName(), ex);
    }
    }

    @SuppressWarnings("unchecked")
    @Override
    public Class<? extends Sink> getClass(String type)
    throws FlumeException {
    String sinkClassName = type;
    SinkType sinkType = SinkType.OTHER;
    try {
      sinkType = SinkType.valueOf(type.toUpperCase());
    } catch (IllegalArgumentException ex) {
      logger.debug("Sink type {} is a custom type", type);
    }
    if (!sinkType.equals(SinkType.OTHER)) {
      sinkClassName = sinkType.getSinkClassName(); //得到sinkClassName,如avro为
    }
    try {
      return (Class<? extends Sink>) Class.forName(sinkClassName); //实例化此sinkClassName.
    } catch (Exception ex) {
      throw new FlumeException("Unable to load sink type: " + type
          + ", class: " + sinkClassName, ex);
    }
    }



