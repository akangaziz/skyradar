/**
 * CLASS
 * 		PersistenceProvider
 * DESCRIPTION
 * 		Provides basic UI persistence functionality to the lient application.
 * USAGE
 * 		var persistenceProvider = new PersistenceProvider ();
 * 		persistenceProvider.registerAll();
 * @class
 * @public
 */
function PersistenceProvider () {
	
	/**
	 * Adds persistence support to the given element.
	 * @method
	 * @public
	 * @param element { HTML Element }
	 * 		The element to register. 
	 */
	this.registerElement = function (element) {
		var id = element.id;
		registry.push[id];
		var value = basicStorageProvider.getItem(id);
		if(value) {setValueToElement(element, value)};
	}
	
	/**
	 * Removes persistence support from the given element.
	 * @param element { HTML Element }
	 * @method
	 * @public
	 * @param element { HTML Element }
	 * 		The element to unregister.
	 */
	this.unregisterElement = function (element) {
		var arr = [];
		for(var i=0; i<registry.length; i++) {
			if(registry[i] === element) {continue};
			arr.push(registry[i]);
		}
		registry = arr;
	}
	
	/**
	 * Adds persistence support to all form elements within the client 
	 * document.
	 * @method
	 * @public
	 */
	this.registerAll = function () {
		var eligibleElements = getEligibleElements();
		for(var i=0; i<eligibleElements.length; i++) {
			var element = eligibleElements[i];
			registry.push (element.id);
		}
		if(registry.length > 0) {restoreAll()};
	}
	
	/**
	 * Removes persistence support from all form elements within the client 
	 * document.
	 * @method
	 * @public
	 */
	this.unregisterAll = function () {
		registry = [];
	}
	
	/**
	 * Saves all registered elements' values on demand.
	 * @method
	 * @public
	 */
	this.saveNow = function() {
		saveAll();
	}
	
	/**
	 * Restores all registered elements' values from cache on demand.
	 * @method
	 * @public
	 */
	this.restoreNow = function() {
		restoreAll();
	}
	
	/**
	 * Clears the internal cache. Useful for custom uninstall scenarios.
	 * @method
	 * @public
	 */
	this.clearPersistence = function() {
		for(var i=0; i<registry.length; i++) {
			var id = registry[i];
			basicStorageProvider.unsetItem(id);			
		}
	}

	/**
	 * The internal representaion of registered elements.
	 * @field
	 * @private
	 */
	var registry = [];
	
	/**
	 * The BasicStorageProvider instance used by class PersistenceProvider.
	 * @field
	 * @private
	 */
	var basicStorageProvider;

	/**
	 * An instance of the external public class EventManager, to be used when 
	 * available. There will be no error if missing.
	 * @field
	 * @private
	 */
	var eventManager;

	/**
	 * Custom initialization for class PersistenceProvider
	 * @method
	 * @private
	 */	
	function init() {
		basicStorageProvider = new BasicStorageProvider();
		
		if(typeof EventManager != 'undefined') {
			eventManager = EventManager.getDefault();
		}
		
		// @subscribe to the 'closing' event and save everything: 
		nativeWindow.addEventListener(
			window.air.Event.CLOSING,
			saveAll
		);
	}
	
	/**
	 * Returns a list with all eligible form elements. 
	 * Note:
	 * Namelly, this includes: 
	 * 	- text fields
	 * 	- check boxes
	 * 	- radio buttons
	 * 	- lists
	 * 	- text areas
	 * Important: an HTML Element of one of these types will only be eligible if
	 * it has a valid 'id' attribute.
	 * @method
	 * @private
	 * @return { Array }
	 * 		An array holding all form elements that are eligible for persistence
	 * 		support. See above note.
	 */
	function getEligibleElements() {
		var ret = [];
		var allInputs = document.getElementsByTagName('input');
		for(var i=0; i<allInputs.length; i++) {
			var input = allInputs[i];
			var type = input.getAttribute('type').toLowerCase(); 
			if(type == 'text' || type == 'checkbox' || type == 'radio') {
				ret.push(input);
			}
		}
		var allSelects = document.getElementsByTagName('select');
		for(var j=0; j<allSelects.length; j++) {ret.push(allSelects[j])};
		var allTextareas = document.getElementsByTagName('textarea');
		for(var k=0; k<allSelects.length; k++) {ret.push(allSelects[k])};
		return ret;
	}

	/**
	 * Tests whether a particular form element is eligible.
	 * @method
	 * @private
	 * @param element { HTML Element }
	 * 		The element to be checked for eligibility.
	 * @return { Boolean }
	 * 		True if this element is eligible; false otherwise. 
	 * @see getEligibleElements() for a list of eligible elements. 
	 */
	function isEligible (element) {
		if (element && element.nodeType && element.nodeType == 1) {
			var nodeName = element.nodeName.toLowerCase();
			switch (nodeName) {
				case 'textarea':
				case 'select':
					return true;
				case 'input':
					var type = element.getAttribute('type').toLowerCase();
					if (type == 'text' ||
						type == 'checkbox' || 
						type == 'radio') {return true};
			}
		}
		return false;
	}

	/**
	 * Retrieves the givent element's value that is to be persisted.
	 * @method
	 * @private
	 * @param element { HTML Element }
	 * 		The registered element whose value is to be retrieved.
	 * @return { String }
	 * 		The value of the given element, provided it is an eligible element;
	 * 		null otherwise.
	 */
	function getValueFromElement (element) {
		if (element && element.nodeType && element.nodeType == 1) {
			var nodeName = element.nodeName.toLowerCase();
			switch (nodeName) {
				case 'input':
					var type = element.getAttribute('type').toLowerCase();
					switch (type) {
						case 'text':
							return element.value;
						case 'radio':
							var checkValue = element.checked;
							if(checkValue) {return true};
						case 'checkbox':
							return element.checked;
					};
				case 'select':
				case 'textarea':
					return element.value;
			}
		}
		return null;
	}

	/**
	 * Restores a registered element to its previously saved value.
	 * @method
	 * @private
	 * @param element { HTML Element }
	 * 		The registered element whose value is to be restored.
	 * @param value { Object }
	 * 		The value to restore.
	 */
	function setValueToElement (element, value) {
		if (element && element.nodeType && element.nodeType == 1) {
			var nodeName = element.nodeName.toLowerCase();
			switch (nodeName) {
				case 'input':
					var type = element.getAttribute('type').toLowerCase();
					switch (type) {
						case 'text':
							element.setAttribute('value', value);
							break;
						case 'radio':
							if(value) {element.setAttribute('checked', true)}
							break;
						case 'checkbox':
							element.setAttribute('checked', true);
							break;
					};
				case 'select':
					break;
				case 'textarea':
					element.setAttribute('value', value);
					break;
			}
		}
	}
	
	/**
	 * Collects and saves the current value from all registered elements.
	 * @method
	 * @private
	 */	
	function saveAll() {
		for (var i=0; i<registry.length; i++) {
			var id = registry[i];
			var element = document.getElementById(id);
			var value = getValueFromElement(element);
			basicStorageProvider.setItem(id, value);
			if(eventManager) {
				var evt = eventManager.createEvent(
					PersistenceProvider.VALUE_SAVED_EVENT, {
						'id': id,
						'value': value		
					}
				);
				eventManager.fireEvent(evt);
			}
		}
	}
	
	/**
	 * Restores all the registered elements to their previously saved value.
	 * @method
	 * @private
	 */
	function restoreAll() {
		for (var i=0; i<registry.length; i++) {
			var id = registry[i];
			var element = document.getElementById(id);
			var value = basicStorageProvider.getItem(id);
			if(value) {setValueToElement(element, value)};
			if(eventManager) {
				var evt = eventManager.createEvent(
					PersistenceProvider.VALUE_RESTORED_EVENT, {
						'id': id,
						'value': value		
					}
				);
				eventManager.fireEvent(evt);
			}
		}
	}

	// @run custom initialization code for class PersistenceProvider.
	init();
}

/**
 * If EventManager class support is avalable, this will refer to a specific
 * event type to be dispatched when some value has been saved to cache.
 * @field
 * @public
 * @static
 */	
PersistenceProvider.VALUE_SAVED_EVENT = "valueSavedEvent";

/**
 * If EventManager class support is avalable, this will refer to a specific
 * event type to be dispatched when some value has been restored from 
 * cache.
 * @field
 * @public
 * @static
 */
PersistenceProvider.VALUE_RESTORED_EVENT = "valueRestoredEvent";