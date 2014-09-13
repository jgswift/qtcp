Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};

function Subject(){
  this.observerList = {};
}
 
Subject.prototype.attach = function(name, obj){
    if(!(name in this.observerList)) {
        this.observerList[name] = new Array;
    }
    
    this.observerList[name].push(obj);
};
 
Subject.prototype.count = function(){
    return Object.size(this.observerList);
};
 
Subject.prototype.getObservers = function(name){
    if(name in this.observerList) {
        return this.observerList[name];
    }
    
    return [];
};
  
Subject.prototype.detach = function(name){
    delete this.observerList[name];
};

Subject.prototype.notify = function(name, context){
    var observers = this.getObservers(name);
    
    var observerCount = observers.length;
    for(var i=0; i < observerCount; i++){
        observers[i].call(context.sender,context.data);
    }
};

function Event(data) {
    this.canceled = false;
    this.data = data;
}

Event.prototype.cancel = function() {
    this.canceled = true;
};

Function.prototype.inheritsFrom = function(parentClassOrObject){ 
    if(parentClassOrObject.constructor instanceof Function) { 
        //Normal Inheritance 
        this.prototype = new parentClassOrObject;
        this.prototype.constructor = this;
        this.prototype.parent = parentClassOrObject.prototype;
    } else { 
        //Pure Virtual Inheritance 
        this.prototype = parentClassOrObject;
        this.prototype.constructor = this;
        this.prototype.parent = parentClassOrObject;
    }
    
    return this;
};

Function.prototype.spawn = function(functionName /*, args */) {
    var args = [].slice.call(arguments).splice(2);
    var namespaces = functionName.split(".");
    var func = namespaces.pop();
    var ns = this;
    for(var i = 0; i < namespaces.length; i++) {
      ns = this[namespaces[i]];
    }
    return new ns[func];
};

var qtcp = {
    resource: function(ip,port) {
        this.ip = ip;
        if(typeof port === "undefined") {
            port = 8080;
        }
        
        this.port = port;
    },
    client: function(viewer,stream) {
        this.viewer = $(viewer);
        this.stream = stream;
        this.log = new Array;
        this.streams = new Array;
        this.streaming = new Array;
        this.subjects = new Subject;
    },
    stream: function(resource) {
        if(resource instanceof qtcp.resource) {
            this.resource = resource;
        }
        
        this.protocol = new qtcp.network.protocol();
    },
    network: {
        protocol: function() {},
        packet: function() {},
        initialize: function() {
            var protocol = Object.getOwnPropertyNames(qtcp.network.protocol);
            for(p in protocol) {
                if(typeof p === Function) {
                    p.inheritsFrom(qtcp.network.packet);
                }
            }
        }
    }
};

qtcp.stream.prototype.open = function() {
    
};

qtcp.stream.prototype.message = function(e) {
    var json_data = JSON.parse(e.data);
        
    var id = json_data.id;
    var data = json_data.data;

    var packet;
    
    var protocol = Object.getOwnPropertyNames(qtcp.network.protocol);
    
    var k = protocol.indexOf(id);
    if(k !== -1) {
        packet = qtcp.network.protocol.spawn(protocol[k]);
    } else {
        packet = new qtcp.network.packet(id);
    }
    
    packet.receive(data);
};

qtcp.stream.prototype.close = function(e) {
    
};

qtcp.stream.prototype.send = function(packet,data) {
    var id;
    if(typeof packet.id !== "undefined") {
        if(packet.send instanceof Function) {
            packet.send(data);
        } else {
            id = packet.id;
        }
    } else {
        id = packet;
    }
    
    if(typeof id !== "undefined") {
        this.socket.send(JSON.stringify({id:id,data:data}));
    }
};

qtcp.client.prototype.connect = function() {
    this.stream.socket = new WebSocket('ws://'+this.stream.resource.ip+':'+this.stream.resource.port);
    this.stream.socket.onopen = this.stream.open;
    this.stream.socket.onmessage = this.stream.message;
    this.stream.socket.onclose = this.stream.close;
};

qtcp.client.prototype.send = function(packet,data) {
    this.stream.send(packet,data);
};

qtcp.client.prototype.attach = function(event, fn) {
    this.subjects.attach(event,fn);
};

qtcp.client.prototype.detach = function(event) {
    this.subjects.detach(event);
};

qtcp.network.packet = function(id) {
    this.id = id;
    this.subjects = new Subject;
    this.receive = function(data) { 
        var e = new Event(data);
        this.subjects.notify('receive', e);
        qtcp.network.client.subjects.notify(id, e)
    };
    
    this.send = function(data) { 
        var e = new Event(data);
        this.subjects.notify('send', e);
        
        if(!e.canceled) {
            qtcp.network.client.stream.socket.send(JSON.stringify({id:this.id,data:data}));
        }
    };
    
    this.attach = function(evt,fn) {
        this.subjects.attach(evt,fn);
    };
    
    this.detach = function(evt) {
        this.subjects.detach(evt);
    };
};