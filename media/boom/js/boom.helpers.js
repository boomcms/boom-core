/**
@function
*/
String.prototype.cleanup = function(node){

	var html = this;

	// remove jquery junk
	$.removeData(html);

	html = html
	.replace(/ jquery\d+="\d+"/ig, '') // remove jquery junk
	.replace(/<a href="[a-zA-Z]{4,5}\W{3}[^\/]+\/replace_[0-9]+">([^<]+)<\/a>/, "$1") // remove placeholder anchor elements

	// fix broken rel attributes #fuckuinternetexplorer
	var matches = html.match(/rel=(.*?)[\b>]/g);
	if (matches && matches.length && /"(.*?)"/.test(matches[0])) {
		// we're going to assume that all other rel attributes are all good
	} else {
		// we're going to assume that all other rel attributes are broken
		html = html.replace(/rel=(.*?)([\b>])/g, 'rel="$1"$2');
	}
	
	return html;
};

// String.prototype.trim = function(chars){
// 	var txt;
// 	
// 	txt = $.trim(this).replace(/&nbsp;/ig, ' ');
// 	if (chars) {
// 		while (txt.substr(txt.length-chars.length, chars.length).toLowerCase() == chars.toLowerCase()) { // rtrim
// 			txt = $.trim(txt.substr(0, (txt.length - chars.length)));
// 		}
// 		while(txt.substr(0, chars.length).toLowerCase() == chars.toLowerCase()) { // ltrim
// 			txt = $.trim(txt.substr(chars.length));
// 		}
// 	}
// 	return $.trim(txt);
// };

/**
@function
*/
String.prototype.addslashes = function(){
	var str = this;
	str = str.replace(/\'/g,'\\\'');
	str = str.replace(/\"/g,'\\"');
	str = str.replace(/\\/g,'\\\\');
	str = str.replace(/\0/g,'\\0');
	return str;
};

/**
@function
*/
String.prototype.stripslashes = function(){
	var str = this;
	str = str.replace(/\\'/g,'\'');
	str = str.replace(/\\"/g,'"');
	str = str.replace(/\\\\/g,'\\');
	str = str.replace(/\\0/g,'\0');
	return str;
};

/**
convert html characters to their HTML entities
@function
*/
String.prototype.encodeHTML = function(){
	return this
	.replace(/\r\n/g, "<br>")
	.replace(/\n/g, "<br>")
	.replace(/\r/g, "<br>")
	.replace(/<br(\s)?\/?>/g, "<br>")
	.replace(/(<br>){2,}\s*?(<br>){2,}/g, '<br><br>')
	.replace(/</g, "&lt;")
	.replace(/>/g, "&gt;");
};

/**
strip all tags, return plain text
@function
*/
String.prototype.text = function(){
	var str = this;
	try { str = decodeURIComponent(str); } catch(e) {}
	str = str && str.length ? $.trim(str.replace(/<\S[^><]*>/g, '')) : '';
	return str;
};

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
	return parseInt(this);
};
