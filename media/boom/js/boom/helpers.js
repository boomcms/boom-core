/**
@fileOverview Helper functions
*/
/**
convert 8 bit characters to their 7 bit equivalent
@function
*/
String.prototype.safeEscape = function(){
	var str = $.trim(this),
	replacements = {
		"\xa0": " ", "\xb7": "*", "\u2018": "'", "\u2019": "'",	"\u2026": "...", "\u2002": " ", "\u2003": " ", "\u2009": " ", "\u2012": "-", "\u2013": "-", "\u2014": "-", "\u2015": "-",
		"\u201c": '"',	// smart quote
		"\u201d": '"'	// smart quote
	};

	for (key in replacements)
		str = str.replace(new RegExp(key, 'g'), replacements[key]);

	return encodeURIComponent(str).replace(/%20/g, '+').toString();
};

/**
@function
*/
String.prototype.ucfirst = function() {
	return this.substr(0, 1).toUpperCase() + this.substr(1, this.length);
};

/**
@function
*/
String.prototype.toInt = function(){
	return parseInt( this, 10 );
};
