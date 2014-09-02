function Subject(){
  this.observerList = [];
}
 
Subject.prototype.add = function( obj ){
  return this.observerList.push( obj );
};
 
Subject.prototype.count = function(){
  return this.observerList.length;
};
 
Subject.prototype.get = function( index ){
  if( index > -1 && index < this.observerList.length ){
    return this.observerList[ index ];
  }
};
 
Subject.prototype.indexOf = function( obj, startIndex ){
  var i = startIndex;
 
  while( i < this.observerList.length ){
    if( this.observerList[i] === obj ){
      return i;
    }
    i++;
  }
 
  return -1;
};
 
Subject.prototype.removeAt = function( index ){
  this.observerList.splice( index, 1 );
};

Subject.prototype.notify = function( context ){
  var observerCount = this.observerList.length;
  for(var i=0; i < observerCount; i++){
    this.get(i).call(context.sender,context.data);
  }
};

function Event(sender,data) {
    this.sender = sender;
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
    client: function(viewer, stream) {
        this.viewer = $(viewer);
        this.stream = stream;
        this.log = new Array;
        this.streams = new Array;
        this.streaming = new Array;
    },
    stream: function(resource) {
        if(resource instanceof qtcp.resource) {
            this.resource = resource;
            this.protocol = new qtcp.network.protocol();
            this.socket = new WebSocket('ws://'+this.resource.ip+':'+this.resource.port);
            this.socket.onopen = this.open;
            this.socket.onmessage = this.message;
            this.socket.onclose = this.close;
        }
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

qtcp.stream.prototype.open = function(e) {
    
};

qtcp.stream.prototype.message = function(e) {
    var json_data = JSON.parse(e.data);
        
    var id = json_data.id;
    var data = json_data.data;

    var packet;
    
    var protocol = Object.getOwnPropertyNames(qtcp.network.protocol);
    
    var k = protocol.indexOf(id);
    if(typeof k !== "undefined") {
        packet = qtcp.network.protocol.spawn(protocol[k]);
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

qtcp.client.prototype.send = function(packet,data) {
    this.stream.send(packet,data);
};

qtcp.network.packet = function(id) {
    this.id = id;
    this.receiveObservers = new Subject;
    this.sendObservers = new Subject;
    this.receive = function(data) { 
        var e = new Event(this,data);
        this.receiveObservers.notify(e);
    };
    
    this.send = function(data) { 
        var e = new Event(this,data);
        this.sendObservers.notify(e);
        
        if(!e.canceled) {
            qtcp.network.client.stream.socket.send(JSON.stringify({id:this.id,data:data}));
        }
    };
    
    this.on = function(evt,fn) {
        if(evt === 'receive') {
            this.receiveObservers.add(fn);
        } else if(evt === 'send') {
            this.sendObservers.add(fn);
        }
    };
    
    this.unbind = function(evt) {
        if(evt === 'receive') {
            this.receiveObservers = new Subject;
        } else if(evt === 'send') {
            this.sendObservers = new Subject;
        }
    };
};