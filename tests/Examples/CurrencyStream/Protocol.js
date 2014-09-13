qtcp.network.protocol.ask = function() {
    qtcp.network.packet.call(this,'ask');
};

qtcp.network.protocol.register = function() {
    qtcp.network.packet.call(this,'register');
    this.attach('receive',function(data) {
        var streams = qtcp.network.client.viewer;
        
        var s = streams.find("#"+data[0]);
        if(s.size() === 0) {
            qtcp.network.client.streaming.push(data[1]);
            var div = $("<div>");
            var val_container = $("<div>");
            val_container.addClass("stream-value");
            
            div.attr("id",data[0]);
            div.addClass("stream");
            div.append(data[1]);
            div.append(val_container);
            streams.append(div);
            
            var chartHeader = new Array;
            chartHeader.push("Time");
            for(var i in qtcp.network.client.streaming) {
                chartHeader.push(qtcp.network.client.streaming[i]);
            }

            window.prices = "";
            window.graph.updateOptions({
                'labels': chartHeader
            });
        }
    });
};

qtcp.network.protocol.tell = function() {
    qtcp.network.packet.call(this,'tell');
    
    this.attach('receive',function(data) {
        var streams = qtcp.network.client.viewer;
        
        var s = streams.find("#"+data[0]).find(".stream-value");
        
        s.html(data[2]);
        
        qtcp.network.client.log[data[1]] = data[2];
    });
};

qtcp.network.protocol.translate = function() {
    qtcp.network.packet.call(this,'translate');
};

qtcp.network.protocol.unregister = function() {
    qtcp.network.packet.call(this,'unregister');
    this.attach('receive',function(data) {
        var streams = qtcp.network.client.viewer;
        var name = data[1];
        
        var k = qtcp.network.client.streaming.indexOf(name);
        
        qtcp.network.client.streaming.splice(k,1);
        
        var s = streams.find("div#"+data[0]);
        if(s.size() > 0) {
            s.remove();
        }
        
        var chartHeader = new Array;
            chartHeader.push("Time");
            
        var streamCount = Object.keys(qtcp.network.client.streaming).length;
        var prices = [];
        if(streamCount > 0) {
            var z = [];
            z.push(window.tick);
            for(var i in qtcp.network.client.streaming) {
                chartHeader.push(qtcp.network.client.streaming[i]);
                z.push(null);
            }
            
            prices.push(z);
        } else {
            prices.push([1,null]);
            chartHeader.push("N/A");
        }
        
        //prices = prices + "\n";

        window.prices = prices.join(",") + "\n";

        window.graph.updateOptions({
            'labels': chartHeader,
            'file': window.prices
        });
    });
};

qtcp.network.protocol.liststreams = function() {
    qtcp.network.packet.call(this,'liststreams');
    
    this.attach('receive',function(data) {
        var list = qtcp.network.client.viewer.find(".list");
        list.html("");
        
        qtcp.network.client.header = "Time";
        for(var k in data.streams) {
            var stream_data = data.streams[k];
            qtcp.network.client.streams[stream_data["name"]] = stream_data;
            
            var check = $('<input>').attr('type','checkbox');
            check.val(stream_data["id"]);
            
            var li = $('<li>');
            
            check.on("change",function() {
                var val = $(this).val();
                if($(this).is(":checked")) {
                    qtcp.network.client.send(new qtcp.network.protocol.register(),[val]);
                } else {
                    qtcp.network.client.send(new qtcp.network.protocol.unregister(),[val]);
                }
            });
            
            li.append(check);
            
            var span = $('<span>');
            span.append(stream_data["name"]);
            li.append(span);
            
            span.on("click",function() {
                var c = $(this).parent().find("input");
                c.click();
            });
            list.append(li);
        }
    });
};

qtcp.network.initialize();
