$.widget('boom.boomLoader', {
	loaders : 0,

	_create : function(){
		var img = new Image();
		img.src = '/media/boom/img/ajax_load.gif';

		this.loader = $('<div id="b-loader"></div>').appendTo($(top.document).find('body'));
	},

	show : function() {
		this.loaders ++;

		this.loader.show();

		return this.loaders;
	},

	hide : function(force) {
		force = (force == undefined) ? false : true;

		if (force) this.loaders = 0;

		if (this.loaders > 0) this.loaders --;

		if (this.loaders === 0) {
			this.loader.hide();
		}

		return this;
	}
});