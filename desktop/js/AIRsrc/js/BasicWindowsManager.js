/**
 * CLASS
 * 		BasicWindowsManager
 * DESCRIPTION
 * 		Provides basic window creation functionality.
 * @class
 * @public
 */
 function BasicWindowsManager () {
 	
 	/**
 	 * Sets the options to be used for a newly created window.
 	 * @method
 	 * @public
 	 * @param oConfig { Object }
 	 * 		Object literal to describe the new window's look/behavior. Expected 
 	 * 		format is:
 	 * 		{
 	 * 			systemChrome: 'none' | 'standard' | 'utility
 	 * 			transparent: true | false
 	 * 			type: 'lightweight' | 'normal' | 'utility'
 	 * 		}
 	 */
 	this.setWindowOptions = function(oConfig) {
 		options = oConfig;
 	}
	
	/**
	 * Sets the boundaries to be used for a newly created window.
	 * @method
	 * @public
	 * @param oRectangle { Object }
	 * 		Object literal to describe the new window's size and position.
	 * 		Expected format is:
	 * 		{
	 * 			x: <integer>
	 * 			y: <integer>
	 * 			width: <integer>
	 * 			height: <integer>
	 * 		}
	 */
	this.setWindowBoundaries = function(oRectangle) {
		boundaries = oRectangle;
	}
	
	/**
	 * Creates a new window using given options and settings. 
	 * @method
	 * @public
	 */
	this.openWindow = function () {
		var _htmlLoader = air.HTMLLoader.createRootWindow (
			true,			// @visible
			getOptions(), 	// @windowInitOptions
			false,			// @scrollBarsVisible
			getBoundaries()	// @bounds
		);
		var completeHandler = function() {
			_htmlLoader.removeEventListener('complete', completeHandler);
            if(eventManager) {
                var event = eventManager.createEvent (
                    BasicWindowsManager.WINDOW_CREATED_EVENT,
                    {'window': _htmlLoader.window}
                );
                eventManager.fireEvent(event);
            }
        }
		_htmlLoader.addEventListener('complete', completeHandler);
		_htmlLoader.window.nativeWindow.addEventListener('close', function(){
			var event = eventManager.createEvent(
				BasicWindowsManager.WINDOW_DESTROYED_EVENT);
			eventManager.fireEvent(event);
		});
		var nowChangingState = false;
		var stateChanging = function() {nowChangingState = true};
		var stateChanged = function() {nowChangingState = false};

		// @will possibly broadcast the new X,Y coordinates respectivelly:
		var windowMoved = function(event) {
			if(eventManager) {
				var win = event.target;
				if(!nowChangingState && win.displayState == air
					.NativeWindowDisplayState.NORMAL) {
					var oldXY = [event.beforeBounds.x, event.beforeBounds.y];
					var newXY = [event.afterBounds.x, event.afterBounds.y];
					if (oldXY[0] != newXY[0]) {
						var evt = eventManager.createEvent(
							BasicWindowsManager.WINDOW_X_CHANGED_EVENT, 
							{'value': newXY[0]});
						eventManager.fireEvent(evt);
					}
					if(oldXY[1] != newXY[1]) {
						var evt = eventManager.createEvent(
							BasicWindowsManager.WINDOW_Y_CHANGED_EVENT, 
							{'value': newXY[1]});
						eventManager.fireEvent(evt);
					}
				}
			}
		}
		
		// @will possibly broadcast new width/height dimensions respectivelly:
		var windowResized = function(event) {
			if(eventManager) {
				var win = event.target;
				if(!nowChangingState && win.displayState == air
					.NativeWindowDisplayState.NORMAL) {
					var oldDims = [event.beforeBounds.width,
						event.beforeBounds.height];
					var newDims = [event.afterBounds.width,
						event.afterBounds.height];
					if(oldDims[0] != newDims[0]) {
						var evt = eventManager.createEvent(
							BasicWindowsManager.WINDOW_WIDTH_CHANGED_EVENT, 
							{'value': newDims[0]}
						)
						eventManager.fireEvent(evt);
					}
					if(oldDims[1] != newDims[1]) {
						var evt = eventManager.createEvent(
							BasicWindowsManager.WINDOW_HEIGHT_CHANGED_EVENT, 
							{'value': newDims[1]}
						)
						eventManager.fireEvent(evt);
					}
				}
			}
		}

		// @observe user moving/resizing the window:
		_htmlLoader.window.nativeWindow.addEventListener(air
			.NativeWindowBoundsEvent.MOVE, windowMoved);
		_htmlLoader.window.nativeWindow.addEventListener(air
			.NativeWindowBoundsEvent.RESIZE, windowResized);
		_htmlLoader.window.nativeWindow.addEventListener(air
			.NativeWindowDisplayStateEvent.DISPLAY_STATE_CHANGING,
			stateChanging);
		_htmlLoader.window.nativeWindow.addEventListener(air
			.NativeWindowDisplayStateEvent.DISPLAY_STATE_CHANGE,
			stateChanged);
		_htmlLoader.loadString('&nbsp;');
	}

	/**
	 * An instance of the external public class EventManager, to be used when 
	 * available. There will be no error if missing.
	 * @field
	 * @private
	 */
	var eventManager;

	/**
	 * Holds the new window options, as set by the client.
	 * @field
	 * @private
	 */
	var options;
	
	/**
	 * Holds the new window boundaries, as set by the client.
	 * @field
	 * @private
	 */
	var boundaries;
	
	/**
	 * Custom initialization for class BasicWindowsManager
	 * @method
	 * @private
	 */	
	function init() {
		if(typeof EventManager != 'undefined') {
			eventManager = EventManager.getDefault();
		}
	}
	
	/**
	 * Returns the default display options for a newly created window.
	 * @method
	 * @private
	 * @return { NativeWindowInitOptions }
	 * 		An object specifying display options for a new window. 
	 */
	function getDefOptions() {
		return {
		 	systemChrome: 'standard',
		 	transparent: false,
		 	type: 'normal'
		}
	}

	/**
	 * Returns the default display boundaries for a newly created 
	 * window.
	 * @method
	 * @private
	 * @return { Rectangle }
	 * 		A rectangle defining the boundaries of this new window.
	 */			
	function getDefBoundaries() {
		return {
			x: Math.max(0, (screen.width-800)/2),
			y: Math.max(0, (screen.height-600)/2),
			width: Math.min(800, screen.width),
			height: Math.min(600, screen.height)	
		}
	}
	
	
	/**
	 * Wrapper that retrieves the client's options or the default ones if
	 * client options are missing.
	 * @method
	 * @private
	 * @return { Object }
	 * 		The options to be used for creating a new window.  
	 */
	function getOptions () {
		var initOptions = new air.NativeWindowInitOptions();
		var src = options? options: getDefOptions();
		for (key in src) {initOptions[key] = src[key]};
		return initOptions;
	}
	
	/**
	 * Wrapper that retrieves the client's boundaries or the default ones if 
	 * client boundaries are missing.
	 * @method
	 * @private
	 * @return { Object }
	 * 		The boundaries to be used for creating a new window.  
	 */
	function getBoundaries () {
		var rectangle = new air.Rectangle();
		var src = boundaries? boundaries: getDefBoundaries();
		for (key in src) {rectangle[key] = src[key]};
		return rectangle;
	}
	
	// @run custom initialization code for class BasicWindowsManager.	
	init();
}

