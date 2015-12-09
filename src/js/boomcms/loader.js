$.widget('boom.boomLoader', {
	_create : function(){
		var img = new Image();
		img.src = '/vendor/boomcms/boom-core/img/ajax_load.gif';

		this.loader = $('<div id="b-loader"></div>').appendTo($(top.document).find('body'));
		this._bind_loader_to_global_ajax_events();
	},

	_bind_loader_to_global_ajax_events : function() {
		var loader = this;

		$(this.document)
			.bind("ajaxSend", function(){
				loader.show();
			 })
			.bind("ajaxComplete", function(){
				loader.hide();
			 });
	},

	show : function() {
		this.loader.show();
	},

	hide : function() {
		this.loader.hide();
	}
});