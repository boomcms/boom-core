/*
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk
*/
var 
buildNav = function(elem){
	var span, li;
	$("#" + elem + " ul li").each(
	function(){
		if ($("ul:first", this).length) {
			var 
				ch = $("ul:first", this).css("display") != 'none' ? '&ndash;' : '+', 
				li = this;
			$("a:first", li).prepend('<span class="exp">' + ch + '&nbsp;</span>');
			$('span:first', this).click(function(){
				span = this;
				$("ul:first", li).animate({
					height: 'toggle'
				}, 200, function(){
					$(span).html(($(this).css("display") == "none") ? "+&nbsp;" : "&ndash;&nbsp;");
				});
				return false;
			});
		}
	});
},
fixBugs = function(){
	// FF2/Mac Opacity Bug
	($.browser.mozilla && parseFloat($.browser.version) < 1.9 && navigator.appVersion.indexOf('Mac') !== -1) && $('body').css('-moz-opacity',.999);
	if ($.browser.msie) {
		// IE6 background css flickering bug
		try{document.execCommand('BackgroundImageCache', false, true);} 
		catch(e){};
		// IE6 transaprent PNG background fix
		if (/MSIE ((5\.5)|6)/i.test(navigator.userAgent) && navigator.platform == "Win32"){
			$(".iecsspng").each(function(){
				var bgIMG = $(this).css('background-image');
				if (bgIMG.indexOf(".png")!=-1){
					var iebg = bgIMG.split('url("')[1].split('")')[0];
					$(this).css('background-image', 'none');
					$(this).get(0).runtimeStyle.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + iebg + "',sizingMethod='scale')";
				}
			});
		}
	}
};
String.prototype.text = function(){
	var str = this;
	try {
		str = decodeURIComponent(str);
        } catch(e) {}
	str = (str != null && str != undefined && str.length) ? $.trim(str.replace(/<\S[^><]*>/g, '')) : '';
	return str;
}
String.prototype.safeEscape = function(){
        return encodeURIComponent($.trim(this)).replace(/%20/g, "+").toString();
}

$(function(){
	fixBugs();
	buildNav("nav");   
});