/**
 * Should the Event Manager be available, this will represent an event to be
 * dispatched after a new window has successfully been created.
 * @field
 * @private
 */
BasicWindowsManager.WINDOW_CREATED_EVENT = "windowCreatedEvent";

/**
 * Should the Event Manager be available, this will represent an event to be
 * dispatched after an opened window -- created by means of this class --
 * has been closed.
 * @field
 * @private
 */	
BasicWindowsManager.WINDOW_DESTROYED_EVENT = "windowDestroyedEvent";

/**
 * Should the Event Manager be available, this will represent an event to be
 * dispatched when the x window coordinate has changed.
 * @field
 * @private
 */
BasicWindowsManager.WINDOW_X_CHANGED_EVENT = "windowXChangedEvent";

/**
 * Should the Event Manager be available, this will represent an event to be
 * dispatched when the y window coordinate has changed.
 * @field
 * @private
 */
BasicWindowsManager.WINDOW_Y_CHANGED_EVENT = "windowYChangedEvent";

/**
 * Should the Event Manager be available, this will represent an event to be
 * dispatched when the width of the window has changed.
 * @field
 * @private
 */
BasicWindowsManager.WINDOW_WIDTH_CHANGED_EVENT = "windowWidthChangedEvent";

/**
 * Should the Event Manager be available, this will represent an event to be
 * dispatched when the height of the window has changed.
 * @field
 * @private
 */
BasicWindowsManager.WINDOW_HEIGHT_CHANGED_EVENT = "windowHeightChangedEvent";
