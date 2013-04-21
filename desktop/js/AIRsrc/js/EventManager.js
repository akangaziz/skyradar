/**
* CLASS
* 	EventManager
* DESCRIPTION
* 	Private class that provides abstract event management functionality.
* SAMPLE USAGE
* 	N/A (internal use only)
* @class
* @public
* @singleton
*/		
function EventManager() {
	throw(new Error(
			"You cannot instantiate the 'EventManager' class. " +
			"Instead, use 'Main.getDefault()' to retrieve the " +
			"class' unique instance."
	));
}
EventManager.getDefault = function() {
	var context = arguments.callee;
	if (context.instance) { return context.instance };
	function _eventManager() {
		/**
		 * Holds all the registered event listeners.
		 * @field
		 * @private
		 */
		var listeners = {};
		
		/**
		 * Registers an event listener.
		 * @method
		 * @public
		 * @param type { String }
		 * 		The type of events this listener is interested in.
		 * @param callback { Function }
		 * 		The callback to activate when a listener of this type will 
		 * 		be notified.
		 */
		this.addListener = function(type, callback) {
			var list = listeners[type] || (listeners[type] = []);
			list.push (callback);
		}
	
		/**
		 * Unregisters an event listener.
		 * @method
		 * @public
		 * @param type { String }
		 * 		The type of the listener(s) to remove.
		 * @param callback { Function }
		 * 		The callback registered with the listener(s) to remove.
		 */
		this.removeListener = function(type, callback) {
			var list = listeners[type];
			for(var i=0; i<list.length; i++) {
				var cb = list[i];
				if(cb === callback) {
					list[i] = null;
					break;
				}
			}
			list.sort(function(a,b){return a === null? 1:0});
			while (list[Math.min(0, list.length-1)] === null) {
				list.length -= 1;	
			}
		}
		
		/**
		 * Unregisters all event listeners of a specific type.
		 * @method
		 * @public
		 * @param type { String }
		 * 		The type of the listeners to be removed.
		 */
		this.removeListenersFor = function(type) {
			listeners[type] = null;
			delete listeners[type];
		}
		
		/**
		 * Notifies all event listeners of a specific type.
		 * @method
		 * @public
		 * @param event { EventManager.Event }
		 * 		The event object being passed to the callback.
		 */
		this.fireEvent = function (event) {
			var type = event.type;
			if(!listeners[type]) {return};
			for (var i=0; i<listeners[type].length; i++) {
				var callback = listeners[type][i];
				callback(event);
			}
		}
		
		/**
		 * Returns an instance of the Event class to the caller.
		 * @method
		 * @public
		 * @param type { String }
		 * 		The type of this event.
		 * @param body { Object }
		 * 		An object literal that holds the information this event
		 * 		transports. Both notifier and callback must have agreed upon
		 * 		this object literal structure.
		 * @param id { String }
		 * 		An optional unique id for this event, should it need be 
		 * 		recognized at some later time.
		 * @return { EventManager.Event }
		 * 		An event object having the specified type, body and id.
		 */
		this.createEvent = function(type, body, id) {
			return new Event(type, body, id);
		}
	
		
		
		
		/**
		 * CLASS
		 * 		Event
		 * DESCRIPTION
		 * 		Private class that provides a vehicle for transporting 
		 * 		information from the notifier to the callback.
		 * SAMPLE USAGE
		 * 		N/A (internal use only)
		 * @class
		 * @private
		 * @param type { String }
		 * 		The type of this event.
		 * @param body { Object }
		 * 		An object literal that holds the information this event
		 * 		transports. Both notifier and must have agreed upon
		 * 		this object literal structure.
		 * @param id { String }
		 * 		An optional unique id for this event, should it need be 
		 * 		recognized at some later time.
		 */
		function Event(type, body, id) {
			this.type = type;
			this.body = body? body: {};
			this.id = id? id : 'anonymous';
			this.toString = function() {
				var ret = '['+this.id+']: '+this.type+' event; ';
				for(var prop in this.body) {
					ret += '\n'+prop+': '+(
						this.body[prop] instanceof Function? 'function':
						this.body[prop]? this.body[prop].toString():
						this.body[prop] === null? 'null value':
						'undefined value');
				}
				return ret;
			}
		}
	}
	context.instance = new _eventManager();
	return context.instance;
}