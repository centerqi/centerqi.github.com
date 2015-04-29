---
layout: post
category : hadoop
tags : [azkaban]
---
{% include JB/setup %}


Azkaban 看了一下代码，特别读了一下从zip解析到整个flow的过程。

主要代码在 azkaban.utils.DirectoryFlowLoader 中

	public void loadProjectFlow(File baseDirectory) {
		propsList = new ArrayList<Props>();
		flowPropsList = new ArrayList<FlowProps>();
		jobPropsMap = new HashMap<String, Props>();
		nodeMap = new HashMap<String, Node>();
		flowMap = new HashMap<String, Flow>();
		errors = new HashSet<String>();
		duplicateJobs = new HashSet<String>();
		nodeDependencies = new HashMap<String, Map<String, Edge>>();
		rootNodes = new HashSet<String>();
		flowDependencies = new HashMap<String, Set<String>>();
		
		// Load all the props files and create the Node objects
		loadProjectFromDir(baseDirectory.getPath(), baseDirectory, null);
		
		// Create edges and find missing dependencies
		resolveDependencies();
		
		// Create the flows.
		buildFlowsFromDependencies();
		
		// Resolve embedded flows
		resolveEmbeddedFlows();
	}




###loadProjectFromDir

主要是解析各个job,把各个job变成node,node的名字就是job的名字.

	Node node = new Node(jobName);
	nodeMap.put(jobName, node);


###resolveDependencies

主要是解析依赖关系，有几个重要的数据结构。

//Edge是一个重要的概念,在azkaban 里面 
	
	Edge edge = new Edge(dependencyName, node.getId());

//Edge的实现其实就是一个依赖结构,toId是依赖于 fromId

	public Edge(String fromId, String toId) {
			this.sourceId = fromId;
			this.targetId = toId;
	}

//保存每一个结点的依赖列表

	dependencies = new HashMap<String, Edge>()
	dependencies.put(dependencyName, edge);
	nodeDependencies.put(node.getId(), dependencies);



###buildFlowsFromDependencies

