/**
* CLASS
* 		DOMProvider
* DESCRIPTION
* 		Private class that provides DOM element creation tools and 
* 		related functionality for the application.
* @class
* @private
* @param oDocument { Object }
*		The document object to provide DOM services for.
*/
function DOMProvider (oDocument) {
	/**
	* The client document object we are providing DOM services for.
	* @field
	* @private
	*/
	var clientDoc = oDocument;

	/**
	* Generic functionality for creating DOM nodes.
	* @method
	* @public
	* @param elName { String }
	* 		The name of the node to create.
	* @param elParent { Object }
	* 		The parent of the node to create (optional, defaults to 
	* 		'clientDoc'). Can be one of the following:
	* 		- a Document node;
	* 		- the global 'window' object;
	* 		- an Element node.
	* 		For the first two cases, the new node will be appended to 
	* 		the Body element (which, in turn, will be created if it 
	* 		doesn't exist.
	* @param cssClass { String }
	* 		The name of a CSS class to add to this node (optional, 
	* 		defaults to empty string - i.e., no class attribute).
	* @param attributes { Object }
	* 		A hash defining a number of arbitrary attributes.
	* 		Note:
	* 		DOM event listeners will not fire if defined this way. Use
	* 		'addEventListener()' instead.
	* @return { HTML Object }
	* 		The newly created HTML Object.
	*/
	this.makeElement = function(elName, elParent, cssClass, attributes){
		// @private function; gracefully returns the 'html' HTML node. 
		var getHtmlNode = function(oDoc) {
			if(arguments.callee.node) { return arguments.callee.node };
			var node = oDoc.getElementsByTagName('html')[0];
			if(!node) {
				node = oDoc.appendChild(oDoc.createElement('html'));
			}
			arguments.callee.node = node;
			return node;
		}
		// @private function; gracefully returns the 'head' HTML node.
		var getHeadNode = function(oDoc) {
			if(arguments.callee.node) { return arguments.callee.node };
			var node = oDoc.getElementsByTagName('head')[0];
			if(!node) {
				var htmlNode = getHtmlNode(oDoc);
				node = htmlNode.insertBefore(oDoc.createElement('head'),
					htmlNode.firstNode);
			}
			arguments.callee.node = node;
			return node;
		}
		// @private function; gracefully returns the 'body' HTML node.
		var getBodyNode = function(oDoc) {
			if(arguments.callee.node) { return arguments.callee.node };
			var node = oDoc.getElementsByTagName('body')[0];
			if(!node) {
				var htmlNode = getHtmlNode(oDoc);
				var headNode = getHeadNode(oDoc);
				node = htmlNode.insertBefore(oDoc.createElement('body'),
					headNode.nextSibling);
			}
			arguments.callee.node = node;
			return node;
		}
		var parentType = 
			(elParent)?
				(elParent.nativeWindow)? 
					'WINDOW_OBJECT' :
				(elParent.nodeType && elParent.nodeType == 9)? 
					'DOCUMENT' :
				(elParent.nodeType && elParent.nodeType == 1)? 
					'ELEMENT' :
				null :
			null;
		var _parent;
		switch (parentType) {
			case 'WINDOW_OBJECT':
				var oDoc = elParent.document;
				_parent = getBodyNode(oDoc);
				break;
			case 'DOCUMENT':
				var oDoc = elParent;
				_parent = getBodyNode(oDoc);
				break;
			case 'ELEMENT':
				_parent = elParent;
				break;
			default:
				var oDoc = clientDoc;
				_parent = getBodyNode(oDoc);
		}
		var el = _parent.ownerDocument.createElement (elName);
		if (cssClass) { 
			el.className = cssClass;
		};
		if (attributes) {
			for (atrName in attributes) {
				el.setAttribute (atrName, attributes[atrName]);
			}
		}
		el = _parent.appendChild(el);
		return el;
	}
	
	/**
	 * Convenience method to create an empty div element.
	 * @method
	 * @private
	 * @see makeElement()
	 * @param className { String }
	 * 		The name of the css class to apply to the newly created div.
	 * 		Optional, defaults to empty string (i.e., no class 
	 * 		attribute).
	 * @param _parent { Object }
	 * 		The parent to create the new div in. Optional, defaults 
	 * 		in effect to the body element.
	 * @return { HTML Object }
	 * 		The newly created div element.
	 */
	this.makeDiv = function (_parent, className) {
		return this.makeElement('div', _parent, className);
	}
	
	/**
	 * Creates a styled text node.
	 * @method
	 * @private
	 * @see makeElement()
	 * @param value { String }
	 * 		The content of the text node to create. 
	 * 		Note:
	 * 		HTML markup will not be expanded.
	 * @param _parent { Object }
	 * 		The parent to create the new text node in. Optional, 
	 * 		defaults to the body element.
	 * @param className { String }
	 * 		The css class name to apply to the newly created text node.
	 * 		Note:
	 * 		The class name is rather applied to a 'span' wrapper that 
	 * 		holds the text node. The span wrapper is added regardless of
	 * 		the fact that the 'className' attribute is present or not.
	 * @return { HTML element }
	 * 		A span element wrapping the newly created text node.
	 */
	this.makeText = function(value, _parent, className) {
		var wrapper = this.makeElement('span', _parent, className);
		var text = wrapper.ownerDocument.createTextNode(value);
		wrapper.appendChild(text);
		return wrapper;
	}
	
	/**
	 * Removes the text blocks created via makeText();
	 * @method
	 * @private
	 * @param _parent { HTML Element }
	 * 		The element to remove the text blocks from.
	 */
	this.destroyText = function(_parent) {
		var sp = null;
		while (sp = _parent.getElementsByTagName('span')[0]) {
			sp = _parent.removeChild (sp);
		}
	}
}