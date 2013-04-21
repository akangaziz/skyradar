jQuery(document).ready(function() {	
	if(!isBrowser()){
		centerWindow();
		first_init = 1;
		//var root = new air.Event(air.Event.CLOSING, onAppClosing, true); 
		window.nativeWindow.addEventListener(air.Event.CLOSING, onAppClosing); 
		window.nativeWindow.addEventListener(air.Event.EXITING, onAppClosing); 
		
		//connectSocket();
	} 
});



function centerWindow(){
 
    //default bounds of nativeWindow
    var applicationBounds = window.nativeWindow.bounds;

    //determine which screen we're located on
    var screens = air.Screen.getScreensForRectangle(window.nativeWindow.bounds);

    var screenBounds = (screens.length > 0) ? screens[0].visibleBounds : air.Screen.mainScreen.visibleBounds;
    
    //get initial position
    x = (screenBounds.width - applicationBounds.width) / 2;
    y = (screenBounds.height - applicationBounds.height) / 2;
    
    //adjust for offset x or offset y (multi monitors)
    x = screenBounds.x + x;
    y = screenBounds.y + y;
    
    window.nativeWindow.x = x;
    window.nativeWindow.y = y;
}

function windowResize(width,height){
	window.nativeWindow.stage.stageWidth	= width;
	window.nativeWindow.stage.stageHeight	= height;
	centerWindow();
}

function applicationExit(){ 
    var exitingEvent = new air.Event(air.Event.EXITING, false, true); 
    air.NativeApplication.nativeApplication.dispatchEvent(exitingEvent); 
    if (!exitingEvent.isDefaultPrevented()) { 
        air.NativeApplication.nativeApplication.exit(); 
    } 
}

function onAppClosing(){
	//conn.close();
	applicationExit();
}