这个函数是重点

	//找到所有非root的结点(azkaban是一倒挂的树,最后执行的结点为root结点)
	HashSet<String> nonRootNodes = new HashSet<String>();
	for (Map<String, Edge> edges: nodeDependencies.values()) {
		for (String sourceId: edges.keySet()) {
			nonRootNodes.add(sourceId);
		}
	}

	// Now create flows. Bad flows are marked invalid
	Set<String> visitedNodes = new HashSet<String>();
	for (Node base: nodeMap.values()) {

          
    // Root nodes can be discovered when parsing jobs
	if (rootNodes.contains(base.getId()) || !nonRootNodes.contains(base.getId())) 


	//找到root后，创建一个flow
	rootNodes.add(base.getId());
	Flow flow = new Flow(base.getId());//流的名字都是以root结点命名的。

	constructFlow(flow, base, visitedNodes);
	flow.initialize();


###constructFlow
	
	Map<String, Edge> dependencies = nodeDependencies.get(node.getId());
	// 递归构建所有edge
	flow.addEdge(edge);
	Node sourceNode = nodeMap.get(edge.getSourceId());
	constructFlow(flow, sourceNode, visited);


###addEdge
addEdge主要是用来建立依赖于被依赖的关系

	public void addEdge(Edge edge) {
		String source = edge.getSourceId();
		String target = edge.getTargetId();
		if (edge.hasError()) {
			addError("Error on " + edge.getId() + ". " + edge.getError());
		}

		Set<Edge> sourceSet = getEdgeSet(outEdges, source);
		sourceSet.add(edge);
		
		Set<Edge> targetSet = getEdgeSet(inEdges, target);
		targetSet.add(edge);
		
		edges.put(edge.getId(), edge);
	}


	###当flow load完成后，会变成json，写入数据库


				{
	"id": "c",
	"success.email": [],
	"edges": [
	    {
	        "source": "a",
	        "target": "b"
	    },
	    {
	        "source": "a4",
	        "target": "b"
	    },
	    {
	        "source": "a5",
	        "target": "b"
	    },
	    {
	        "source": "a2",
	        "target": "b1"
	    },
	    {
	        "source": "a6",
	        "target": "b"
	    },
	    {
	        "source": "a3",
	        "target": "b"
	    },
	    {
	        "source": "a4",
	        "target": "b1"
	    },
	    {
	        "source": "a6",
	        "target": "b1"
	    },
	    {
	        "source": "a",
	        "target": "b1"
	    },
	    {
	        "source": "a1",
	        "target": "b"
	    },
	    {
	        "source": "b",
	        "target": "c"
	    },
	    {
	        "source": "a1",
	        "target": "b1"
	    },
	    {
	        "source": "b1",
	        "target": "c"
	    },
	    {
	        "source": "a3",
	        "target": "b1"
	    },
	    {
	        "source": "a5",
	        "target": "b1"
	    },
	    {
	        "source": "a2",
	        "target": "b"
	    }
	],
	"project.id": 1,
	"failure.email": [],
	"nodes": [
	    {
	        "propSource": null,
	        "id": "b",
	        "jobType": "command",
	        "priority": 8,
	        "layout": {
	            "level": 1
	        },
	        "jobSource": "b.job",
	        "expectedRuntime": 1
	    },
	    {
	        "propSource": null,
	        "id": "c",
	        "jobType": "command",
	        "priority": 9,
	        "layout": {
	            "level": 2
	        },
	        "jobSource": "c.job",
	        "expectedRuntime": 1
	    },
	    {
	        "propSource": null,
	        "id": "b1",
	        "jobType": "command",
	        "priority": 7,
	        "layout": {
	            "level": 1
	        },
	        "jobSource": "b1.job",
	        "expectedRuntime": 1
	    },
	    {
	        "propSource": null,
	        "id": "a",
	        "jobType": "command",
	        "priority": 4,
	        "layout": {
	            "level": 0
	        },
	        "jobSource": "a.job",
	        "expectedRuntime": 1
	    },
	    {
	        "propSource": null,
	        "id": "a1",
	        "jobType": "command",
	        "priority": 1,
	        "layout": {
	            "level": 0
	        },
	        "jobSource": "a1.job",
	        "expectedRuntime": 1
	    },
	    {
	        "propSource": null,
	        "id": "a2",
	        "jobType": "command",
	        "priority": 2,
	        "layout": {
	            "level": 0
	        },
	        "jobSource": "a2.job",
	        "expectedRuntime": 1
	    },
	    {
	        "propSource": null,
	        "id": "a3",
	        "jobType": "command",
	        "priority": 3,
	        "layout": {
	            "level": 0
	        },
	        "jobSource": "a3.job",
	        "expectedRuntime": 1
	    },
	    {
	        "propSource": null,
	        "id": "a4",
	        "jobType": "command",
	        "priority": 4,
	        "layout": {
	            "level": 0
	        },
	        "jobSource": "a4.job",
	        "expectedRuntime": 1
	    },
	    {
	        "propSource": null,
	        "id": "a5",
	        "jobType": "command",
	        "priority": 5,
	        "layout": {
	            "level": 0
	        },
	        "jobSource": "a5.job",
	        "expectedRuntime": 1
	    },
	    {
	        "propSource": null,
	        "id": "a6",
	        "jobType": "command",
	        "priority": 6,
	        "layout": {
	            "level": 0
	        },
	        "jobSource": "a6.job",
	        "expectedRuntime": 1
	    }
	],
	"mailCreator": "default",
	"layedout": false,
	"type": "flow",
	"props": [],
	"metadata": {},
	"version": 29
	}


###每次执行某一flow时候，都会从数据库中load flow，然后写入execute_flow表中

	{
	    "attempt": 0,
	    "submitUser": "azkaban",
	    "status": "PREPARING",
	    "updateTime": -1,
	    "proxyUsers": [
	        "azkaban"
	    ],
	    "submitTime": 1430298348449,
	    "executionId": 12770,
	    "projectId": 1,
	    "properties": [],
	    "endTime": -1,
	    "type": null,
	    "executionPath": null,
	    "version": 29,
	    "id": null,
	    "startTime": -1,
	    "executionOptions": {
	        "successEmails": [],
	        "pipelineLevel": null,
	        "mailCreator": "default",
	        "concurrentOption": "ignore",
	        "failureAction": "FINISH_CURRENTLY_RUNNING",
	        "failureEmailsOverride": false,
	        "notifyOnFirstFailure": false,
	        "successEmailsOverride": false,
	        "pipelineExecId": null,
	        "queueLevel": 0,
	        "flowParameters": {},
	        "notifyOnLastFailure": false,
	        "disabled": [],
	        "failureEmails": []
	    },
	    "priority": 0,
	    "nodes": [
	        {
	            "attempt": 0,
	            "startTime": -1,
	            "id": "b",
	            "outNodes": [
	                "c"
	            ],
	            "inNodes": [
	                "a",
	                "a1",
	                "a2",
	                "a3",
	                "a4",
	                "a5",
	                "a6"
	            ],
	            "updateTime": -1,
	            "status": "READY",
	            "priority": 8,
	            "jobSource": "b.job",
	            "type": "command",
	            "endTime": -1
	        },
	        {
	            "attempt": 0,
	            "startTime": -1,
	            "id": "c",
	            "inNodes": [
	                "b",
	                "b1"
	            ],
	            "updateTime": -1,
	            "status": "READY",
	            "priority": 9,
	            "jobSource": "c.job",
	            "type": "command",
	            "endTime": -1
	        },
	        {
	            "attempt": 0,
	            "startTime": -1,
	            "id": "a",
	            "outNodes": [
	                "b",
	                "b1"
	            ],
	            "updateTime": -1,
	            "status": "READY",
	            "priority": 4,
	            "jobSource": "a.job",
	            "type": "command",
	            "endTime": -1
	        },
	        {
	            "attempt": 0,
	            "startTime": -1,
	            "id": "b1",
	            "outNodes": [
	                "c"
	            ],
	            "inNodes": [
	                "a",
	                "a1",
	                "a2",
	                "a3",
	                "a4",
	                "a5",
	                "a6"
	            ],
	            "updateTime": -1,
	            "status": "READY",
	            "priority": 7,
	            "jobSource": "b1.job",
	            "type": "command",
	            "endTime": -1
	        },
	        {
	            "attempt": 0,
	            "startTime": -1,
	            "id": "a1",
	            "outNodes": [
	                "b",
	                "b1"
	            ],
	            "updateTime": -1,
	            "status": "READY",
	            "priority": 1,
	            "jobSource": "a1.job",
	            "type": "command",
	            "endTime": -1
	        },
	        {
	            "attempt": 0,
	            "startTime": -1,
	            "id": "a2",
	            "outNodes": [
	                "b",
	                "b1"
	            ],
	            "updateTime": -1,
	            "status": "READY",
	            "priority": 2,
	            "jobSource": "a2.job",
	            "type": "command",
	            "endTime": -1
	        },
	        {
	            "attempt": 0,
	            "startTime": -1,
	            "id": "a3",
	            "outNodes": [
	                "b",
	                "b1"
	            ],
	            "updateTime": -1,
	            "status": "READY",
	            "priority": 3,
	            "jobSource": "a3.job",
	            "type": "command",
	            "endTime": -1
	        },
	        {
	            "attempt": 0,
	            "startTime": -1,
	            "id": "a4",
	            "outNodes": [
	                "b",
	                "b1"
	            ],
	            "updateTime": -1,
	            "status": "READY",
	            "priority": 4,
	            "jobSource": "a4.job",
	            "type": "command",
	            "endTime": -1
	        },
	        {
	            "attempt": 0,
	            "startTime": -1,
	            "id": "a5",
	            "outNodes": [
	                "b",
	                "b1"
	            ],
	            "updateTime": -1,
	            "status": "READY",
	            "priority": 5,
	            "jobSource": "a5.job",
	            "type": "command",
	            "endTime": -1
	        },
	        {
	            "attempt": 0,
	            "startTime": -1,
	            "id": "a6",
	            "outNodes": [
	                "b",
	                "b1"
	            ],
	            "updateTime": -1,
	            "status": "READY",
	            "priority": 6,
	            "jobSource": "a6.job",
	            "type": "command",
	            "endTime": -1
	        }
	    ],
	    "flowId": "c"
	}