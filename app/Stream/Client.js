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
}

//var conn = new WebSocket('ws://localhost:8080');
//conn.onopen = function(e) {
//    console.log("Connection established!");
//};
//
//conn.onmessage = function(e) {
//    console.log(e.data);
//};
//
//conn.send('Hello World!');



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
    this.receive = function(data) { };
    this.send = function(data) { 
        qtcp.network.client.stream.socket.send(JSON.stringify({id:this.id,data:data}));
    };
};