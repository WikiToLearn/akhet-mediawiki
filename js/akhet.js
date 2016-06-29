$(document).ready(function() {

    if ($("#akhetwait").length) {
      AkhetWaitPoll();
    }

    function AkhetWaitPoll() {
      $.get( mw.util.wikiScript(), {
				action: 'ajax',
				rs: 'SpecialAkhet::pollingInstance',
				rsargs: [$("#akhetwait").data('akhethost'),$("#akhetwait").data('token')]
			}, function ( data ) {
        if(data.status==1){
          if(location.protocol == 'https:') {
            port =  data.host_ssl_port;
            encrypt  = 1;
          } else {
            port =  data.host_port;
            encrypt  = 0;
          }
          location.href =  mw.util.getUrl("extensions/Akhet/noVNC/vnc.html") + "?resize=scale&autoconnect=1" +
             "&host=" + data.host_name +
             "&port=" + port +
             "&password=" + data.instance_password +
             "&path=" + data.instance_path +
             "&encrypt=" + encrypt;
        } else {
          setTimeout(AkhetWaitPoll,1000);
        }
				console.log(  data );
			} , 'json').fail(function() {
        console.log("Error")
      });
    }
});
