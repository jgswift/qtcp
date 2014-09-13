<html>
    <head>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js" type="text/javascript"></script>
        <script src="../Client.js" type="text/javascript"></script>
        <script src="config.js" type="text/javascript"></script>
        <link rel="stylesheet" href="master.css"/>
        
<script type="text/javascript">
$(document).ready(function() {
    // create client
    qtcp.network.client = new qtcp.client(
        "body",
        new qtcp.stream(
            new qtcp.resource(LightStream.host,LightStream.port)
        )
    );
    
    // attach packet processor for event packet
    qtcp.network.client.attach("event",function(data) {
        $("#response").html(data[0]);
    });
    
    // connect to server
    qtcp.network.client.connect();
    
    // send event packet with some dummy data
    $('input').on('click',function() {
        qtcp.network.client.send(new qtcp.network.packet("event"),{var1:"test"});
    });
});

</script>

    </head>
    <body>
        <div>
            <input type="button" value="Click me!"/> <span id="response"></span>
        </div>
    </body>
</html>