/* 
 * Boom history class.
 * Eventuall replacement to history.js
 *
 * Uses the JS History API where available
 */

function boomHistory() {
	boomHistory.prototype.isSupported = function() {
		return (typeof top.window.history === 'object');
	};

	boomHistory.prototype.replaceState = function(object, title, url) {
		if (this.isSupported()) {
			top.window.history.replaceState(object, title, url);
		}
	};
}