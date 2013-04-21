/**
 * CLASS
 * 		Main
 * DESCRIPTION
 * 		Main class of the BkMark application.
 * 
 * 		THE BKMARK APPLICATION places a shortcut on the desktop to a web
 * 		page at user's choice. That page will then display in a slightly
 * 		customized chrome, and, eventually, will automatically open at system
 * 		start.
 * USAGE
 * 		var main = Main.getDefault();
 * 		main.run();
 * @class
 * @public 
 */
Main = function() {
	throw(new Error(
			"You cannot instantiate the 'Main' class. " +
			"Instead, use 'Main.getDefault()' to retrieve the " +
			"class' unique instance."
	));
}
Main.getDefault = function() {
	var context = arguments.callee;
	if (context.instance) { return context.instance };
	function _main() {

		/**
		 * The shared PersistenceProvider instance.
		 * @field
		 * @private
		 */
		var persistenceProvider;

		/**
		 * The shared EventManager instance.
		 * @field
		 * @private
		 */
		var eventManager;

		/**
		 * The shared BasicWindowsManager instance.
		 * @field
		 * @private
		 */
		var basicWindowsManager;

		/**
		 * This will hold the url viewer's native window instance when 
		 * available.
		 */
		var urlViewerWindow;

		/**
		 * Internal representation of user values, as just entered in the text 
		 * fields or loaded from cache.
		 * Note:
		 * The window position and dimension values will transparently change as
		 * the user moves or resizes the url viewer window.
		 * @field
		 * @private 
		 */
		var currentConfig = {};

		/**
		 * Enhances the application UI.
		 * @method
		 * @private
		 */
		function enhanceUI() {
			enhanceButtons ();
		}
		
		/**
		 * Flag we raise when somebody calls 'appExit()' -- this prevents having
		 * it called a second time, and throw.
		 * @field
		 * @private
		 */
		var appExiting;

		/**
		 * Programmatically ends the application _and_ dispatches an 'exiting'
		 * event -- unlike NativeApplication.nativeApplication.exit().
		 * @method
		 * @private
		 */
		function appExit() {
			if(!appExiting) {
				appExiting = true;
				var app = air.NativeApplication
					.nativeApplication;
				var exitingEvent = new air.Event(
				 	air.Event.EXITING, true, true);
				app.dispatchEvent(exitingEvent);
				app.exit();
			}
		}

		/**
		 * Adds related functionality to the 'Done' button.
		 * @method
		 * @private
		 */
		function hookDoneButton() {
			var form = document.getElementById('appMainUIForm');
			var clickHandler = function(event){
				event.preventDefault();
				event.stopPropagation();
				persistenceProvider.saveNow();
				persistenceProvider.restoreNow();
			}
			form.addEventListener('submit', clickHandler, false);
		}

		/**
		 * Adds related functionality to the 'View source' link.
		 * @method
		 * @private
		 */
		function hookSourceButton(){
			var link = document.getElementById('viewSourceLink');
			var clickHandler = function() {
				var browser = air.SourceViewer.getDefault();
				browser.setup ({ exclude:['/src/SourceViewer'] });
				browser.viewSource();
			}
			link.addEventListener('click', clickHandler, false);
		}
		
		/**
		 * Adds related functionality to the 'about' link.
		 * @method
		 * @private
		 */
		function hookAboutButton(){
			var link = document.getElementById('showAboutLink');
			var clickHandler = function() {
				alert("" +
					"BkMark\n\nCopyright 2007-2008. Adobe Systems Incorporated.\n" +
					"All rights reserved" 
				);
			}
			link.addEventListener('click', clickHandler, false);
		}

		/**
		 * Makes application's controlls persistent, i.e., their value will
		 * persist over application shutdown.
		 * @method
		 * @private
		 */
		function hookPersistence() {
			persistenceProvider = new PersistenceProvider ();
			persistenceProvider.registerAll();
		}

		/**
		 * Adds a tray/dock icon to the aplication. The icon is brought from the
		 * /icon folders -- the nearest resolution is automatically picked up.
		 * @method
		 * @private 
		 */
        function addTrayOrDockIcon() {

        	// @resolve urls to all image files we need to load:
        	var root = air.File.applicationDirectory;
			var iconsFolder = root.resolvePath('src/icons');
			if (iconsFolder && iconsFolder.exists) {
				var iconFiles = iconsFolder.getDirectoryListing();
				var iconFilesOK = true;
				for(var i=0; i<iconFiles.length; i++) {
					var iconFile = iconFiles[i];
					if (!/\.gif$|\.png$|\.bmp$|\.jpg$|\jpeg$/
						.test(iconFile.url)) {
						iconFilesOK = false;
						break;
					}
				}

				// @collect bitmapData objects:
				if(iconFilesOK) {
					var bmpDataObjects = [];
					var completeHandler = function(event){
						var bitmap = event.target.loader.content;
						bmpDataObjects.push(bitmap.bitmapData);
						loadNext();
					}
					var ioErrorHandler = function(event){
					}
					var loadNext = function(){
						var iconFile = iconFiles.pop();
						if(iconFile) {
							var iconURL = iconFile.url;
							var request = new air.URLRequest(iconURL);
							loader.load(request);
						} else {
							finish();
						}
					}
					var loader = new air.Loader();
		            loader.contentLoaderInfo.addEventListener(
		            	air.Event.COMPLETE, completeHandler);
		            loader.contentLoaderInfo.addEventListener(
		            	air.IOErrorEvent.IO_ERROR,
		            	ioErrorHandler);
		            loadNext();
				}

				// @display the shell icon:
				var finish = function() {
					var win = air.NativeApplication
						.supportsSystemTrayIcon;
					var osx = air.NativeApplication
						.supportsDockIcon;
					if (win || osx) {
						var app = air.NativeApplication
							.nativeApplication;
						var icon = app.icon;
						icon.bitmaps = bmpDataObjects;
						addTrayOrDockIconMenu(icon);
					}
				}
			}
        }

        /**
         * Adds a tray/doc menu that will show up when the user clickes the
         * application's tray/doc icon.
         * @method
         * @private
         * @param iconObj { Object }
         * 		The tray or dock icon object that is to attach a menu.
         */
        function addTrayOrDockIconMenu (iconObj) {

        	// @define a generic menu item click handler:
        	var menuClickHandler = function(event){
        		var item = event.target;
        		var itemName = item.name;
        		var actionName = itemName+ '_action'; 
        		if(item.data[actionName]) {
        			var action = item.data[actionName];
        			if (action instanceof Function) {action()};
        		}
        	};

        	// @define the menu items:
        	var settingsItem = new air.NativeMenuItem();
        	settingsItem.name = "settings_item";
        	settingsItem.label = "Settings...";
        	settingsItem.data = {
        		'settings_item_action': function(){
        			switchToSettingsUI();
        		}
        	}
        	var separatorItem = new air.NativeMenuItem(null, true);
        	var exitItem = new air.NativeMenuItem();
        	exitItem.name = "exit_item";
        	exitItem.label = "Exit";
        	exitItem.data = {
        		'exit_item_action': function() { appExit(); }
        	};

        	// @hook items and finish setup:
        	var menu = new air.NativeMenu();
        	menu.addItem(settingsItem);
        	menu.addItem(separatorItem);
        	menu.addItem(exitItem);
        	menu.addEventListener (air.Event.SELECT,
        		menuClickHandler);
        	air.NativeApplication.nativeApplication
        		.icon.menu = menu;
        }

		/**
		 * Closes the url viewer page and displays the settings UI, so the user 
		 * can, for instance, change the url.
		 * @method
		 * @private 
		 */
		function switchToSettingsUI() {
			if (urlViewerWindow) {
				window.nativeWindow.activate();
				urlViewerWindow.close();
			}
		}

		/**
		 * Hides the settings window and uses current settings to possibly
		 * open the url viewer and load the given web page inside it.
		 * Note:
		 * Will fallback to the settings window upon failure (i.e., if url value
		 * is invalid).
		 * @method
		 * @public
		 */
		function switchToViewerUI() {
			if(validateUrl(currentConfig['url'])) {
				window.nativeWindow.visible = false;
				openUrl();
			} else {
				fallback();
			}
		}

		/**
		 * Saves current window boundaries to current configuration.
		 * @method
		 * @private
		 */
		function saveWindowBoundaries() {
			if(urlViewerWindow) {
				var boundaries = urlViewerWindow.bounds;
				var box = currentConfig['box'];
				if(box) {
					box['x'] = boundaries.x;
					box['y'] = boundaries.y;
					box['width'] = boundaries.width;
					box['height'] = boundaries.height;
				};
				updateUICoordinateFields();
			}
		}

		/**
		 * Opens the given url inside a (possibly customized) native window.
		 * @method
		 * @private
		 */
		function openUrl() {
			var url = currentConfig['url'];
			var box = currentConfig['box'];
			if(box) {basicWindowsManager.setWindowBoundaries(box)};
			
			// @save window position, etc. as user changes it:
			eventManager.addListener(
				BasicWindowsManager.WINDOW_X_CHANGED_EVENT, 
				saveWindowBoundaries
			);
			eventManager.addListener(
				BasicWindowsManager.WINDOW_Y_CHANGED_EVENT,
				saveWindowBoundaries
			);
			eventManager.addListener(
				BasicWindowsManager.WINDOW_HEIGHT_CHANGED_EVENT,
				saveWindowBoundaries
			);
			eventManager.addListener(
				BasicWindowsManager.WINDOW_WIDTH_CHANGED_EVENT,
				saveWindowBoundaries
			);
			
			// @close the application when secondary window is closed:
			var windowDestroyedHandler = function(evt){
				eventManager.removeListener(
					BasicWindowsManager.WINDOW_DESTROYED_EVENT,
					windowDestroyedHandler
				);
				urlViewerWindow = null;
				var mayCloseApp = true;
				var openedWindows = air.NativeApplication
					.nativeApplication.openedWindows;
				for(var i=0; i<openedWindows.length; i++) {
					var w = openedWindows[i];
					if(w.closed) {continue};
					if(w.active) {
						mayCloseApp = false;
						break;
					}
				}
				persistenceProvider.saveNow();
				if(mayCloseApp) {
					appExit()
				};
			}
			eventManager.addListener (
				BasicWindowsManager.WINDOW_DESTROYED_EVENT,
				windowDestroyedHandler
			);

			// @create an orphaned iframe to load web pages into
			var windowCreatedHandler = function(evt) {
				urlViewerWindow = evt.body.window.nativeWindow;
				var doc = evt.body.window.document;

				// @set new window's title:
				doc.title = 'BkMark - ' +
					(currentConfig['name']? currentConfig['name'] : url);
				
				// @build page content:
				var domProvider = new DOMProvider(doc);
				var layoutProvider = new LayoutProvider();
				
				// @build regular iframe:
				var iframeRecipient = domProvider.makeDiv();
				layoutProvider.setupBox(iframeRecipient);
				layoutProvider.setupStretched(iframeRecipient);
				var iframe = domProvider.makeElement('iframe', iframeRecipient,
					null, {
					'frameborder' : 0,
					'marginheight': 0,
					'marginwidth': 0,
					'width': '100%', 
					'height': '100%', 
					'src': url
				});
				layoutProvider.setupBox(iframe, {left:0, top:0});
				layoutProvider.setupStretched(iframe, {bottom:0});
				
				// @clear everything:
				domProvider = null;
				layoutProvider = null;
				eventManager.removeListener(BasicWindowsManager
					.WINDOW_CREATED_EVENT, windowCreatedHandler);
			}
			eventManager.addListener (
				BasicWindowsManager.WINDOW_CREATED_EVENT, windowCreatedHandler);
				
			// @actually create a new window:
			basicWindowsManager.openWindow();
		}

		/**
		 * Provided a valid url isn't available -- as per means of user having
		 * previously set it -- this will show the application window, thus 
		 * giving the user a chance to set one now.
		 * @method
		 * @private
		 */
		function fallback() {
			window.nativeWindow.activate();
			var urlInput = document.getElementById('urlInput');
			urlInput.select();
		}

		/**
		 * Provides basic validation for a given URL.
		 * @method
		 * @private
		 * @param urlValue { String }
		 * 		The string to validate as a URL.
		 * @return { Boolean }
		 * 		True if the given string validates as a url, false otherwise.
		 */
		function validateUrl (urlValue) {
			var urlPattern = /^http\:\/\/\w[\w\.\-\+\?\%\=\/]+$/; 
			return urlValue && urlPattern.test(urlValue);
		}
		
		/**
		 * Provides basic validation for a given name.
		 * @method
		 * @private
		 * @param name { String }
		 * 		The string to validate as a name.
		 * @return { Boolean }
		 * 		True if the given string validates as a name, false otherwise.
		 */
		function validateName (name) {
			return name && /\w/.test(name);
		}
		
		/**
		 * Provides basic validation for user iputs that are to be used as pixel
		 * values.
		 * @method
		 * @private
		 * @param value { String }
		 * 		The string to validate as an integer.
		 * @return { Boolean }
		 * 		True if the given string validates as an integer, false
		 * 		otherwise.
		 */
		function validatePixelNumber (value) {
			var n = parseFloat(value);
			if(isNaN(n)) {return false};
			return true;
		}
		
		/**
		 * Possibly returns an integer value from the given value, that is 
		 * suitable for representing a screen coordinate.
		 * Note:
		 * The returned value will be at least 0.
		 * @method
		 * @private
		 * @param value { String }
		 * 		The string to retrieve a number from.
		 * @return { Number }
		 * 		A number from the given string value, allways greater or equal 
		 * 		to 0. Will throw if provided value is not recognizable as a 
		 * 		string, use validatePixelNumber() first.
		 */
		function getPixelNumber (value) {
			var n = parseFloat(value);
			return Math.max(0, Math.floor(n));
		}
		
		/**
		 * Given the four box coordinates, this will assure that they define an 
		 * area inside the screen's visible area.
		 * @method
		 * @private
		 * @param oBox { Object }
		 * 		The original box, as an object holding x, y, width and height 
		 * 		numeric values.
		 * @return { Object }
		 * 		The new, computed box, holding values that define a corrected 
		 * 		rectangle area that is assured to fit within the visible screen.
		 */
		function castToScreenBox (oBox) {
			var x = oBox['x'];
			var y = oBox['y'];
			var width = oBox['width'];
			var height = oBox['height'];
			x = Math.max(0, x);
			y = Math.max(0, y);
			width = Math.min (screen.width, width);
			height = Math.min (screen.height, height);
			
			// @first pass -- try to accommodate by bringing into screen:
			var absRight = x + width;
			var absBottom = y + height;
			if(absRight > screen.width) {
				x = Math.max(0, x - (absRight - screen.width));
			}
			if(absBottom > screen.height) {
				y = Math.max(0, y - (absBottom - screen.height));
			}
			
			// @second pass -- try to accommodate by reducing dimensions:
			var absRight2 = x + width;
			var absBottom2 = y + height;
			if (absRight2 > screen.width) {
				width = (width - (absRight2 - screen.width));
			}
			if (absBottom2 > screen.height) {
				height = (height - absBottom2 - screen.height);
			}
			return {'x':x, 'y':y, 'width':width, 'height':height};
		}

		/**
		 * Will update the UI fields responsible with window position and
		 * dimensions to match the ones in the current configuration.
		 * @method
		 * @private
		 */
		function updateUICoordinateFields() {
			var widthInput = document.getElementById('widthInput');
			var heightInput = document.getElementById('heightInput');
			var xInput = document.getElementById('xInput');
			var yInput = document.getElementById('yInput');
			var box = currentConfig['box'];
			if(box) {
				widthInput.value = box['width'];
				heightInput.value = box['height'];
				xInput.value = box['x'];
				yInput.value = box['y'];
			}
		}

		/**
		 * Validates a given object as a box, i.e., it must have 'x', 'y', 
		 * 'width' and 'height' properties that hold numeric values.
		 * @method
		 * @private
		 * @param oBox { Object }
		 * 		The object to validate as a box.
		 * @return { Boolean }
		 * 		True if the object qualifies as a box; false otherwise.
		 */
		function validateBox (oBox) {
			return 	(typeof oBox['x'] == 'number') &&
					(typeof oBox['y'] == 'number') &&
					(typeof oBox['width'] == 'number') &&
					(typeof oBox['height'] == 'number');
		}

		/**
		 * Adds a custom css class to buttons to reflect their depressed state.
		 * @method
		 * @private
		 */
		function enhanceButtons () {
			var allInputs = document.getElementsByTagName('input');
			var buttonDown = function (event) {
				event.target.className = event.target.className+' down';
			}
			var buttonUp = function (event) {				
				event.target.className = event.target.className
					.replace(/\s*down/, '');
				event.target.className = event.target.className+ ' justClicked';
			}
			var buttonOut = function (event) {
				var el = event.target; 
				window.setTimeout(function() {
					while(el.className.indexOf('justClicked') != -1) {
						el.className =el.className.replace(/\s*justClicked/,'');
					}
				}, 10);
			}
			for(var i=0; i<allInputs.length; i++) {
				var input = allInputs[i];
				if (input.getAttribute('type') == 'button') {
					input.addEventListener('mousedown',	buttonDown,	false);
					input.addEventListener('mouseup',	buttonUp,	false);
					input.addEventListener('mouseout',	buttonOut,	false);
					input.addEventListener('mouseout',	buttonUp,	false);
				}
			}
		}

		/**
		 * The main class' entry point.
		 * @method
		 * @public
		 */
		this.run = function() {
			eventManager = EventManager.getDefault();
			basicWindowsManager = new BasicWindowsManager();
			enhanceUI();
			addTrayOrDockIcon();
			
			// @we need to find out whether a valid URL has ever been set:
			var shouldShowViewer = false;
			var windowBox = {};
			var fieldsCount = 0;
			var fieldsNo = 11;
			eventManager.addListener(PersistenceProvider.VALUE_RESTORED_EVENT,
				function(evt) {
					fieldsCount++;
					switch(evt.body.id) {
						case 'urlInput':
							var urlValue = evt.body.value;
							var haveValidUrl = validateUrl(urlValue);
							if(haveValidUrl) {
								currentConfig['url'] = urlValue;
								shouldShowViewer = true;
							}
							break;
						case 'titleInput':
							var nameValue = evt.body.value;
							if(validateName(nameValue)) {
								currentConfig['name'] = nameValue;
							}
							break;
						case 'widthInput':
							var widthValue = evt.body.value;
							if (validatePixelNumber(widthValue)) {
								windowBox['width'] = getPixelNumber(widthValue);
							}
							break;
						case 'heightInput':
							var heightValue = evt.body.value;
							if (validatePixelNumber(heightValue)) {
								windowBox['height'] = getPixelNumber(heightValue);
							}
							break;
						case 'xInput':
							var xValue = evt.body.value;
							if (validatePixelNumber(xValue)) {
								windowBox['x'] = getPixelNumber(xValue);
							}
							break;
						case 'yInput':
							var yValue = evt.body.value;
							if (validatePixelNumber(yValue)) {
								windowBox['y'] = getPixelNumber(yValue);
							}
							break;
//						case 'addressBarCheck':
//							break;
//						case 'chromelessCheck':
//							break;
//						case 'transparentCheck':
//							break;
//						case 'taskBarCheck':
//							break;
						case 'openAtLoginCheck':
							var app = air.NativeApplication
								.nativeApplication;
								try {
									app.startAtLogin = evt.body.value;
								} catch(e) {
									air.trace(e.message);
								}
							break;
					}
					if(fieldsCount == fieldsNo) {
						fieldsCount = 0;
						if(validateBox(windowBox)) {
							currentConfig['box'] = castToScreenBox(windowBox);
							updateUICoordinateFields();
						}
						if(shouldShowViewer) {switchToViewerUI()} 
						else {fallback()}
					}
				}
			);

			// @ask for cached user input:
			hookPersistence();
			hookDoneButton();
			hookSourceButton();
			hookAboutButton();
		}
	}
	context.instance = new _main();
	return context.instance;
}