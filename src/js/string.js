/**
@function
*/
String.prototype.ucfirst = function() {
	return this.substr(0, 1).toUpperCase() + this.substr(1, this.length);
};

/**
@function
*/
String.prototype.toInt = function() {
	return parseInt(this, 10);
};