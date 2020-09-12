(function(){
	var window = this;
	var document = this.document;
	var FyD = WebSocket;
	var Connected = false;
	var OneTime = true;
	
	var Sockets = {	
		"Connected": false, 
		
		"Start": function(){
			FyD = new WebSocket('ws://localhost:2107/');
			Sockets.BindEvents();
		},
		
		"BindEvents": function(){
			FyD.onopen = function(){
				OneTime = false;
				Connected = true;
				Sockets.Connected = true;
				Glybe.Storage.ServerConnected = true;
			}
			
			FyD.onmessage = function(ev){
				Glybe.Server.HandleData(ev.data, ev);
			}
			
			FyD.onerror = function(ev){
				alert(ev.data);
			}
			
			FyD.onclose = function(event){
				Glybe.Storage.ServerConnected = false;
			}
		},
		
		"Send": function(packet){
			if(FyD.readyState == 1) {
				var finalPacket = packet;
				FyD.send(finalPacket);
			}
		}
	}
	
	window["Sockets"] = Sockets;
})();