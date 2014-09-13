<html>
    <head>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js" type="text/javascript"></script>
        <script src="../../../vendor/danvk/dygraphs/dygraph-dev.js" type="text/javascript"></script>
        <script src="../Client.js" type="text/javascript"></script>
        <script src="Protocol.js" type="text/javascript"></script>
        <script src="config.js" type="text/javascript"></script>
        <link rel="stylesheet" href="master.css"/>
        
<script type="text/javascript">
$(document).ready(function() {
    
    window.graph = new Dygraph(document.getElementById("chart"), [],
    {
        drawPoints: true,
        valueRange: [0, 1000],
        labels: ["Time"],
        legend: 'always',
        strokeWidth: 3
    });
    
    window.prices = "";
    
    qtcp.network.client = new qtcp.client(
        "#qtcp-container",
        new qtcp.stream(
            new qtcp.resource(CurrencyStream.host,CurrencyStream.port)
        )
    );
    
    qtcp.network.client.connect();
    
    window.tick = 1;
    
    setInterval(function() {
            var amt = Object.keys(qtcp.network.client.log).length;
            var samt = $("input:checked").size();
            if(amt > 0 && amt >= samt) {
                var linecount = window.prices.split(/\r\n|\r|\n/).length;
                if(linecount > 20) {
                    var lines = window.prices.split("\n");
                    
                    lines.splice(0,1);
                    window.prices = lines.join("\n");
                }
                
                window.prices = window.prices + window.tick;
            
                for(var i in qtcp.network.client.streaming) {
                    var name = qtcp.network.client.streaming[i];
                    if(name in qtcp.network.client.log) {
                        window.prices = window.prices + "," + qtcp.network.client.log[name];
                    } else {
                        window.prices = window.prices + ",0";
                    }
                }

                window.prices = window.prices + "\n";
                
                console.log(window.prices);

                window.graph.updateOptions({
                    'file': window.prices
                });
                
                qtcp.network.client.log = new Array;
                window.tick++;
            }
    },100);
});

</script>

    </head>
    <body>
<div id="qtcp-container">
    <ul class="list">
        
    </ul>
    
    <div class="data">
        <div id="chart" style="height: 300px;"></div>
    </div>
</div>