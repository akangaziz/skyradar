/*init var */
jQuery.ajaxSetup({
    timeout: 20000,
    error : function(jqXHR, textStatus, errorThrown) {
        if (jqXHR.status == 404) {
            showError("Oops, failed connect to server, please try again..");
        } else {
            showError("Oops..  timeout..\n please try again. ");
        }
    }
});

function showloading(){
	jQuery('#loading').html("<img src='images/ajax-loader.gif'>");
}

jQuery(document).ajaxStart(function() {
  //var ajaxname	= processname!='' ? processname : 'Loading';
  blockUI( 'Loading..');
});

jQuery(document).ajaxStop(function() {
  setTimeout(jQuery.unblockUI, 0);
});

function isJSON(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}

function blockUI(msg){
	jQuery.blockUI({ css: { 
        border: 'none', 
        padding: '15px', 
        backgroundColor: 'rgb(19, 126, 153)', 
        '-webkit-border-radius': '10px', 
        '-moz-border-radius': '10px', 
        opacity: .5, 
        color: '#000000' 
    },
    message: "<h4><img src='images/ajax-loader.gif'> " + msg + " </h4>",
    }); 
}

function showErrorToast(msg) {
	
	//jQuery().toastmessage('showErrorToast', msg);
   jQuery().toastmessage('showToast', {
        text     : msg,
        sticky   : false,
        position : 'middle-center',
        type     : 'error',
        closeText: '',
        stayTime:  900, 
        close    : function () {
            //console.log("toast is closed ...");
        }
    });
}

function showError(msg){
	alert(msg);
	/*
	bootbox.alert(msg, function() {
    });*/
}
    
function jSerialize(form){
	return jQuery("#" + form).serialize() + '&apps=desktop';
}

function logger(msg){
	if(appsmode=='devel'){
		if(isBrowser()){
			//console.log(msg);
		}else{
			air.Introspector.Console.log(msg);
		}
	}
}

function airLog(msg){
	air.Introspector.Console.log(msg);
}

function changeUrl(url){
	event.preventDefault();
    linkLocation = url;
    blockUI('Loading..');
    jQuery("body").fadeOut(500, redirectPage);      
}

function redirectPage() {
	setTimeout(jQuery.unblockUI, 0);
    window.location = linkLocation;
}

function setDataApi(str){
	x	= explode('#',str);
	str =  x[0] + "&apps=" + fg_source;
	if(isBrowser()) str = str + "&jsoncallback=?";
	//logger(str);
	return str;
}

function buildMenu(){
	var mastermenu	=  {
						//id => class
						1 : 'indonesia',
						2 : 'singapore',
						3 : 'usa',
						4 : 'china',
						5 : 'malaysia',
						6 : 'german',
						7 : 'other'
						}
					;
	
	for(z=1;z<=7;z++){
		jQuery("#"+mastermenu[z]).html('');
		jQuery.each(airport[z], function(key,val){
			jQuery("#"+mastermenu[z]).append("<li><a href='#' id='airportmenu' airportid='"+val['fs']+"' urutan='"+z+"' "+(z==7 ? "url='"+val['url']+"'" : '')+">"+val['name']+"</a></li>");		
		});		
	}
	
}

function isBrowser(){
	var userAgent = navigator.userAgent.toString().toLowerCase();
	if(substr_count(userAgent,'adobeair')<1){
		return true;
	}else{
		return false;
	}
}

function checkBrowser(){
	if(isBrowser() && appsmode=='production'){		
		alert("Illegal activity.. this apps will be closed in seconds..");
		jQuery("body").css("display", "none");
		window.close();
		return false;
	}else{
		//do nothing for adobe air
	}
}

function showFormPradefined(){

}

function showFormLink(){
	
}

jQuery(window).load(function() { // makes sure the whole site is loaded
	setTimeout(jQuery.unblockUI, 0);
})

function checkOS(){
	isMac = navigator.platform.toUpperCase().indexOf('MAC')!==-1;
	isWindows = navigator.platform.toUpperCase().indexOf('WIN')!==-1;
	isLinux = navigator.platform.toUpperCase().indexOf('LINUX')!==-1;
}

function openFancyWindow(){
	jQuery('#linkbrowserModal').html('<a id="kliklinkbrowserModal" class="fancybox" href="#fancycontainer">x</a>').hide();
	jQuery('#kliklinkbrowserModal').click();
}

function openFancyWindowBrowser(){
	jQuery('#linkbrowserModal').html('<a id="kliklinkbrowserModal" class="various" data-fancybox-type="iframe" href="'+target+'">Iframe</a>').hide();
	jQuery('#kliklinkbrowserModal').click();
}

jQuery(document).ready(function() {
	//jQuery("body").css("display", "none");
    jQuery("body").fadeIn(500);   
    checkBrowser();
    checkOS();
});
	    
jQuery.extend({
  getUrlVars: function(){
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
      hash = hashes[i].split('=');
      vars.push(hash[0]);
      vars[hash[0]] = hash[1];
    }
    return vars;
  },
  getUrlVar: function(name){
    return jQuery.getUrlVars()[name];
  }
});

