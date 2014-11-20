/**
@fileOverview jQuery plugins written specifically for Boom.
*/
/**
boom.plugins.js
@author Hoop Associates
*/

/**
@namespace
@name $.fn
*/

(function($) {

	/**
	@function
	*/
	$.fn.ui = function(opts){

		opts = $.extend({
			tree: $.boom.config.tree,
			sortable: $.boom.config.sortable,
		}, opts);

		$('.b-button').button($.boom.config.button);
		$('.boom-tabs').tabs();
		$('.boom-datepicker').datetimepicker($.boom.config.datepicker);

		var elems = '.boom-tree';

		$.boom.log('Start bind UI events');

		this.find(elems).each(function(){

			var elem = $(this), types = [];

			$.each(this.className.split(' '), function(){

				if (/boom/.test(this)) {

					types.push(this.replace(/boom-/, ''));
				}
			});

			$.each(types, function(){
				switch(this.toString()) {
					case 'tree' :
						elem.tree($.extend({}, opts.tree, {
							border: !(/tree-noborder/).test(elem[0].className)
						}));
					break;
				};
			});
		});

		$.boom.log('Stop bind UI events');

		return this;
	};
})( jQuery );