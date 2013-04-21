/**
* CLASS
* 	LayoutProvider
* DESCRIPTION
* 	Private class that provides layout building blocks for the
* 	application UI.
* @class
* @private
*/
function LayoutProvider () {

	/**
	* Turns a certain HTML element into a CSS box.
	* @method
	* @public
	* @param target { HTML Element }
	* 		The HTML element that is to be set up as a CSS box. This 
	* 		implies both out-of-page-flow(1) positioning and fixed 
	* 		dimensions(2).
	* @param oPoint { Object }
	* 		An object literal that specifies the box's boundaries. Use:
	* 		- x: The horizontal position of top left corner.
	* 		- y: The vertical position of top left corner.
	* 		- w: The width of the box.
	* 		- h: The height of the box.
	* 		All are optional. Not defining one of the above members will
	* 		unset the corresponding CSS property.
	* 		Note:
	* 		(1) Out-of-page-flow positioning translates to 'fixed' if 
	*          the target element is a direct child of the body 
	*          element; it translates to 'absolute' otherwise.
	* 		(2) All values are computed as ems.
	*/
	this.setupBox = function(target, oPoint) {
		var isTopLevel = target.parentNode.nodeName
			.toLowerCase() == 'body';
		CSSProvider.setStyle (target, 'position', 
			isTopLevel? "fixed": "absolute");
		CSSProvider.setStyle (target, 'left', 
			oPoint && oPoint.x? (oPoint.x + "em") : '');
		CSSProvider.setStyle (target, 'top', 
			oPoint && oPoint.y? (oPoint.y + "em") : '');
		CSSProvider.setStyle (target, 'width', 
			oPoint && oPoint.w? (oPoint.w + "em") : '');
		CSSProvider.setStyle (target, 'height', 
			oPoint && oPoint.h? (oPoint.h + "em") : '');
	}
	
	/**
	 * Centers a certain CSS box inside its parent.
	 * @method
	 * @private
	 * @param target { HTML Element }
	 * 		The HTML element (already set up as a box) that is to be 
	 * 		centered.
	 * @param oPoint { Object }
	 * 		An object literal that describes an optional offset from the 
	 * 		computed 'center' position. Use:
	 * 		- x: a positive value will move the box right.
	 * 		- y: a positive value will move to box down.
	 * Note:
	 * All values are computed as ems.
	 */
	this.setupCentered = function(target, oPoint) {
		var w = parseFloat(target.style.width);
		var h = parseFloat(target.style.height);
		var xOff = oPoint && oPoint.x? parseFloat(oPoint.x) : 0;
		var yOff = oPoint && oPoint.y? parseFloat(oPoint.y) : 0;
		CSSProvider.setStyle(target, 'left', '50%');
		CSSProvider.setStyle(target, 'top', '50%');
		CSSProvider.setStyle(target, 'marginLeft', -1*(w/2-xOff)+'em');
		CSSProvider.setStyle(target, 'marginTop',  -1*(h/2-yOff)+'em');
	}
	
	/**
	 * Makes a certain CSS box stretch.
	 * @method
	 * @private
	 * @param target { HTML Element }
	 * 		The HTML element (already set up as a box) that has to 
	 * 		stretch.
	 * @param oPoint { Object }
	 * 		An object literal that defines one to four anchor points. 
	 * 		The box will stretch, the way that its boundaries stay 
	 * 		aligned to each defined anchor point, respectivelly.
	 * 		Example:
	 * 		oPoint = { bottom: 1.5, top: 0 }
	 * 		The box's bottom boundary will be anchored at 1.5 em away 
	 * 		from the parent-box's bottom boundary; also the top boundary
	 * 		of the box will be anchored at the parent-box's top 
	 * 		boundary. As the parent box resizes, the box resizes with
	 * 		it, while keeping the given anchors.
	 */
	this.setupStretched = function(target, oPoint) {
		var topA = oPoint && oPoint.top?
			parseFloat(oPoint.top) : 0;
		var rightA = oPoint && oPoint.right?
			parseFloat(oPoint.right) : 0;
		var bottomA = oPoint && oPoint.bottom?
			parseFloat(oPoint.bottom) : 0;
		var leftA = oPoint && oPoint.left?
			parseFloat(oPoint.left) : 0;
		if(topA >= 0) {
			CSSProvider.setStyle(target, 'top', topA+ 'em');
		}
		if(rightA >= 0) {
			CSSProvider.setStyle(target, 'right', rightA+ 'em');
		}
		if(bottomA >= 0) {
			CSSProvider.setStyle(target, 'bottom', bottomA+ 'em');
		}
		if(leftA >= 0) {
			CSSProvider.setStyle(target, 'left', leftA+ 'em');
		}
	}



	/**
	* CLASS
	* 		CSSProvider
	* DESCRIPTION
	* 		Private class that provides CSS styling services for the 
	* 		application.
	* @class
	* @private
	* @param oDocument { Object }
	* 		The document object to provide CSS for.
	*/
	CSSProvider = {};
	
	/**
	 * Sets the provided style on an HTML element.
	 * @field
	 * @public
	 * @static
	 * @param target {HTML Element}
	 * 		An HTML Element to set CSS style on.
	 * @param property (String)
	 * 		The name of the CSS property to be set
	 * @param value {String}
	 * 		The new value to set
	 */
	CSSProvider.setStyle = function (target, property, value) {
		target.style[property] = String(value);
	}
	
	/**
	 * Unsets a style property on an HTML element.
	 * @field
	 * @public
	 * @static
	 * @see CSSProvider.setStyle
	 */
	CSSProvider.clearStyle = function(target, property) {
		CSSProvider.setStyle(target, property, '');
	}
}